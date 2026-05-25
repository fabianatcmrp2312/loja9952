<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (is_file(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env ?: [] as $k => $v) {
        $_ENV[$k] = $v;
    }
}

use App\Controller\VeiculoController;

$basePath = trim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
$uri      = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = trim(substr($uri, strlen($basePath)), '/');
}

$partes  = explode('/', $uri);
$recurso = $partes[0] ?? '';
$acao    = $partes[1] ?? '';
$id      = (int) ($partes[2] ?? 0);

$ctrl = new VeiculoController();

match ("$recurso/$acao") {
    '/' => $ctrl->catalogo(),
    'veiculo/detalhe' => $ctrl->detalhe($id),
    default => $ctrl->catalogo(),
};
