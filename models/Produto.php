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

    public function buscarTodos() {
        $sql = "SELECT * FROM produto";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarEstoque($id_produto, $novo_estoque) {
        $sql = "UPDATE produto SET quantidade_estoque = :novo_estoque WHERE id = :id_produto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':novo_estoque', $novo_estoque);
        $stmt->bindParam(':id_produto', $id_produto);
        $stmt->execute();
    }

    public function atualizar($id, $nome, $descricao, $preco, $quantidade, $id_categoria) {
        $sql = "UPDATE produto SET nome = ?, descricao = ?, preco = ?, quantidade_estoque = ?, id_categoria = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nome, $descricao, $preco, $quantidade, $id_categoria, $id]);
    }

    public function deletar($id) {
        try { 
            $this->pdo->beginTransaction();

            // Buscar os IDs das ordens relacionadas ao produto via movimentações
            $stmt = $this->pdo->prepare("SELECT DISTINCT id_ordem FROM movimentacao WHERE id_produto = :id_produto");
            $stmt->bindParam(':id_produto', $id);
            $stmt->execute();
            
            $ordensRelacionadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!$ordensRelacionadas) {
                $ordensRelacionadas = [];
            }
    
            // Deletar as movimentações relacionadas ao produto
            $stmt = $this->pdo->prepare("DELETE FROM movimentacao WHERE id_produto = :id_produto");
            $stmt->bindParam(':id_produto', $id);
            $stmt->execute();
    
            // Deletar as ordens relacionadas, usando os IDs armazenados
            if (!empty($ordensRelacionadas)) {
                // Usamos o IN para deletar todas as ordens cujos IDs estão no array $ordensRelacionadas
                $inQuery = implode(',', array_fill(0, count($ordensRelacionadas), '?'));
                $stmt = $this->pdo->prepare("DELETE FROM ordem WHERE id IN ($inQuery)");
                $stmt->execute($ordensRelacionadas);
            }
    
            // Deletar o produto
            $stmt = $this->pdo->prepare("DELETE FROM produto WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $this->pdo->commit();
            return true; // Retornar sucesso

        } catch (PDOException $e) {
            // Em caso de erro, reverter a transação
            $this->pdo->rollBack();
            throw new Exception("Erro ao deletar: " . $e->getMessage());
        }
    }
    
    
    
    
}
