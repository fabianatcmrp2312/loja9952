<?php
namespace App;
 
class Auth {
    public static function verificar(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!($_SESSION['logado'] ?? false)) {
            header('Location: /login');
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
