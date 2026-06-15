<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_validar(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Token CSRF invalido.');
    }
}

if (is_file(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env ?: [] as $k => $v) {
        $_ENV[$k] = $v;
    }
}

use App\Controller\VeiculoController;
use App\Controller\CarrinhoController;
use App\Controller\AuthController;
use App\Controller\ContaController;
use App\Controller\CheckoutController;

$basePath = trim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
$uri      = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = trim(substr($uri, strlen($basePath)), '/');
}

$partes  = explode('/', $uri);
$recurso = $partes[0] ?? '';
$acao    = $partes[1] ?? '';
$id      = (int) ($partes[2] ?? 0);

match ("$recurso/$acao") {
    '/' => (new VeiculoController())->catalogo(),
    'veiculo/detalhe' => (new VeiculoController())->detalhe($id),
    'carrinho/' => (new CarrinhoController())->ver(),
    'carrinho/adicionar' => (new CarrinhoController())->adicionar(),
    'carrinho/remover' => (new CarrinhoController())->remover(),
    'checkout/' => (new CheckoutController())->ver(),
    'checkout/confirmar' => (new CheckoutController())->confirmar(),
    'login/' => (new AuthController())->login(),
    'registar/' => (new AuthController())->registar(),
    'logout/' => (new AuthController())->logout(),
    'conta/' => (new ContaController())->ver(),
    default => (new VeiculoController())->catalogo(),
    'admin/'              => (new AdminController())->dashboard(),
    'admin/login'         => (new AuthController())->adminLogin(),
    'admin/veiculos'      => (new AdminController())->veiculosLista(),
    'admin/veiculos/criar'=> (new AdminController())->veiculoCriar(),
    'admin/reservas'      => (new AdminController())->reservasLista(),
    'admin/reservas/estado'=> (new AdminController())->reservaEstado(),

};
