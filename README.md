# Avalicação Técnica Jedis
Olá senhor(a) avaliador, Gabriel Teixeira aqui, aqui está o resultado do teste técnico que me foi enviado via e-mail em 24/01/2024. O teste consistia na criação de uma aplicação Laravel que utiliza o Laravel Passport e possui CRUDs de Usuários e Produtos. Quero adiantar desde já que todas as prerrogativas presentes no documento que me foi enviado foram cumpridas, inclusive a utilização das seguintes tecnologias:

<a href="https://laravel.com/">
    <img src="https://shields.io/badge/Laravel-10.0v-blue.svg?logo=laravel" alt="Laravel 10.0" />
</a>
<a href="https://docker.com">
    <img src="https://shields.io/badge/Docker-24.0v-blue.svg?logo=docker" alt="Docker 24.0" />
</a>
<a href="https://www.postgresql.org/">
    <img src="https://shields.io/badge/Postgres-15.0v-blue.svg?logo=postgresql" alt="Postgres 15.0" />
</a>
<a href="https://redis.io/">
    <img src="https://shields.io/badge/Redis-7.2v-blue.svg?logo=redis" alt="Redis 7.2" />
</a>
<a href="https://nginx.org/">
    <img src="https://shields.io/badge/Nginx-1.25v-blue.svg?logo=nginx" alt="Nginx 1.25" />
</a>

Quero deixar claro que utilizei todas as tecnologias e também agradeço muito pela oportunidade que me foi dada 😁.

![Alt Text](https://66.media.tumblr.com/5d660d5cee750d69c64c4e5eaca5e862/tumblr_mqklvtXpgy1s0kkr4o1_250.gif)

**OBSERVAÇÕES IMPORTANTES**: 
- Usar Accept-application/json nos headers das requisições.
- Verificar a versão do Docker para o pleno funcionamento da aplicação.
- Também fiz uma tarefa a mais porque achei que seria mais coerente. Os produtos têm foreign keys da pessoa que cadastrou aquele produto e da pessoa que atualizou aquele produto. O único "problema" seria na hora de apagar um usuário, que também apaga o produto em que aquele usuário tem foreign key. Dessa maneira, não temos simplesmente produtos cadastrados soltos no banco de dados.
- O Redis é utilizado apenas nas requisições de busca de dados com o lifetime de 40 segundos.  

A aplicação é de simples execução, "plug and play". Basta rodar:
```
docker compose up --build
```
e esperar essa mensagem no console do docker: 
```
NOTICE: fpm is running, pid 1
NOTICE: ready to handle connections
``` 
# Documentação das rotas
Toda a aplicação tem como base a url: ```http://localhost::80/api```

## Rota de Registro - POST
```/register```
Exemplo do json:
```
{
  "name": "example",
  "email": "example@gmail.com",
  "password": "example321"
}
```
## Rota de Login - POST 
```/login```
Exemplo do json:
```
{
  "email": "example@gmail.com",
  "password": "example321"
}
```

**OBSERVAÇÃO:** A partir daqui todas as rotas estão protegidas então LEMBRE-SE de usar Bearer {token}

## Rota de logout - POST
```/logout```

## Rota para buscar o profile do usuario logado - GET
```/profile```

## Buscar todos os usuarios do sistema - GET
```/users```

## Buscar um usuario do sistema - GET
```/user/{id}```

## Cadastrar usuario no sistema - POST
```/user```
Exemplo do json:
```
{
  "name": "gabriel",
  "email": "gabriel@gmail.com",
  "password": "gabrielgabriel"
}
```

## Alterar usuario no sistema - PUT
```/user/{id}```
Exemplo do json:
```
{
  "name": "heitor",
  "email": "heitor@gmail.com",
  "password": "heitor321"
}
```

## Apagar usuario do sistema - DELETE
```/user/{id}```

## Buscar todos os produtos do sistema - GET
```/products```

## Buscar um produto do sistema - GET
```/product/{id}```

## Cadastrar produto no sistema - POST
```/product```
Exemplo do json:
```
{
  "name": "Sabão",
  "description": "Sabão de corpo",
  "preco": 10.00,
  "garantia": "7 dias",
  "marca": "J&J",
  "material": "Sal de ácido graxo",
  "origem": "Vegetal"
}
```

## Alterar produto no sistema - PUT
```/product/{id}```
Exemplo do json:
```
{
  "name": "Sabão",
  "description": "Sabão de corpo",
  "preco": 15.00,
  "garantia": "7 dias",
  "marca": "J&J",
  "material": "Sal de ácido graxo",
  "origem": "Vegetal"
}
```

## Apagar produto do sistema - DELETE
```/product/{id}```
