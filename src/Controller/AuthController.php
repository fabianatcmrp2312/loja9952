<?php
namespace App\Controller;

use App\Model\ClienteModel;

class AuthController {
    private ClienteModel $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->model = new ClienteModel();
    }

    public function registar(): void {
        $erros = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validar();
            $nome     = trim($_POST['nome'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $tel      = trim($_POST['telefone'] ?? '');
            $pass     = $_POST['password'] ?? '';
            $pass2    = $_POST['password2'] ?? '';

            if (strlen($nome) < 3) $erros[] = 'Nome demasiado curto (mínimo 3 caracteres).';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'Email inválido.';
            if (strlen($pass) < 8) $erros[] = 'A password deve ter pelo menos 8 caracteres.';
            if ($pass !== $pass2) $erros[] = 'As passwords não coincidem.';
            if ($this->model->emailExiste($email)) $erros[] = 'Este email já está registado.';

            if (empty($erros)) {
                $this->model->criar([
                    ':nome'     => $nome,
                    ':email'    => $email,
                    ':telefone' => $tel ?: null,
                    ':password' => password_hash($pass, PASSWORD_BCRYPT),
                ]);
                $_SESSION['msg_ok'] = 'Conta criada com sucesso! Faz login.';
                header('Location: ' . $this->baseUrl() . '/login');
                exit;
            }
        }
        $titulo = 'Criar conta';
        require __DIR__ . '/../../templates/auth/registar.php';
    }

    public function login(): void {
        $erro = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validar();
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';

            $this->garantirContaClienteDemo();

            $cliente = $this->model->getByEmail($email);
            $fallbackEmail = trim((string) ($_ENV['CLIENT_EMAIL'] ?? ''));
            $fallbackPass  = (string) ($_ENV['CLIENT_PASSWORD'] ?? '');
            $fallbackHash  = (string) ($_ENV['CLIENT_PASSWORD_HASH'] ?? '');
            $fallbackOk = false;

            if ($fallbackEmail !== '' && hash_equals(mb_strtolower($fallbackEmail), mb_strtolower($email))) {
                if ($fallbackHash !== '') {
                    $fallbackOk = password_verify($pass, $fallbackHash);
                } elseif ($fallbackPass !== '') {
                    $fallbackOk = hash_equals($fallbackPass, $pass);
                }
            }

            if (($cliente && password_verify($pass, $cliente['password'])) || $fallbackOk) {
                if (!$cliente) {
                    $cliente = [
                        'id' => 0,
                        'nome' => (string) ($_ENV['CLIENT_NAME'] ?? 'Cliente'),
                        'email' => $email,
                    ];
                }
                session_regenerate_id(true);
                $_SESSION['logado'] = true;
                $_SESSION['cliente_id'] = $cliente['id'];
                $_SESSION['cliente_nome'] = $cliente['nome'];
                $_SESSION['cliente_email'] = $cliente['email'];
                $redirect = $_SESSION['redirect_after_login'] ?? ($this->baseUrl() . '/');
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            }
            $erro = 'Email ou password incorretos. Se for uma conta de teste, confirma as credenciais no .env.';
        }
        $titulo = 'Iniciar sessao';
        require __DIR__ . '/../../templates/auth/login.php';
    }

    public function logout(): void {
        session_destroy();
        session_unset();
        header('Location: ' . $this->baseUrl() . '/');
        exit;
    }

    private function baseUrl(): string {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        return preg_replace('#/public$#', '', $base) ?: '';
    }

    public function adminLogin(): void {
        $erro = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validar();
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';

            $this->garantirContaAdmin();

            $admin = null;
            $passwordOk = false;

            try {
                $pdo  = \App\Database::getConnection();
                $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = :email LIMIT 1');
                $stmt->execute([':email' => $email]);
                $admin = $stmt->fetch() ?: null;
            } catch (\Throwable $e) {
                $admin = null;
            }

            if ($admin) {
                $stored = (string) ($admin['password'] ?? '');
                $passwordOk = password_verify($pass, $stored) || hash_equals($stored, $pass);
            } else {
                $fallbackEmail = trim((string) ($_ENV['ADMIN_EMAIL'] ?? ''));
                $fallbackPass  = (string) ($_ENV['ADMIN_PASSWORD'] ?? '');
                $fallbackHash  = (string) ($_ENV['ADMIN_PASSWORD_HASH'] ?? '');

                if ($fallbackEmail !== '' && hash_equals(mb_strtolower($fallbackEmail), mb_strtolower($email))) {
                    if ($fallbackHash !== '') {
                        $passwordOk = password_verify($pass, $fallbackHash);
                    } elseif ($fallbackPass !== '') {
                        $passwordOk = hash_equals($fallbackPass, $pass);
                    }
                }
            }

            if (($admin || $passwordOk) && $passwordOk) {
                session_regenerate_id(true);
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_id']     = $admin['id'] ?? 0;
                $_SESSION['admin_nome']   = $admin['nome'] ?? 'Administrador';
                header('Location: ' . $this->baseUrl() . '/admin');
                exit;
            }

            $erro = $admin === null
                ? 'Conta de administrador não encontrada. Verifica a configuração do acesso admin.'
                : 'Credenciais inválidas.';
        }
        $titulo = 'Administração - Login';
        require __DIR__ . '/../../templates/admin/login.php';
    }

    private function garantirContaAdmin(): void {
        $email = trim((string) ($_ENV['ADMIN_EMAIL'] ?? 'joao@autoshop.pt'));
        $password = (string) ($_ENV['ADMIN_PASSWORD'] ?? 'AutoShop123!');

        if ($email === '' || $password === '') {
            return;
        }

        try {
            $pdo = \App\Database::getConnection();
            $pdo->exec(
                'CREATE TABLE IF NOT EXISTS admins (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome VARCHAR(120) NOT NULL,
                    email VARCHAR(190) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
            );

            $stmt = $pdo->prepare('SELECT id FROM admins WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            if (!$stmt->fetch()) {
                $insert = $pdo->prepare('INSERT INTO admins (nome, email, password) VALUES (:nome, :email, :password)');
                $insert->execute([
                    ':nome' => 'Administrador',
                    ':email' => $email,
                    ':password' => password_hash($password, PASSWORD_BCRYPT),
                ]);
            }
        } catch (\Throwable $e) {
        }
    }

    private function garantirContaClienteDemo(): void {
        $email = trim((string) ($_ENV['CLIENT_EMAIL'] ?? ''));
        $password = (string) ($_ENV['CLIENT_PASSWORD'] ?? '');
        $nome = trim((string) ($_ENV['CLIENT_NAME'] ?? ''));

        if ($email === '' || $password === '') {
            return;
        }

        try {
            if (!$this->model->getByEmail($email)) {
                $this->model->criar([
                    ':nome' => $nome !== '' ? $nome : 'Cliente',
                    ':email' => $email,
                    ':telefone' => null,
                    ':password' => password_hash($password, PASSWORD_BCRYPT),
                ]);
            }
        } catch (\Throwable $e) {
        }
    }

    public static function verificarAdmin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!($_SESSION['admin_logado'] ?? false)) {
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
            $base = preg_replace('#/public$#', '', $base) ?: '';
            header('Location: ' . $base . '/admin/login');
            exit;
        }
    }
}
