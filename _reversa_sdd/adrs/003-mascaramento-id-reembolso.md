# ADR 003: Mascaramento de Identificadores de Reembolso

## Status
Aceito (Retroativo)

## Contexto
Por razões de percepção de volume ou segurança, expor IDs sequenciais baixos (ex: Reembolso #1, #2) pode revelar a juventude da plataforma ou facilitar a enumeração de registros por parte do cliente final.

## Decisão
Implementar um mascaramento simples nos IDs de reembolso apresentados na interface e em documentos PDF gerados.

A lógica em `ReembolsoController@fillWithZeros` aplica um offset de **1000** ao ID original do banco de dados:
```php
return (string) ($number + 1000);
```

## Consequências
- **Positivas:** IDs parecem mais "profissionais" e padronizados para o cliente final; dificulta a estimativa de volume total de reembolsos do sistema.
- **Negativas:** Pode causar confusão para a equipe interna de suporte se não houver clareza sobre qual ID está sendo referenciado (banco vs interface).
