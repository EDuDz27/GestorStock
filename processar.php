<?php
require_once 'db/Conexao.php';
require_once 'models/Produto.php';
require_once 'models/Movimentacao.php';
require_once 'models/Categoria.php';
require_once 'models/Fornecedor.php';
require_once 'models/Ordem.php';

use models\Produto;
use models\Movimentacao;
use models\Categoria;
use models\Fornecedor;
use models\Ordem;

$method = $_SERVER['REQUEST_METHOD'];
$headers = getallheaders();

$produto = new Produto();
$ordem = new Ordem();

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

// Método POST - (Registrar movimentação - Entrada/Saida)
if ($method == 'POST') {
    if ($_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded') {
        $nome = $_POST['nome'] ?? null;
        $descricao = $_POST['descricao'] ?? null; // Descrição é opcional
        $valor = $_POST['valor'] ?? null;
        $quantidade = $_POST['quantidade'] ?? null;
        $tipo = $_POST['tipo'] ?? null; // "0 - Entrada" ou "1 - Saída"
        $id_categoria = $_POST['categoria'] ?? null;
        $id_fornecedor = $_POST['fornecedor'] ?? null;

        // Campos obrigatórios
        if (!$nome || !$valor || !$quantidade || !$tipo || !$id_categoria) {
            echo json_encode(['error' => 'Campos obrigatórios estão faltando.']);
            exit();
        }

        // Validação do tipo
        if ($tipo !== '0 - Entrada' && $tipo !== '1 - Saida') {
            echo json_encode(['error' => 'Tipo inválido. O tipo deve ser "0 - Entrada" ou "1 - Saída".']);
            exit();
        }

        $data_ordem = date('Y-m-d');

        // Validação de fornecedor para tipo "0 - Entrada"
        if ($tipo === '0 - Entrada' && !$id_fornecedor) {
            echo json_encode(['error' => 'Fornecedor é obrigatório para entrada.']);
            exit();
        }

        // Verificar se o produto já existe
        $produto_existente = $produto->buscarPorNome($nome);

        if ($produto_existente) {
            // Produto já existe, atualizar o estoque com base na movimentação
            $id_produto = $produto_existente['id'];
            $quantidade_estoque = $produto_existente['quantidade_estoque'];

            if ($tipo === '0 - Entrada') {
                $tipo = 0;
                $novo_estoque = $quantidade_estoque + $quantidade;

            } elseif ($tipo === '1 - Saida') {
                $tipo = 1;
                if ($quantidade_estoque >= $quantidade) {
                    $novo_estoque = $quantidade_estoque - $quantidade;

                } else {
                    echo json_encode(['error' => 'Quantidade insuficiente no estoque.']);
                    exit();
                }
            }

            $produto->atualizarEstoque($id_produto, $novo_estoque);
            // Atualizar o estoque do produto
        } else {
            // Produto não existe, inserir novo produto
            $id_produto = $produto->inserir($nome, $descricao, $valor, $quantidade, $id_categoria, $id_fornecedor);
            $tipo = 0;  // Considera a entrada como padrão, mas pode ser ajustado
            $novo_estoque = $quantidade; // A quantidade inicial será a inserida
        }

        // Calcular o valor da ordem
        $valor = $valor * $quantidade;

        // Inserir a ordem
        $id_ordem = $ordem->inserir($tipo, $data_ordem, $id_fornecedor, $valor);

        // Registrar a movimentação
        $movimentacao = new Movimentacao();
        $movimentacao->registrarMovimentacao($id_produto, $tipo, $quantidade, $id_ordem);

        // Resposta de sucesso
        echo json_encode(['message' => 'Movimentação registrada com sucesso!', 'estoque_atual' => $novo_estoque]);
        exit();
    } else {
        // Retornar erro se o tipo de conteúdo não for x-www-form-urlencoded
        echo json_encode(['error' => 'Formato de conteúdo não suportado.']);
        exit();
    }
}

