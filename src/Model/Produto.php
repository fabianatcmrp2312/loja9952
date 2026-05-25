<?php

namespace App\Model;

class Produto
{
    public function __construct(
        public string $nome,
        public float $preco,
        public int $stock = 0
    ) {
    }

    public function descricao(): string
    {
        return "{$this->nome} - {$this->preco} EUR (stock: {$this->stock})";
    }
}
