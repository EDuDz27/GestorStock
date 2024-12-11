<?php
namespace models;

use db\Conexao;
use PDO;

class Fornecedor {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::conectar();
    }

    public function inserir($nome, $contato, $endereco) {
        $sql = "INSERT INTO fornecedor (nome, contato, endereco) 
                VALUES (:nome, :contato, :endereco)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':contato', $contato);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function buscarTodos() {
        $sql = "SELECT * FROM fornecedor";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM fornecedor WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
