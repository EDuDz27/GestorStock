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
}
