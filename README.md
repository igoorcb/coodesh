# Food Facts API

<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Visão Geral

Esta API fornece acesso aos dados de produtos alimentícios do Open Food Facts, permitindo listar, buscar, criar, atualizar e excluir produtos. A API também inclui funcionalidade para importar produtos diretamente do Open Food Facts.

## Tecnologias Utilizadas

- **PHP 8.2**
- **Laravel 10.x**
- **MySQL 8.0**
- **Docker & Docker Compose**
- **Nginx**

## Arquitetura

O projeto segue os princípios de Domain-Driven Design (DDD) e Clean Architecture, com uma clara separação entre:

- **Domain**: Contém as entidades, interfaces de repositório e serviços de domínio
- **Application**: Contém os controllers e lógica de aplicação
- **Infrastructure**: Contém implementações concretas de repositórios e serviços externos

### Estrutura de Diretórios

app/
├── Application/
│ └── Products/
│ └── Controllers/
│ └── ProductController.php
├── Domain/
│ └── Products/
│ ├── Models/
│ │ └── Product.php
│ ├── Repositories/
│ │ └── Interfaces/
│ │ └── ProductRepositoryInterface.php
│ └── Services/
│ ├── Interfaces/
│ │ └── ProductServiceInterface.php
│ └── ProductService.php
├── Infrastructure/
│ └── Repositories/
│ └── ProductRepository.php
└── Providers/
└── DomainServiceProvider.php


## Requisitos

- Docker e Docker Compose
- Git

## Instalação

1. Clone o repositório:

```bash
git clone https://github.com/seu-usuario/food-facts-api.git
cd food-facts-api
Configure o arquivo .env:
bash
Copy Code
cp .env.example .env
Inicie os containers Docker:
bash
Copy Code
docker-compose up -d
Instale as dependências:
bash
Copy Code
docker-compose exec app composer install
Gere a chave da aplicação:
bash
Copy Code
docker-compose exec app php artisan key:generate
Execute as migrações:
bash
Copy Code
docker-compose exec app php artisan migrate
(Opcional) Importe alguns produtos para teste:
bash
Copy Code
docker-compose exec app php artisan tinker
No console do Tinker:

php
Copy Code
use App\Domain\Products\Models\Product;
use Illuminate\Support\Carbon;

Product::create([
    'code' => '123456789',
    'status' => 'published',
    'product_data' => [
        'product_name' => 'Example Product 1',
        'brands' => 'Example Brand',
        'quantity' => '100g',
        'ingredients_text' => 'Ingredient 1, Ingredient 2',
        'nutriments' => [
            'energy-kcal_100g' => 200,
            'fat_100g' => 10,
            'carbohydrates_100g' => 20,
            'proteins_100g' => 5,
            'salt_100g' => 1
        ]
    ],
    'imported_t' => Carbon::now(),
]);
Uso da API
A API estará disponível em http://localhost:8000/api.

Exemplo de Requisição
bash
Copy Code
curl -X GET http://localhost:8000/api/products
Endpoints
Informações da API
GET /api
Retorna informações sobre a API, incluindo status do banco de dados, última execução do cron, uptime e uso de memória.

Listar Produtos
GET /api/products
Parâmetros de consulta:

per_page (opcional): Número de produtos por página (padrão: 10)
Obter Produto Específico
GET /api/products/{code}
Parâmetros de caminho:

code: Código do produto
Criar Produto
POST /api/products
Corpo da requisição:

json
Copy Code
{
  "code": "123456789",
  "name": "Example Product",
  "brands": "Example Brand",
  "quantity": "100g",
  "ingredients": "Ingredient 1, Ingredient 2",
  "nutriments": {
    "energy-kcal_100g": 200,
    "fat_100g": 10,
    "carbohydrates_100g": 20,
    "proteins_100g": 5,
    "salt_100g": 1
  }
}
Atualizar Produto
PUT /api/products/{code}
Parâmetros de caminho:

code: Código do produto
Corpo da requisição (campos a serem atualizados):

json
Copy Code
{
  "product_name": "Updated Product Name",
  "brands": "Updated Brand"
}
Excluir Produto
DELETE /api/products/{code}
Parâmetros de caminho:

code: Código do produto
Importar Produtos
POST /api/import
Inicia a importação de produtos do Open Food Facts.

Desenvolvimento
Comandos Úteis
Acessar o container da aplicação:

bash
Copy Code
docker-compose exec app bash
Limpar o cache:

bash
Copy Code
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
Executar migrações:

bash
Copy Code
docker-compose exec app php artisan migrate
Reverter migrações:

bash
Copy Code
docker-compose exec app php artisan migrate:rollback
Padrões de Código
Este projeto segue os padrões de código PSR-12 e utiliza o Laravel Pint para formatação de código.

Para formatar o código:

bash
Copy Code
docker-compose exec app ./vendor/bin/pint
Testes
Execute os testes com:

bash
Copy Code
docker-compose exec app php artisan test
Contribuição
Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the Laravel documentation.

Code of Conduct
In order to ensure that the Laravel community is welcoming to all, please review and abide by the Code of Conduct.

Security Vulnerabilities
If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via taylor@laravel.com. All security vulnerabilities will be promptly addressed.

License
The Laravel framework is open-sourced software licensed under the MIT license.

Desenvolvido como parte de um desafio técnico utilizando dados do Open Food Facts.
