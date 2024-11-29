Para o Método POST:
"localhost/GestorStock/index.php"
Envie uma requisição POST com o seguinte corpo JSON:

{
    "nome": "Produto Teste",
    "descricao": "Descrição do produto teste.",
    "preco": 10.50,
    "quantidade": 5,
    "tipo": 0,
    "categoria": null,
    "fornecedor": null,
    "cliente": null
}

tipo 0 - Entrada
tipo 1 - Saida

Categorias:
1 - Eletronicos
2 - Roupas
3 - Movéis
4 - Alimentos
5 - Cosméticos

Fornecedores disponiveis do 1 ao 5 (1 - NENHUM), caso seja operação de entrada fornecedor é obrigatorio e cliente opcional

Clientes disponiveis do 1 ao 5 (1 - NENHUM), caso seja operação de saida cliente é obrigatorio e fornecedor opcional



---------------------------------------------------------------------------------------------------------------------------------------------------

Para o Método GET:
Para buscar um produto específico, envie uma requisição GET para "localhost/GestorStock/index.php?id=1"
Para buscar todos os produtos, envie uma requisição GET para "localhost/GestorStock/index.php"


---------------------------------------------------------------------------------------------------------------------------------------------------

Para o Método PUT:
"localhost/GestorStock/index.php"
Envie uma requisição PUT com o seguinte corpo JSON:

{
    "id": 1,
    "nome": "Produto Atualizado",
    "descricao": "Descrição atualizada.",
    "preco": 15.00,
    "quantidade": 10,
    "categoria": 1
}

As alterações seram realizadas em relação ao ID especificado, ID correspondente aos buscados no metodo GET
Veja o Metodo POST para mais informações


---------------------------------------------------------------------------------------------------------------------------------------------------

Para o Método DELETE:
"localhost/GestorStock/index.php"
Envie uma requisição DELETE com o seguinte corpo JSON:

{
    "id": 1
}

ID correspondente aos buscados no metodo GET