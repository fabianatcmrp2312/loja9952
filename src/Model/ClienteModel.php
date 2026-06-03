<?php
namespace App\Model;
 
use App\Database;
use PDO;
 
class ClienteModel {
    private PDO $db;
 
    public function __construct() {
        $this->db = Database::getConnection();
    }
 
    public function criar(array $dados): int {
        $stmt = $this->db->prepare(
            'INSERT INTO clientes (nome, email, telefone, password)
             VALUES (:nome, :email, :telefone, :password)'
        );
        $stmt->execute($dados);
        return (int) $this->db->lastInsertId();
    }
 
    public function getByEmail(string $email): array|false {
        $stmt = $this->db->prepare(
            'SELECT * FROM clientes WHERE email = :email'
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
 
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare('SELECT * FROM clientes WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
 
    public function emailExiste(string $email): bool {
        $stmt = $this->db->prepare('SELECT id FROM clientes WHERE email = :email');
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetch();
    }
}
