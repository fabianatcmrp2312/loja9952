<?php
namespace App\Controller;

use App\Auth;
use App\Model\ReservaModel;
use App\Model\VeiculoModel;

class CheckoutController {
    public function ver(): void {
        Auth::verificar();
        if (session_status() === PHP_SESSION_NONE) session_start();

        $ids = $_SESSION['carrinho'] ?? [];
        if (empty($ids)) {
            header('Location: ' . $this->baseUrl() . '/carrinho');
            exit;
        }

        $model = new VeiculoModel();
        $veiculos = array_filter(array_map(fn($id) => $model->getById((int) $id), $ids));
        $titulo = 'Confirmar reserva';
        require __DIR__ . '/../../templates/checkout/ver.php';
    }

    public function confirmar(): void {
        Auth::verificar();
        csrf_validar();

        $ids = $_SESSION['carrinho'] ?? [];
        $clienteId = (int) ($_SESSION['cliente_id'] ?? 0);
        $mensagem = trim($_POST['mensagem'] ?? '');

        $reservaModel = new ReservaModel();
        $confirmadas = 0;

        foreach ($ids as $veiculoId) {
            try {
                $reservaModel->criar($clienteId, (int) $veiculoId, $mensagem);
                $confirmadas++;
            } catch (\Throwable $e) {
                error_log('Erro ao criar reserva: ' . $e->getMessage());
            }
        }

        $_SESSION['carrinho'] = [];

        if ($confirmadas > 0) {
            $_SESSION['msg_ok'] = "$confirmadas reserva(s) confirmada(s)! Veiculo(s) marcado(s) como vendido(s). A nossa equipa entrara em contacto.";
        } else {
            $_SESSION['msg_info'] = 'Nao foi possivel confirmar a reserva. O veiculo pode ja estar vendido.';
        }

        header('Location: ' . $this->baseUrl() . '/conta');
        exit;
    }

    private function baseUrl(): string {
        return rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    }
}