// Método GET - Ler (Buscar produtos)
if ($method == 'GET') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $movimentacao = new Movimentacao();
        $ordem = new Ordem();
        $fornecedor = new Fornecedor();
        $produtoQuantidade = $produto->estoqueAtual($id);
        $movimentacoes = $movimentacao->buscarMovimentacoesPorProduto($id);

        echo "<link rel='stylesheet' href='./css/pesquisar-style.css'>" . 
                "<h4>Quantidade atual em estoque: " . $produtoQuantidade['quantidade_estoque'] . "</h4>";

        if (!empty($movimentacoes)) {
            echo "<table class='tabela-resultados'>" .
                    "<thead>" .
                        "<tr>" .
                        "<th>Data</th>" .
                        "<th>Tipo</th>" .
                        "<th>Quantidade</th>" .
                        "<th>Fornecedor</th>" .
                        "<th>Valor Total</th>" .
                        "</tr>" .
                        "</thead>" .
                    "<tbody>";

            foreach ($movimentacoes as $mov) {
                $ordemInfo = $ordem->buscarPorId($mov['id_ordem']);
                $fornecedorInfo = $fornecedor->buscarPorId($ordemInfo['id_fornecedor']);

                echo "<tr>" .
                    "<td>" . htmlspecialchars($mov['data_mov']) . "</td>" .
                    "<td>" . htmlspecialchars($mov['tipo']) . "</td>" .
                    "<td>" . htmlspecialchars($mov['quantidade']) . "</td>" .
                    "<td>" . htmlspecialchars($fornecedorInfo['nome']) . "</td>" .
                    "<td>" . htmlspecialchars($ordemInfo['valor']) . "</td>" .
                    "</tr>";
            }

            echo "</tbody>" .
                "</table>";

        } else {
            echo "<p>Nenhuma movimentação encontrada para o produto.</p>";
        }
    } else {
        echo "<p>Produto não encontrado.</p>";
    }
    exit();

}

// Método PUT - Atualizar (Modificar um produto)
if ($method == 'PUT') {
    if ($_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded') {
        $id = $_POST['id'] ?? null;
        $nome = $_POST['novo-nome'] ?? null;
        $descricao = $_POST['descricao'] ?? null;
        $valor = $_POST['valor'] ?? null;
        $quantidade = $_POST['quantidade'] ?? null;
        $id_categoria = $_POST['categoria'] ?? null;
    
        // Verificar se o id foi fornecido
        if (!$id) {
            echo json_encode(['error' => 'ID do produto não fornecido.']);
            exit();
        }
    
        // Resgatar os dados atuais do produto para garantir que os valores não fornecidos sejam mantidos
        $produto_atual = $produto->buscarPorId($id);
        
        if (!$produto_atual) {
            echo json_encode(['error' => 'Produto não encontrado.']);
            exit();
        }
    
        // Backup dos dados originais
        $nome_original = $produto_atual['nome'];
        $descricao_original = $produto_atual['descricao'];
        $valor_original = $produto_atual['preco'];
        $quantidade_original = $produto_atual['quantidade_estoque'];
        $id_categoria_original = $produto_atual['id_categoria'];
    
        // Validar e manter as informações originais caso os campos sejam vazios
        $nome = ($nome !== null && $nome !== '') ? $nome : $nome_original;
        $descricao = ($descricao !== null && $descricao !== '') ? $descricao : $descricao_original;
        $valor = ($valor !== null && $valor !== '') ? $valor : $valor_original;
        $quantidade = ($quantidade !== null && $quantidade !== '') ? $quantidade : $quantidade_original;
        $id_categoria = ($id_categoria !== null && $id_categoria !== '') ? $id_categoria : $id_categoria_original;
    
        // Atualizar o produto com os dados fornecidos (com os valores atualizados)
        $produto_atualizado = $produto->atualizar($id, $nome, $descricao, $valor, $quantidade, $id_categoria);
        if ($produto_atualizado) {
            echo json_encode(['message' => 'Produto atualizado com sucesso.']);
        } else {
            echo json_encode(['error' => 'Falha ao atualizar o produto.']);
        }
        exit();
    } else {
        echo json_encode(['error' => 'Formato de conteúdo não suportado.']);
        exit();
    }
    
}

// Método DELETE - Deletar (Remover um produto)
if ($method == 'DELETE') {
    if ($_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded') {
        $id = $_POST['id'] ?? null;

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