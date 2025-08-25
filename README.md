# Desafio Spassu

## Como rodar o projeto
Para rodar o projeto, siga os passos abaixo:

- Tenha certeza que o docker está instalado na sua máquina e rodando.
- Execute o comando `docker compose up -d --build` na raiz do projeto.
- Aguarde o término do processo de build.
- Execute o composer install `docker compose exec app composer install`
- Execute as migrations `docker compose exec app php artisan migrate`
- O projeto estará rodando em `http://localhost:8000`
- Para executar os testes, rode o comando `docker compose exec app php artisan test --coverage`

A api está documentada no arquivo Desafio_Spassu.postman_collection.json, que pode ser importado no [Postman](https://www.postman.com/).

## Metodologia

Uma parte do desafio foi desenvolvida utilizando as tecnicas indicadas na documentação do Laravel.
Isso pode ser verificado no controlador AuthController.

No restante do projeto utilizei a abordagem DDD (Domain Driven Design) para organizar o código.
Podemos observar esse desenvolvimento no diretório app/Livros.

## Testes

Os testes foram desenvolvidos utilizando o PHPUnit, que já vem integrado com o Laravel.
Consegui atingir uma cobertura de 100% nos testes unitários e de feature.
