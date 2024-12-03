<?php
require_once 'db/Conexao.php';
require_once '/models/Produto.php';
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

$method = $_SERVER['REQUEST_METHOD'];
$headers = getallheaders();

$produto = new Produto();
$ordem = new Ordem();

// Método POST - (Registrar movimentação - Entrada/Saida)
if ($method == 'POST') {
    // Se o conteúdo for JSON continua
    if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'application/json') !== false) {
        // Receber dados via JSON
        $data = json_decode(file_get_contents('php://input'), true);
        $nome = $data['nome'] ?? null;
        $descricao = $data['descricao'] ?? null; // Descrição é opcional
        $preco = $data['preco'] ?? null;
        $quantidade = $data['quantidade'] ?? null;
        $tipo = isset($data['tipo']) ? (int)$data['tipo'] : null; // Convertendo para inteiro
        $id_categoria = $data['categoria'] ?? null;
        $id_fornecedor = $data['fornecedor'] ?? null;
        $id_cliente = $data['cliente'] ?? null;

        // Verificações de campos obrigatórios
        if (!$nome || !$preco || !$quantidade || !isset($tipo) || !$id_categoria) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['error' => 'Campos obrigatórios estão faltando.']);
            exit();
        }

        $data_ordem = date('Y-m-d');

        // Validações
        if ($tipo === 0 && !$id_fornecedor) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['error' => 'Fornecedor é obrigatório para entrada.']);
            exit();
        }

        if ($tipo === 1 && !$id_cliente) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['error' => 'Cliente é obrigatório para saída.']);
            exit();
        }

        // Verificar se o produto já existe
        $produto_existente = $produto->buscarPorNome($nome);

        if ($produto_existente) {
            // Produto já existe, atualizar o estoque com base na movimentação
            $id_produto = $produto_existente['id'];
            $quantidade_estoque = $produto_existente['quantidade_estoque'];

            if ($tipo === 0) { // Entrada
                $novo_estoque = $quantidade_estoque + $quantidade;
            } elseif ($tipo === 1) { // Saída
                if ($quantidade_estoque >= $quantidade) {
                    $novo_estoque = $quantidade_estoque - $quantidade;
                } else {
                    header("Content-Type: application/json; charset=UTF-8");
                    echo json_encode(['error' => 'Quantidade insuficiente no estoque.']);
                    exit();
                }
            }

            // Atualizar o estoque do produto
            $produto->atualizarEstoque($id_produto, $novo_estoque);
        } else {
            // Produto não existe, inserir novo produto
            $id_produto = $produto->inserir($nome, $descricao, $preco, $quantidade, $id_categoria, $id_fornecedor);
            $tipo = 0;
            $novo_estoque = $quantidade; // A quantidade inicial será a inserida
        }

        // Calcular o valor da ordem
        $valor = $preco * $quantidade;

        // Inserir a ordem
        $id_ordem = $ordem->inserir($tipo, $data_ordem, $id_fornecedor, $id_cliente, $valor);

        // Registrar a movimentação
        $movimentacao = new Movimentacao();
        $movimentacao->registrarMovimentacao($id_produto, $tipo, $quantidade, $id_ordem);

        // Resposta de sucesso
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(['message' => 'Movimentação registrada com sucesso!', 'estoque_atual' => $novo_estoque]);
        exit();
    } else {
        // Retornar erro se não for um JSON
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(['error' => 'Formato de conteúdo não suportado.']);
        exit();
    }
}

// Método GET - Ler (Buscar produtos)
if ($method == 'GET') {
    // Verificar se um ID específico foi passado na URL
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Buscar um produto específico pelo ID
        $produto_encontrado = $produto->buscarPorId($id);
        if ($produto_encontrado) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode($produto_encontrado);
        } else {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['error' => 'Produto não encontrado.']);
        }
    } else {
        // Buscar todos os produtos
        $produtos = $produto->buscarTodos();
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($produtos);
    }
    exit();
}

// Método PUT - Atualizar (Modificar um produto)
if ($method == 'PUT') {
    // Se o conteúdo for JSON continua
    if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'application/json') !== false) {
        // Receber dados via JSON
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $nome = $data['nome'] ?? null;
        $descricao = $data['descricao'] ?? null; // Descrição é opcional
        $preco = $data['preco'] ?? null;
        $quantidade = $data['quantidade'] ?? null;
        $id_categoria = $data['categoria'] ?? null;

        if (!$id || !$nome || !$preco || !$quantidade || !$id_categoria) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['error' => 'Campos obrigatórios estão faltando.']);
            exit();
        }

        // Atualizar o produto
        $produto_atualizado = $produto->atualizar($id, $nome, $descricao, $preco, $quantidade, $id_categoria);
        if ($produto_atualizado) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['message' => 'Produto atualizado com sucesso.']);
        } else {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['error' => 'Falha ao atualizar o produto.']);
        }
        exit();
    } else {
        // Retornar erro se não for um JSON
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(['error' => 'Formato de conteúdo não suportado.']);
        exit();
    }
}

// Método DELETE - Deletar (Remover um produto)
if ($method == 'DELETE') {
    // Se o conteúdo for JSON continua
    if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'application/json') !== false) {
        // Receber dados via JSON
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['error' => 'ID do produto é obrigatório.']);
            exit();
        }

        // Deletar o produto
        $produto_deletado = $produto->deletar($id);
        if ($produto_deletado) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['message' => 'Produto deletado com sucesso.']);
        } else {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(['error' => 'Falha ao deletar o produto.']);
        }
        exit();
    } else {
        // Retornar erro se não for um JSON
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(['error' => 'Formato de conteúdo não suportado.']);
        exit();
    }
}

// Se o método não for suportado
header("Content-Type: application/json; charset=UTF-8");
echo json_encode(['error' => 'Método não suportado.']);
exit();
?>
