# Módulo: Portal do Cliente

## Visão Geral
Uma interface segmentada e restrita onde os clientes finais da Celic podem visualizar o progresso dos serviços, taxas, faturamentos, adicionar comentários e interagir com a equipe interna.

## Responsabilidades
- Prover visualização apenas dos dados pertencentes às empresas e unidades associadas ao cliente logado.
- Exibir status e detalhes de serviços, laudos e protocolos.
- Gerenciar sistema de comentários (interações) em serviços.
- Identificar marcações (`@user`) nos comentários e disparar notificações webhooks via n8n.

## Interface
- **Modelos Principais:** (Consome dados de todos os módulos principais, mas de forma Read-Only na maioria, exceto Historico).
- **Controladores:** `ClienteController`
- **Integração:** Webhook HTTP via Guzzle para n8n.

## Regras de Negócio
- Todo dado exibido no portal do cliente (Serviços, Taxas, Reembolso) DEVE passar pela verificação da tabela `user_accesses`. 🟢
- O cliente pode postar atualizações no log de `Historico` do serviço. 🟢
- Se o comentário possuir a string `@` e um nome válido, o sistema intercepta, extrai o contexto e notifica a equipe através de uma automação externa. 🟢

## Fluxo Principal (Menção e Notificação)
1. Cliente escreve comentário no detalhe do serviço: "Por favor verificar @Joao".
2. O formulário envia o POST para `salvarInteracao`.
3. O sistema cria o `Historico` vinculado ao `Servico` e ao `User` (Cliente).
4. O sistema detecta a menção, extrai o nome (`Joao`) e constrói um payload contendo: Nome mencionado, Link do Serviço, Texto Original, Nome do Serviço.
5. Um request assíncrono é disparado via Guzzle para o endpoint do n8n.
6. A página recarrega para o cliente.

## Fluxos Alternativos
- **Acesso Negado (Falta de Vinculo):** Se um cliente tentar acessar a URL direta de um serviço de uma empresa que ele não tem acesso, o sistema bloqueia exibindo página 403.

## Dependências
- `UserAccess` — Base de todo o ACL deste módulo.
- `Historico` — Tabela usada para "mensageria" e timeline do cliente.

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Controle de acesso vertical e horizontal | `getServicosCliente()` em `ClienteController` | 🟢 |
| Resiliência | Envio de webhook em background | `ClienteController@salvarInteracao` (embora Guzzle possa ser síncrono dependendo da configuração) | 🟡 |

## Cenários de Borda
1. **Falha no n8n:** A chamada do webhook ao n8n deve ser no modelo "fire-and-forget" (assíncrona/não-bloqueante). Se o n8n estiver fora do ar ou lento, o comentário do usuário deve ser salvo normalmente no banco e a interface seguir seu fluxo sem retornar erro 500 para o cliente. 🟢

## Critérios de Aceitação

```gherkin
Dado que o Cliente A tem acesso à Empresa X
Quando ele tentar listar serviços
Então ele deve ver apenas os serviços da Empresa X
E não deve ver nenhum serviço da Empresa Y

Dado que um cliente publica uma interação mencionando @Administrador
Quando o botão de enviar é clicado
Então a mensagem deve aparecer na timeline do serviço
E o n8n deve receber um webhook com o payload da menção
```

## Prioridade

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Filtro de Acesso | Must | Omitir essa regra gera vazamento de dados críticos de clientes. |
| Interações | Must | Meio oficial de comunicação com o cliente na plataforma. |
| Webhook de Notificação | Should | Essencial para tempo de resposta da equipe. |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/Http/Controllers/ClienteController.php` | `ClienteController` | 🟢 |
