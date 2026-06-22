<?php
namespace App\Controller;

use App\Model\VeiculoModel;

class AdminController {
    private function baseUrl(): string {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        return preg_replace('#/public$#', '', $base) ?: '';
    }

    private function auth(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!($_SESSION['admin_logado'] ?? false)) {
            header('Location: ' . $this->baseUrl() . '/admin/login');
            exit;
        }
    }

    public function dashboard(): void {
        $this->auth();
        $pdo = \App\Database::getConnection();
        $totalVeic = $pdo->query('SELECT COUNT(*) FROM veiculos')->fetchColumn();
        $totalRes = $pdo->query('SELECT COUNT(*) FROM reservas')->fetchColumn();
        $pendentes = $pdo->query('SELECT COUNT(*) FROM reservas WHERE estado="pendente"')->fetchColumn();
        $titulo = 'Dashboard - AutoShop Admin';
        require __DIR__ . '/../../templates/admin/dashboard.php';
    }

    public function veiculosLista(): void {
        $this->auth();
        $pdo = \App\Database::getConnection();
        $veiculos = $pdo->query(
            'SELECT v.*, m.nome AS marca FROM veiculos v
             JOIN marcas m ON m.id = v.marca_id
             ORDER BY v.id DESC'
        )->fetchAll();
        $titulo = 'Gerir Veiculos';
        require __DIR__ . '/../../templates/admin/veiculos.php';
    }

    public function veiculoCriar(): void {
        $this->auth();
        $model = new VeiculoModel();
        $marcas = $model->getMarcas();
        $veiculo = [];
        $erros = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validar();
            $dados = $this->validarVeiculo($_POST, $erros);
            $imagem = $this->processarImagem($erros);
            if ($imagem) {
                $dados[':imagem'] = $imagem;
            }

            if (empty($erros)) {
                $model->criar($dados);
                $_SESSION['msg_ok'] = 'Veiculo adicionado!';
                header('Location: ' . $this->baseUrl() . '/admin/veiculos');
                exit;
            }
        }

        $titulo = 'Adicionar Veiculo';
        require __DIR__ . '/../../templates/admin/veiculo_form.php';
    }

    public function veiculoEditar(int $id): void {
        $this->auth();
        $model = new VeiculoModel();
        $veiculo = $model->getById($id);
        if (!$veiculo) {
            http_response_code(404);
            echo 'Veiculo nao encontrado.';
            return;
        }

        $marcas = $model->getMarcas();
        $erros = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validar();
            $dados = $this->validarVeiculo($_POST, $erros);
            $imagem = $this->processarImagem($erros);
            if ($imagem) {
                $dados[':imagem'] = $imagem;
            } elseif (!empty($veiculo['imagem'])) {
                $dados[':imagem'] = $veiculo['imagem'];
            }

            if (empty($erros)) {
                $model->atualizar($id, $dados);
                $_SESSION['msg_ok'] = 'Veiculo atualizado!';
                header('Location: ' . $this->baseUrl() . '/admin/veiculos');
                exit;
            }
        }

        $titulo = 'Editar Veiculo';
        require __DIR__ . '/../../templates/admin/veiculo_form.php';
    }

    public function veiculoApagar(int $id): void {
        $this->auth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->baseUrl() . '/admin/veiculos');
            exit;
        }
        csrf_validar();
        if ($id > 0) {
            (new VeiculoModel())->apagar($id);
            $_SESSION['msg_ok'] = 'Veiculo apagado!';
        }
        header('Location: ' . $this->baseUrl() . '/admin/veiculos');
        exit;
    }

    private function validarVeiculo(array $post, array &$erros): array {
        $dados = [];
        if (empty($post['marca_id'])) $erros[] = 'Seleciona uma marca.';
        if (empty($post['modelo'])) $erros[] = 'Modelo obrigatorio.';
        if (empty($post['ano'])) $erros[] = 'Ano obrigatorio.';
        if (!is_numeric($post['preco']) || $post['preco'] <= 0) {
            $erros[] = 'Preco invalido.';
        }

        if (empty($erros)) {
            $dados = [
                ':marca_id' => (int) $post['marca_id'],
                ':modelo' => trim($post['modelo']),
                ':ano' => (int) $post['ano'],
                ':quilometros' => (int) ($post['quilometros'] ?? 0),
                ':combustivel' => $post['combustivel'],
                ':cilindrada' => trim($post['cilindrada'] ?? '') ?: null,
                ':preco' => (float) $post['preco'],
                ':descricao' => trim($post['descricao'] ?? ''),
                ':imagem' => null,
            ];
        }

        return $dados;
    }

    private function processarImagem(array &$erros): ?string {
        if (empty($_FILES['imagem']['name'])) return null;
        $f = $_FILES['imagem'];
        $tipos = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $tipo = $finfo->file($f['tmp_name']);
        if (!in_array($tipo, $tipos, true)) {
            $erros[] = 'Imagem invalida (so JPG/PNG/WEBP).';
            return null;
        }
        if ($f['size'] > 5 * 1024 * 1024) {
            $erros[] = 'Imagem demasiado grande (max. 5MB).';
            return null;
        }
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        $nome = uniqid('veiculo_', true) . '.' . strtolower($ext);
        $dest = __DIR__ . '/../../public/uploads/' . $nome;
        if (!move_uploaded_file($f['tmp_name'], $dest)) {
            $erros[] = 'Erro ao guardar imagem.';
            return null;
        }
        $this->redimensionar($dest, $dest, 800, 600);
        return $nome;
    }

    private function redimensionar(string $src, string $dst, int $mw, int $mh): void {
        [$w, $h, $t] = getimagesize($src);
        $r = min($mw / $w, $mh / $h);
        $nw = (int) ($w * $r);
        $nh = (int) ($h * $r);
        $im = match ($t) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($src),
            IMAGETYPE_PNG => imagecreatefrompng($src),
            IMAGETYPE_WEBP => imagecreatefromwebp($src),
            default => null,
        };
        if (!$im) return;
        $nd = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($nd, $im, 0, 0, 0, 0, $nw, $nh, $w, $h);
        match ($t) {
            IMAGETYPE_JPEG => imagejpeg($nd, $dst, 85),
            IMAGETYPE_PNG => imagepng($nd, $dst, 6),
            IMAGETYPE_WEBP => imagewebp($nd, $dst, 85),
            default => null,
        };
        imagedestroy($im);
        imagedestroy($nd);
    }

    public function reservasLista(): void {
        $this->auth();
        $pdo = \App\Database::getConnection();
        $reservas = $pdo->query(
            'SELECT r.*, c.nome AS cliente, c.email, c.telefone,
                    v.modelo, v.ano, m.nome AS marca, v.preco
             FROM reservas r
             JOIN clientes c ON c.id = r.cliente_id
             JOIN veiculos v ON v.id = r.veiculo_id
             JOIN marcas m ON m.id = v.marca_id
             ORDER BY r.criado_em DESC'
        )->fetchAll();
        $titulo = 'Gerir Reservas';
        require __DIR__ . '/../../templates/admin/reservas.php';
    }

    public function reservaEstado(): void {
        $this->auth();
        csrf_validar();
        $id = (int) ($_POST['reserva_id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        $validos = ['pendente', 'confirmada', 'cancelada'];
        if ($id > 0 && in_array($estado, $validos, true)) {
            $pdo = \App\Database::getConnection();
            $stmt = $pdo->prepare('SELECT veiculo_id FROM reservas WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $reserva = $stmt->fetch();

            if ($reserva) {
                $pdo->beginTransaction();
                try {
                    $stmt = $pdo->prepare('UPDATE reservas SET estado = :e WHERE id = :id');
                    $stmt->execute([':e' => $estado, ':id' => $id]);

                    $disponivel = $estado === 'cancelada' ? 1 : 0;
                    $stmt2 = $pdo->prepare('UPDATE veiculos SET disponivel = :d WHERE id = :vid');
                    $stmt2->execute([
                        ':d' => $disponivel,
                        ':vid' => (int) $reserva['veiculo_id'],
                    ]);

                    $pdo->commit();
                } catch (\Throwable $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    throw $e;
                }
            }
        }
        header('Location: ' . $this->baseUrl() . '/admin/reservas');
        exit;
    }
}
