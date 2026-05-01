# Inventário do Projeto — SistemaCelic2

## 1. Estrutura de Diretórios
O projeto segue a estrutura padrão do framework Laravel.

- `app/`: Lógica central (Models, Controllers, Services, Providers)
- `bootstrap/`: Scripts de inicialização do framework
- `config/`: Arquivos de configuração da aplicação
- `database/`: Migrations, factories e seeds
- `public/`: Entry point público e assets estáticos
- `resources/`: Views (Blade), assets (Sass, JS) e traduções
- `routes/`: Definições de rotas (Web, API, Console)
- `storage/`: Logs, cache e arquivos gerados
- `tests/`: Testes automatizados (Unit e Feature)

## 2. Tecnologias e Frameworks
- **Linguagem Principal:** PHP 7.2+ (313 arquivos .php)
- **Framework Web:** Laravel 6.2
- **Frontend:** Laravel Mix, Sass, AdminLTE 2.0
- **Banco de Dados:** MySQL (inferido via Laravel)
- **Containerização:** Docker (docker-compose.yml presente)

## 3. Módulos Identificados
Com base na estrutura de views e controllers:
- **Auth:** Gerenciamento de autenticação e senhas
- **Dashboard:** Painel principal
- **Faturamento:** Gestão de faturamento e notas
- **Ordem de Serviço:** Controle de ordens de serviço
- **Prestadores:** Cadastro e gestão de prestadores
- **Proposta:** Gestão de propostas comerciais
- **Reembolso:** Controle de solicitações de reembolso
- **Relatórios:** Geração de relatórios gerenciais
- **Solicitantes:** Gestão de solicitantes
- **Cliente:** Área ou gestão de clientes

## 4. Pontos de Entrada
- **Web:** `public/index.php` -> `routes/web.php`
- **API:** `routes/api.php`
- **CLI:** `artisan` -> `routes/console.php`

## 5. Configurações e CI/CD
- **Ambiente:** `.env`, `.env.example`
- **Docker:** `docker-compose.yml`, pasta `.docker/`
- **Build JS/CSS:** `webpack.mix.js`

## 6. Qualidade e Testes
- **Framework:** PHPUnit
- **Total de Arquivos de Teste:** 5
- **Observação:** Baixa cobertura aparente, foco em Feature tests.
