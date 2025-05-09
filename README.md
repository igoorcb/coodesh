# Food Facts API

## Sobre o Projeto
API RESTful para o desafio técnico Open Food Facts, desenvolvida com Laravel 10 e MySQL, seguindo princípios SOLID, Domain-Driven Design (DDD) e Clean Architecture. A API permite acessar e gerenciar dados de produtos alimentícios, com funcionalidades para listar, buscar, criar, atualizar e excluir produtos.

## Tecnologias Utilizadas
- PHP 8.2
- Laravel 10.x
- MySQL 8.0
- Docker & Docker Compose
- Nginx

## Arquitetura
O projeto segue uma arquitetura em camadas baseada em DDD:

- **Domain**: Contém as entidades, interfaces de repositório e serviços de domínio
- **Application**: Contém os controllers e lógica de aplicação
- **Infrastructure**: Contém implementações concretas de repositórios e serviços externos

## Estrutura de Diretórios

```
app/
├── Application/
│   └── Products/
│       └── Controllers/
│           └── ProductController.php
├── Domain/
│   └── Products/
│       ├── Models/
│       │   └── Product.php
│       ├── Repositories/
│       │   └── Interfaces/
│       │       └── ProductRepositoryInterface.php
│       └── Services/
│           ├── Interfaces/
│           │   └── ProductServiceInterface.php
│           └── ProductService.php
├── Infrastructure/
│   └── Repositories/
│       └── ProductRepository.php
└── Providers/
    └── DomainServiceProvider.php
```

## Requisitos
- Docker e Docker Compose
- Git

## Instalação

```bash
# Clone o repositório
git clone https://github.com/seu-usuario/food-facts-api.git
cd food-facts-api

# Configure o ambiente
cp .env.example .env

# Inicie os containers Docker
docker-compose up -d

# Instale as dependências
docker-compose exec app composer install

# Gere a chave da aplicação
docker-compose exec app php artisan key:generate

# Execute as migrações
docker-compose exec app php artisan migrate
```

## Funcionalidades Implementadas
- ✅ Arquitetura DDD com separação clara de responsabilidades
- ✅ Containerização com Docker para ambiente de desenvolvimento
- ✅ Endpoints RESTful para gerenciamento de produtos
- ✅ Importação de produtos do Open Food Facts
- ✅ Validação de dados e tratamento de erros
- ✅ Paginação de resultados
- ✅ Documentação completa da API

## Endpoints da API

### Status da API
- `GET /api`: Retorna informações sobre a API, incluindo status do banco de dados, última execução do cron, uptime e uso de memória.

### Produtos
- `GET /api/products`: Lista todos os produtos com paginação
- `GET /api/products/{code}`: Obtém um produto específico pelo código
- `POST /api/products`: Cria um novo produto
- `PUT /api/products/{code}`: Atualiza um produto existente
- `DELETE /api/products/{code}`: Move um produto para a lixeira (soft delete)

### Importação
- `POST /api/import`: Inicia a importação de produtos do Open Food Facts

## Exemplos de Uso

### Listar Produtos

```bash
curl -X GET http://localhost:8000/api/products
```

### Criar Produto

```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{
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
  }'
```

### Importar Produtos

```bash
curl -X POST http://localhost:8000/api/import
```

## Comandos Úteis para Desenvolvimento

```bash
# Acessar o container da aplicação
docker-compose exec app bash

# Limpar caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear

# Executar testes
docker-compose exec app php artisan test

# Formatar código (PSR-12)
docker-compose exec app ./vendor/bin/pint
```

## Testes
Para executar os testes automatizados:

```bash
docker-compose exec app php artisan test
```

## Desafios Superados

Durante o desenvolvimento, enfrentamos e superamos diversos desafios:

- Configuração do ambiente Docker para desenvolvimento
- Implementação da arquitetura DDD em Laravel
- Integração com a API do Open Food Facts
- Tratamento de grandes volumes de dados durante a importação
- Resolução de problemas de dependências e injeção de serviços

## Próximos Passos

Melhorias planejadas para o futuro:

- Implementação de autenticação JWT
- Documentação com Swagger/OpenAPI
- Implementação de cache para melhorar o desempenho
- Configuração de CI/CD para integração e entrega contínuas
- Monitoramento e logging avançados

## License

The Laravel framework is open-sourced software licensed under the MIT license.

Desenvolvido como parte de um desafio técnico utilizando dados do Open Food Facts.
