@component('mail::message')
# Relatório de Auditoria de Boletos (via IA)

Abaixo estão os resultados da auditoria de boletos e comprovantes desta semana.

**Total Auditado:** {{ $estatisticas['total_auditado'] }}
**Total Divergentes:** {{ $estatisticas['total_divergentes'] }}
**Reembolsos Bloqueados (Regra Castro):** {{ $estatisticas['reembolsos_bloqueados'] }}

---

### Alertas de Divergência Crítica

@foreach ($comprovantesDivergentes as $comprovante)
- **ID Faturamento:** {{ $comprovante->boleto->faturamento_id ?? 'N/A' }}
- **Motivo da Divergência:** {{ $comprovante->motivo_divergencia }}
- **Valor Boleto:** R$ {{ $comprovante->boleto->valor ?? 'N/A' }} | **Valor Pago:** R$ {{ $comprovante->valor_pago }}
@if($comprovante->reembolso_bloqueado)
- 🔴 **REEMBOLSO BLOQUEADO!** O favorecido não é a Castro.
@endif
---
@endforeach

Por favor, acesse o painel financeiro para conciliação manual.

@component('mail::button', ['url' => config('app.url')])
Acessar Painel
@endcomponent

Atenciosamente,<br>
{{ config('app.name') }} (Equipe de IA)
@endcomponent
