<?php
namespace App;
 
class Auth {
    public static function verificar(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!($_SESSION['logado'] ?? false)) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
            header('Location: ' . $baseUrl . '/login');
            exit;
        }
    }
 
    public static function clienteAtual(): array {
        return [
            'id'   => $_SESSION['cliente_id']   ?? null,
            'nome' => $_SESSION['cliente_nome'] ?? 'Cliente',
        ];
    }
}
