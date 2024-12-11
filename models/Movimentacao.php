<?php
namespace models;

use db\Conexao;
use PDO;

class Movimentacao {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::conectar();
    }

    public function registrarMovimentacao($id_produto, $tipo, $quantidade, $id_ordem) {
        $sql = "INSERT INTO movimentacao (id_produto, tipo, quantidade, data_mov, id_ordem) 
                VALUES (:id_produto, :tipo, :quantidade, NOW(), :id_ordem)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_produto', $id_produto);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':id_ordem', $id_ordem);
        $stmt->execute();
    }

    public function buscarMovimentacoesPorProduto($id_produto) {
        $sql = "SELECT * FROM movimentacao WHERE id_produto = :id_produto ORDER BY data_mov DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_produto', $id_produto);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


