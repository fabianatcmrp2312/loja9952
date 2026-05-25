<?php
namespace App\Model;
 
use App\Database;
use PDO;
 
class VeiculoModel {
    private PDO $db;
 
    public function __construct() {
        $this->db = Database::getConnection();
    }
 
    // Listar com filtros opcionais
    public function listar(array $filtros = []): array {
        $sql    = 'SELECT v.*, m.nome AS marca FROM veiculos v
                   JOIN marcas m ON m.id = v.marca_id
                   WHERE v.disponivel = 1';
        $params = [];
 
        if (!empty($filtros['marca_id'])) {
            $sql .= ' AND v.marca_id = :marca_id';
            $params[':marca_id'] = $filtros['marca_id'];
        }
        if (!empty($filtros['combustivel'])) {
            $sql .= ' AND v.combustivel = :combustivel';
            $params[':combustivel'] = $filtros['combustivel'];
        }
        if (!empty($filtros['preco_max'])) {
            $sql .= ' AND v.preco <= :preco_max';
            $params[':preco_max'] = $filtros['preco_max'];
        }
        if (!empty($filtros['ano_min'])) {
            $sql .= ' AND v.ano >= :ano_min';
            $params[':ano_min'] = $filtros['ano_min'];
        }
        if (!empty($filtros['pesquisa'])) {
            $sql .= ' AND (v.modelo LIKE :p OR m.nome LIKE :p2)';
            $params[':p']  = '%'.$filtros['pesquisa'].'%';
            $params[':p2'] = '%'.$filtros['pesquisa'].'%';
        }
 
        $sql .= ' ORDER BY v.criado_em DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
 
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            'SELECT v.*, m.nome AS marca FROM veiculos v
             JOIN marcas m ON m.id = v.marca_id WHERE v.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
 
    public function getMarcas(): array {
        return $this->db->query('SELECT * FROM marcas ORDER BY nome')->fetchAll();
    }
}
