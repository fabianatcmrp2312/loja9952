<?php
namespace App\Controller;

use App\Auth;
use App\Model\ClienteModel;
use App\Model\ReservaModel;

class ContaController {
    public function ver(): void {
        Auth::verificar();

        $cliente = (new ClienteModel())->getById((int) $_SESSION['cliente_id']);
        $reservas = (new ReservaModel())->getByCliente((int) $_SESSION['cliente_id']);
        $titulo = 'A minha conta';

        require __DIR__ . '/../../templates/conta/ver.php';
   
    }
}
