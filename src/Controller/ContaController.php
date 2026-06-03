<?php
namespace App\Controller;
 
use App\Auth;
use App\Model\ClienteModel;
use App\Model\ReservaModel; // a criar na FT05
 
class ContaController {
    public function ver(): void {
        Auth::verificar(); // redireciona para /login se não autenticado
        $cliente  = (new ClienteModel())->getById($_SESSION['cliente_id']);
        $titulo   = 'A minha conta';
        require '../templates/conta/ver.php';
    }
}
