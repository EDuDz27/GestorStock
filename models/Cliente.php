<?php
namespace models;

use db\Conexao;
use PDO;

class Cliente {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::conectar();
    }

    public function inserir($nome, $contato, $endereco) {
        $sql = "INSERT INTO cliente (nome, contato, endereco)
                VALUES (:nome, :contato, :endereco)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':contato', $contato);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function buscaPorId($id){
        $sql = "SELECT * FROM produto WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount()>0){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        else {
            throw new Exception("Sem registro de Clientes!");
        }
    }

    public function buscarTodos() {
        $sql = "SELECT * FROM cliente";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
