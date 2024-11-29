<?php
namespace models;

use db\Conexao;
use PDO;

class Produto {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::conectar();
    }

    public function inserir($nome, $descricao, $preco, $quantidade_estoque, $id_categoria, $id_fornecedor) {
        $sql = "INSERT INTO produto (nome, descricao, preco, quantidade_estoque, id_categoria, id_fornecedor)
                VALUES (:nome, :descricao, :preco, :quantidade_estoque, :id_categoria, :id_fornecedor)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':quantidade_estoque', $quantidade_estoque);
        $stmt->bindParam(':id_categoria', $id_categoria);
        $stmt->bindParam(':id_fornecedor', $id_fornecedor);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function atualizarEstoque($id_produto, $novo_estoque) {
        $sql = "UPDATE produto SET quantidade_estoque = :novo_estoque WHERE id = :id_produto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':novo_estoque', $novo_estoque);
        $stmt->bindParam(':id_produto', $id_produto);
        $stmt->execute();
    }

    public function buscarPorNome($nome) {
        $sql = "SELECT * FROM produto WHERE nome = :nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM produto WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
