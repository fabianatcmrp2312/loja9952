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
            $nome     = trim($_POST['nome']     ?? '');
            $email    = trim($_POST['email']    ?? '');
            $tel      = trim($_POST['telefone'] ?? '');
            $pass     = $_POST['password']      ?? '';
            $pass2    = $_POST['password2']     ?? '';
 
            if (strlen($nome) < 3)              $erros[] = 'Nome demasiado curto (mínimo 3 caracteres).';
            if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $erros[] = 'Email inválido.';
            if (strlen($pass) < 8)              $erros[] = 'A password deve ter pelo menos 8 caracteres.';
            if ($pass !== $pass2)               $erros[] = 'As passwords não coincidem.';
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
            $email = trim($_POST['email']    ?? '');
            $pass  = $_POST['password'] ?? '';
            $cliente = $this->model->getByEmail($email);
            if ($cliente && password_verify($pass, $cliente['password'])) {
                session_regenerate_id(true);
                $_SESSION['logado']      = true;
                $_SESSION['cliente_id']  = $cliente['id'];
                $_SESSION['cliente_nome']= $cliente['nome'];
                header('Location: ' . $this->baseUrl() . '/');
                exit;
            }
            $erro = 'Email ou password incorretos.';
        }
        $titulo = 'Iniciar sessão';
        require __DIR__ . '/../../templates/auth/login.php';
    }
 
    public function logout(): void {
        session_destroy();
        session_unset();
        header('Location: ' . $this->baseUrl() . '/');
        exit;
    }

    private function baseUrl(): string {
        return rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    }
}
