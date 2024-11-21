<?php
namespace models;

use db\Conexao;
use PDO;

class Categoria {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::conectar();
    }

    public function inserir($nome, $descricao) {
        $sql = "INSERT INTO categoria (nome, descricao) 
                VALUES (:nome, :descricao)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function buscarTodos() {
        $sql = "SELECT * FROM categoria";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
