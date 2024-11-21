<?php
require_once 'db/Conexao.php';
require_once 'models/Produto.php';
require_once 'models/Movimentacao.php';
require_once 'models/Categoria.php';
require_once 'models/Fornecedor.php';
require_once 'models/Ordem.php';
require_once 'models/Cliente.php';

use models\Produto;
use models\Movimentacao;
use models\Categoria;
use models\Fornecedor;
use models\Ordem;
use models\Cliente;

$categoria = new Categoria();
$fornecedor = new Fornecedor();
$cliente = new Cliente();
$ordem = new Ordem();

$categorias = $categoria->buscarTodos();
$fornecedores = $fornecedor->buscarTodos();
$clientes = $cliente->buscarTodos();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'] ?? null; // Descrição é opcional
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];
    $tipo = $_POST['tipo'];
    $id_categoria = $_POST['categoria'] ?: null; // Categoria pode ser nula
    $id_fornecedor = $_POST['fornecedor'] ?: null; // Fornecedor pode ser nulo
    $id_cliente = $_POST['cliente'] ?: null; // Cliente pode ser nulo
    $data_ordem = date('Y-m-d');

    if ($tipo == 0 && !$id_fornecedor) {
        echo "Erro: Fornecedor é obrigatório para entrada.";
        exit();
    }

    if ($tipo == 1 && !$id_cliente) {
        echo "Erro: Cliente é obrigatório para saída.";
        exit();
    }

    $produto = new Produto();
    $movimentacao = new Movimentacao();

    // Verificar se o produto já existe
    $produto_existente = $produto->buscarPorNome($nome);

    if ($produto_existente) {
        // Produto já existe, atualizar o estoque com base na movimentação
        $id_produto = $produto_existente['id'];
        $quantidade_estoque = $produto_existente['quantidade_estoque'];

        if ($tipo == 0) { // Entrada
            $novo_estoque = $quantidade_estoque + $quantidade;
        } elseif ($tipo == 1) { // Saída
            if ($quantidade_estoque >= $quantidade) {
                $novo_estoque = $quantidade_estoque - $quantidade;
            } else {
                echo "Erro: Quantidade insuficiente no estoque.";
                exit();
            }
        }

        // Atualizar o estoque do produto
        $produto->atualizarEstoque($id_produto, $novo_estoque);
    } else {
        // Produto não existe, inserir novo produto
        $id_produto = $produto->inserir($nome, $descricao, $preco, $quantidade, $id_categoria, $id_fornecedor);
        $novo_estoque = $quantidade; // A quantidade inicial será a inserida
    }

    // Calcular o valor da ordem
    $valor = $preco * $quantidade;

    // Inserir a ordem
    $id_ordem = $ordem->inserir($tipo, $data_ordem, $id_fornecedor, $id_cliente, $valor);

    // Registrar a movimentação
    $movimentacao->registrarMovimentacao($id_produto, $tipo, $quantidade, $id_ordem);

    echo "Movimentação registrada com sucesso! Estoque atualizado para $novo_estoque unidades.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Estoque</title>
</head>
<body>
    <h2>Adicionar ou Remover Produto do Estoque</h2>
    
    <form action="" method="POST">
        <label for="nome">Nome do Produto:</label><br>
        <input type="text" id="nome" name="nome" required><br><br>
        
        <label for="descricao">Descrição (opcional):</label><br>
        <input type="text" id="descricao" name="descricao"><br><br>

        <label for="preco">Preço Unidade:</label><br>
        <input type="number" id="preco" name="preco" step="0.01" required><br><br>

        <label for="quantidade">Quantidade:</label><br>
        <input type="number" id="quantidade" name="quantidade" required><br><br>

        <label for="tipo">Tipo de Movimentação:</label><br>
        <select id="tipo" name="tipo" required>
            <option value="0">Entrada</option>
            <option value="1">Saída</option>
        </select><br><br>

        <label for="categoria">Categoria:</label><br>
        <select id="categoria" name="categoria">
            <option value="">--</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id'] ?>"><?= $categoria['nome'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="fornecedor">Fornecedor:</label><br>
        <select id="fornecedor" name="fornecedor">
            <option value="">--</option>
            <?php foreach ($fornecedores as $fornecedor): ?>
                <option value="<?= $fornecedor['id'] ?>"><?= $fornecedor['nome'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="cliente">Cliente:</label><br>
        <select id="cliente" name="cliente">
            <option value="">--</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id'] ?>"><?= $cliente['nome'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="submit" name="submit" value="Registrar Movimentação">
    </form>
</body>
</html>
