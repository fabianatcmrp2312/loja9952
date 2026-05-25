<?php

namespace App\Controller;

use App\Model\VeiculoModel;

class VeiculoController
{
    private VeiculoModel $model;

    public function __construct()
    {
        $this->model = new VeiculoModel();
    }

    public function catalogo(): void
    {
        $filtros = [
            'marca_id' => (int) ($_GET['marca_id'] ?? 0) ?: null,
            'combustivel' => $_GET['combustivel'] ?? null,
            'preco_max' => (float) ($_GET['preco_max'] ?? 0) ?: null,
            'ano_min' => (int) ($_GET['ano_min'] ?? 0) ?: null,
            'pesquisa' => trim($_GET['pesquisa'] ?? ''),
        ];

        $filtros = array_filter($filtros);

        $veiculos = $this->model->listar($filtros);
        $marcas = $this->model->getMarcas();
        $titulo = 'Catalogo de Veiculos';

        require __DIR__ . '/../../templates/veiculos/catalogo.php';
    }

    public function detalhe(int $id): void
    {
        $veiculo = $this->model->getById($id);

        if (!$veiculo) {
            http_response_code(404);
            echo 'Veiculo nao encontrado.';
            return;
        }

        $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');

        echo '<h1>' . htmlspecialchars($veiculo['marca'] . ' ' . $veiculo['modelo']) . '</h1>';
        echo '<p>Preco: ' . number_format((float) $veiculo['preco'], 2, ',', '.') . ' EUR</p>';
        echo '<p><a href="' . htmlspecialchars($baseUrl . '/') . '">Voltar</a></p>';
    }
}
