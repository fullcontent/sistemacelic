# ADR 002: Integração de Notificações via Webhook (n8n)

## Status
Aceito (Retroativo)

## Contexto
O sistema precisa notificar usuários sobre menções (`@user`) e eventos importantes. O uso do driver nativo de e-mail do Laravel pode ser limitado para fluxos complexos de automação e personalização sem sobrecarregar o servidor de aplicação.

## Decisão
Externalizar o envio de e-mails e processamento de notificações complexas para uma ferramenta de automação externa (**n8n**) via Webhooks.

A implementação em `ClienteController@salvarInteracao` dispara uma requisição POST para um endpoint do n8n contendo o contexto da interação (resumo gerado por IA, nome do usuário e link do serviço).

## Consequências
- **Positivas:** Desacoplamento da lógica de envio de e-mail; facilidade para alterar templates e provedores de e-mail no n8n sem mexer no código PHP; menor latência na resposta do servidor (processamento assíncrono natural via webhook).
- **Negativas:** Dependência de uma infraestrutura externa ativa; necessidade de gerenciar falhas de entrega no lado do integrador (n8n).
