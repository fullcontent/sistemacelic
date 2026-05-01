# Módulo: Auth

## Visão Geral
Gerencia a autenticação de usuários, controle de sessões e a filtragem de acesso baseada em papéis (RBAC) combinada com permissões em nível de dados (ACL por Empresa/Unidade).

## Responsabilidades
- Processar o fluxo de login e redirecionar usuários para o painel correto baseado no privilégio (`admin` vs `cliente`).
- Fornecer os métodos e relacionamentos necessários para filtrar consultas de dados (`UserAccess`).
- Gerenciar envio de notificações e e-mails de recuperação de senha.

## Interface
- **Modelos Principais:** `User`, `UserAccess`
- **Controladores:** `LoginController`, `RegisterController`, `ForgotPasswordController`, `ResetPasswordController`

## Regras de Negócio
- Um usuário do tipo `admin` deve ser redirecionado para `admin/home` após o login. 🟢
- Um usuário do tipo `cliente` deve ser redirecionado para `cliente/home` após o login. 🟢
- Um usuário do tipo `cliente` só pode ter visibilidade das entidades (Empresas, Unidades, Serviços) às quais está explicitamente vinculado na tabela `user_accesses`. 🟢

## Fluxo Principal (Login)
1. Usuário submete credenciais (email/senha).
2. Sistema valida credenciais.
3. Sistema verifica o campo `privileges` do modelo `User`.
4. Sistema redireciona para a rota apropriada baseada no privilégio.

## Fluxos Alternativos
- **Credenciais Inválidas:** Retorna para a tela de login com erro de credencial.
- **Esqueci a Senha:** Gera token, armazena no banco e dispara e-mail via driver nativo de Notificações do Laravel.

## Dependências
- `Empresa` e `Unidade` — Modelos vinculados via `UserAccess`.

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Autenticação obrigatória em rotas protegidas | Middleware `auth` | 🟢 |
| Segurança | Redirecionamento condicional de painel | `LoginController` | 🟢 |

> Inferido a partir do código. Validar com equipe de operações.

## Critérios de Aceitação

```gherkin
Dado que o usuário tem o campo `privileges` como `admin`
Quando ele realiza login com sucesso
Então ele deve ser redirecionado para a rota `admin/home`

Dado que o usuário é do tipo `cliente` e tenta acessar a rota `admin/home`
Quando a requisição é interceptada
Então o middleware de segurança deve bloquear e redirecionar ou exibir erro 403

Dado que o usuário insere uma senha incorreta
Quando ele submete o formulário de login
Então ele deve ver uma mensagem de erro e permanecer na página de login
```

## Prioridade

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Autenticação (Login) | Must | Caminho crítico absoluto do sistema |
| Filtragem ACL | Must | Sem isso, ocorre vazamento de dados de clientes |
| Redirecionamento Múltiplo | Must | Garante a UX para dois perfis de público distintos |
| Reset de Senha | Should | Evita overhead do suporte |

> Prioridade inferida por frequência de chamada e posição na cadeia de dependências.

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/User.php` | `User` | 🟢 |
| `app/UserAccess.php` | `UserAccess` | 🟢 |
| `app/Http/Controllers/Auth/LoginController.php` | `LoginController` | 🟢 |
