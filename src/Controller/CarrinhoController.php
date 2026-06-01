<?php

namespace App\Controller;

use App\Model\VeiculoModel;

class CarrinhoController
{
    private VeiculoModel $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->model = new VeiculoModel();
    }

    public function ver(): void
    {
        $ids = $_SESSION['carrinho'] ?? [];
        $veiculos = array_map(fn($id) => $this->model->getById((int) $id), $ids);
        $veiculos = array_filter($veiculos);
        $titulo = 'A minha lista de reservas';

        require __DIR__ . '/../../templates/carrinho/ver.php';
    }

    public function adicionar(): void
    {
        csrf_validar();

        $id = (int) ($_POST['veiculo_id'] ?? 0);

        if ($id > 0) {
            $carrinho = $_SESSION['carrinho'] ?? [];

            if (!in_array($id, $carrinho, true)) {
                $carrinho[] = $id;
                $_SESSION['carrinho'] = $carrinho;
                $_SESSION['msg_ok'] = 'Veiculo adicionado a lista.';
            } else {
                $_SESSION['msg_info'] = 'Este veiculo ja esta na tua lista.';
            }
        }

        $this->redirectCarrinho();
    }

    public function remover(): void
    {
        csrf_validar();

        $id = (int) ($_POST['veiculo_id'] ?? 0);
        $carrinho = $_SESSION['carrinho'] ?? [];
        $carrinho = array_values(array_filter($carrinho, fn($i) => (int) $i !== $id));
        $_SESSION['carrinho'] = $carrinho;

        $this->redirectCarrinho();
    }

    private function redirectCarrinho(): void
    {
        $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');

        header('Location: ' . $baseUrl . '/carrinho');
        exit;
    }
}
