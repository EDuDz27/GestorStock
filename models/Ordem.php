<?php
namespace models;

use db\Conexao;
use PDO;

class Ordem
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexao::conectar();
    }

    public function inserir($tipo, $data_ordem, $id_fornecedor, $valor)
    {
        $sql = "INSERT INTO ordem (tipo, data_ordem, id_fornecedor, valor)
                VALUES (:tipo, :data_ordem, :id_fornecedor, :valor)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':data_ordem', $data_ordem);
        $stmt->bindParam(':id_fornecedor', $id_fornecedor);
        $stmt->bindParam(':valor', $valor);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function buscarOrdemPorProduto($id_produto) {
        $sql = "SELECT DISTINCT id_ordem FROM movimentacao WHERE id_produto = :id_produto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_produto', $id_produto);
        $stmt->execute();
        
        $ordensRelacionadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!$ordensRelacionadas) {
            $ordensRelacionadas = [];
        }

        // Deletar as ordens relacionadas, usando os IDs armazenados
        if (!empty($ordensRelacionadas)) {
            // Usamos o IN para deletar todas as ordens cujos IDs estão no array $ordensRelacionadas
            $inQuery = implode(',', array_fill(0, count($ordensRelacionadas), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM ordem WHERE id IN ($inQuery)");
            $stmt->execute($ordensRelacionadas);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }



    }
}
