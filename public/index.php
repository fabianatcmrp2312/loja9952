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
use App\Controller\AdminController;

$basePath = trim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
$uri      = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = trim(substr($uri, strlen($basePath)), '/');
}

$partes  = explode('/', $uri);
$recurso = $partes[0] ?? '';
$acao    = $partes[1] ?? '';
$id      = (int) ($partes[2] ?? 0);
$id2     = (int) ($partes[3] ?? 0);

if ($recurso === '' && $acao === '') {
    (new VeiculoController())->catalogo();
} elseif ($recurso === 'veiculo' && $acao === 'detalhe') {
    (new VeiculoController())->detalhe($id);
} elseif ($recurso === 'carrinho' && $acao === '') {
    (new CarrinhoController())->ver();
} elseif ($recurso === 'carrinho' && $acao === 'adicionar') {
    (new CarrinhoController())->adicionar();
} elseif ($recurso === 'carrinho' && $acao === 'remover') {
    (new CarrinhoController())->remover();
} elseif ($recurso === 'checkout' && $acao === '') {
    (new CheckoutController())->ver();
} elseif ($recurso === 'checkout' && $acao === 'confirmar') {
    (new CheckoutController())->confirmar();
} elseif ($recurso === 'login' && $acao === '') {
    (new AuthController())->login();
} elseif ($recurso === 'registar' && $acao === '') {
    (new AuthController())->registar();
} elseif ($recurso === 'logout' && $acao === '') {
    (new AuthController())->logout();
} elseif ($recurso === 'conta' && $acao === '') {
    (new ContaController())->ver();
} elseif ($recurso === 'admin' && $acao === '') {
    (new AdminController())->dashboard();
} elseif ($recurso === 'admin' && $acao === 'login') {
    (new AuthController())->adminLogin();
} elseif ($recurso === 'admin' && $acao === 'veiculos' && empty($partes[2])) {
    (new AdminController())->veiculosLista();
} elseif ($recurso === 'admin' && $acao === 'veiculos' && ($partes[2] ?? '') === 'novo') {
    (new AdminController())->veiculoCriar();
} elseif ($recurso === 'admin' && $acao === 'veiculos' && ($partes[2] ?? '') === 'criar') {
    (new AdminController())->veiculoCriar();
} elseif ($recurso === 'admin' && $acao === 'veiculos' && ($partes[2] ?? '') === 'editar') {
    (new AdminController())->veiculoEditar($id2);
} elseif ($recurso === 'admin' && $acao === 'veiculos' && ($partes[2] ?? '') === 'apagar') {
    (new AdminController())->veiculoApagar($id2);
} elseif ($recurso === 'admin' && $acao === 'reservas' && empty($partes[2])) {
    (new AdminController())->reservasLista();
} elseif ($recurso === 'admin' && $acao === 'reservas' && ($partes[2] ?? '') === 'estado') {
    (new AdminController())->reservaEstado();
} else {
    (new VeiculoController())->catalogo();
}
