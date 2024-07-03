<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
</p>

## Como executar

É necessário ter o docker e docker-compose configurado.

Para rodar o projeto pela primeira vez, basta executar o comando:
```shell
docker-compose up -d --build
```

Para executar os testes unitários é necessário acessar o container:

```shell
docker exec -it ebanx-take-home_app_1 /bin/bash
```
E depois executar cada feature de teste:

```shell
php artisan test --filter ResetTest
php artisan test --filter AccountTest
```

O projeto é executado na porta 8008 por padrão (http://localhost:8008)