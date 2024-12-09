<?php
require_once 'db\Conexao.php';
require_once 'models\Categoria.php';
require_once 'models\Fornecedor.php';
require_once 'models\Produto.php';

use models\Categoria;
use models\Fornecedor;
use models\Produto;


$categoria = new Categoria();
$fornecedor = new Fornecedor();
$produto = new Produto();

$categorias = $categoria->buscarTodos();
$fornecedores = $fornecedor->buscarTodos();
$produtos = $produto->buscarTodos();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestorStock</title>
    <link rel="stylesheet" href="./css/style.css">

</head>

<body>
    <main>
        <nav>
            <a href="javascript:void(0);" onclick="showForm(1)">Adicionar</a>
            <a href="javascript:void(0);" onclick="showForm(2)">Pesquisar</a>
            <a href="javascript:void(0);" onclick="showForm(3)">Alterar</a>
            <a href="javascript:void(0);" onclick="showForm(4)">Remover</a>
        </nav>
        <div id="form1" class="container">
            <form action="processar.php" method="post">
                <div class="campos">
                    <div class="entrada">
                        <label for="nome">Produto</label>
                        <input list="produtos" type="text" name="nome" required>
                        <datalist id="produtos">
                        <?php foreach ($produtos as $produto): ?>
                            <option value="<?php echo htmlspecialchars($produto['nome']); ?>">
                        <?php endforeach; ?>
                        </datalist>
                    </div>

                    <span></span>

                    <div class="entrada">
                        <label for="categoria">Categoria</label>
                        <select name="categoria" id="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= htmlspecialchars($categoria['id']) ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="entrada">
                        <label for="tipo">Tipo Movimentação</label>
                        <select name="tipo" id="tipo" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="0 - Entrada">Entrada</option>
                            <option value="1 - Saida">Saída</option>
                        </select>
                    </div>

                    <div class="entrada">
                        <label for="fornecedor">Fornecedor</label>
                        <select name="fornecedor" id="fornecedor" required>
                            <option value="">Selecione um fornecedor</option>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <option value="<?= htmlspecialchars($fornecedor['id']) ?>"><?= htmlspecialchars($fornecedor['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="entrada">
                        <label for="descricao">Descrição</label>
                        <input type="text" name="descricao">
                    </div>

                    <div class="entrada">
                        <label for="valor">Valor Unidade</label>
                        <input type="number" name="valor" required>
                    </div>

                    <div class="entrada">
                        <label for="quantidade">Quantidade</label>
                        <input type="text" name="quantidade" required>
                    </div>
                    
                </div>
                <input class="submit" type="submit" value="Enviar Produto">
            </form>
        </div>

        <div id="form2" class="container">
            <form action="processar.php" method="get">
                <div class="campos">
                    <div class="pesquisa">
                        <label for="nome">Pesquisar Produto</label>
                        <input type="text" name="nome" required>
                    </div>
                    <div id="resultados" class="resultados-container">
                        <!-- LISTA AQUI -->
                    </div>
                </div>
                <input class="submit" type="submit" value="Pesquisar">
            </form>
        </div>

        <div id="form3" class="container">
            <form action="processar.php" method="alter">
                <div class="campos">
                    <div class="entrada">
                        <label for="nome">Produto</label>
                        <input type="text" name="nome" required>
                    </div>
                    <div class="entrada">
                        <label for="categoria">Categoria</label>
                        <input type="text" name="categoria" required>
                    </div>
                    <div class="entrada">
                        <label for="valor">Valor Unidade</label>
                        <input type="text" name="valor" required>
                    </div>
                    <div class="entrada">
                        <label for="quantidade">Quantidade</label>
                        <input type="text" name="quantidade" required>
                    </div>
                    <div class="entrada">
                        <label for="descricao">Descrição</label>
                        <input type="text" name="descricao">
                    </div>
                </div>
                <input class="submit" type="submit" value="Enviar Produto">
            </form>
        </div>

        <div id="form4" class="container">
            <form action="processar.php" method="delete">
                <div class="campos">
                <div class="entrada">
                        <label for="nome">Produto</label>
                        <input list="produtos" type="text" name="nome" required>
                        <datalist id="produtos">
                        <?php foreach ($produtos as $produto): ?>
                            <option value="<?php echo htmlspecialchars($produto['nome']); ?>">
                        <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="entrada">
                        <label for="categoria">Categoria</label>
                        <select name="categoria" id="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= htmlspecialchars($categoria['id']) ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <input class="submit" type="submit" value="Remover">
            </form>
        </div>
    </main>
    <script src="./js/script.js"></script>
</body>

</html>