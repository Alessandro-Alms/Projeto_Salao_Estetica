# Sistema de Gestao para Salao de Beleza

Este projeto e um sistema de gerenciamento para saloes de estetica, desenvolvido com **Laravel 12**, PHP **8.2+** e arquitetura **MVC**.

No ambiente local do projeto o banco configurado e **SQLite**. A aplicacao tambem possui arquivos de exemplo para configuracao em ambiente de deploy.

## Diagrama de Banco de Dados

![Diagrama do Banco de Dados](docs/diagrama-banco.png)

## Equipe e Responsabilidades

| Integrante | Funcao | Responsabilidades principais |
| :--- | :--- | :--- |
| **Geovanna Vieira** | Gerente de Projeto | Guia a equipe, organiza cronogramas e relatorios. |
| **Alessandro Lima** | Dev. Back-end | Implementacao dos Models, Controllers e regras de negocio. |
| **Sophia Ribeiro** | Dev. Front-end | Implementacao das Views Blade, CSS e experiencia visual. |
| **Eliene de Sousa** | Auxiliar | Suporte ao desenvolvimento front-end e back-end. |
| **Anthony Almeida** | Auxiliar | Suporte ao desenvolvimento front-end e back-end. |

## Tecnologias

- **Framework:** Laravel 12
- **Linguagem:** PHP 8.2+
- **Banco local:** SQLite
- **Interface:** Blade, Tailwind CSS, Vite e Alpine.js
- **Testes:** PHPUnit
- **Exportacoes:** DomPDF e Laravel Excel

## Credenciais de Teste

Quando o banco estiver populado pelo `DatabaseSeeder`, podem ser usados estes acessos:

- **Gerente:** `geovanna.vieira@gmail.com` / `senha123`
- **Recepcionista:** `eliene.sousa@gmail.com` / `senha123`
- **Profissional:** `antony.almeida@gmail.com` / `senha123`
- **Cliente:** `sophia.ribeiro@gmail.com` / `senha123`

## Como Rodar

```bash
cd src
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Como Testar

```bash
cd src
php artisan test
```

## Estrutura Principal

- `src/app/Models`: entidades e relacionamentos.
- `src/app/Http/Controllers`: regras de negocio e fluxo das telas.
- `src/resources/views`: telas Blade.
- `src/database/migrations`: estrutura do banco.
- `src/database/seeders`: dados iniciais para demonstracao.

## Convencoes

- Classes em `PascalCase`.
- Campos de banco em `snake_case`.
- Metodos em `camelCase`.
- Commits sugeridos: `[FEAT]`, `[FIX]`, `[DOCS]`, `[STYLE]`.
