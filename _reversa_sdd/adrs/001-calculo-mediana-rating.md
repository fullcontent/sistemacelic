# ADR 001: Cálculo de Reputação de Prestadores via Mediana

## Status
Aceito (Retroativo)

## Contexto
O sistema gerencia prestadores de serviço terceirizados que executam Ordens de Serviço (OS). A qualidade desses serviços precisa ser monitorada para auxiliar na escolha de parceiros futuros. Métodos tradicionais de média aritmética são suscetíveis a distorções causadas por avaliações extremamente baixas ou altas (outliers).

## Decisão
Implementar o cálculo de rating (reputação) utilizando a **Mediana** das notas das avaliações armazenadas em `prestador_comentarios`.

A lógica foi implementada em `OrdemServicoController@calculateMedian`:
1. Coleta todas as notas (`rating`) do prestador.
2. Ordena os valores.
3. Se a quantidade for ímpar, pega o valor central.
4. Se for par, calcula a média dos dois valores centrais.

## Consequências
- **Positivas:** Maior resiliência contra "ataques" de avaliações negativas ou avaliações excessivamente positivas que não representam o padrão real do prestador.
- **Negativas:** Requer um volume mínimo de avaliações para ser estatisticamente significativo; pode parecer contraintuitivo para usuários acostumados com médias simples.
