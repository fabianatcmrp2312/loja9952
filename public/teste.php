<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Model\Produto;
 
$p = new Produto('Caneta Azul', 0.99, 150);
echo $p->descricao();
