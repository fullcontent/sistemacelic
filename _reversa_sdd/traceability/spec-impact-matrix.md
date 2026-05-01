# Matriz de Impacto de Especificações (Spec Impact Matrix)

A matriz abaixo cruza os principais componentes (módulos) do sistema, demonstrando o nível de impacto de uma mudança em um componente (Linha) sobre os demais componentes (Coluna).

**Legenda:**
- **🔴 Alto:** Mudança quebra/afeta diretamente regras de negócio ou estrutura do alvo.
- **🟡 Médio:** Mudança afeta relatórios, métricas ou integrações secundárias.
- **🟢 Baixo / Nulo:** Módulos isolados.

| Módulo que muda ↓ \ Impactado → | Auth / ACL | Dashboard | Faturamento | OS / Prestadores | Proposta | Reembolso | Portal Cliente |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Auth / ACL** (Regras de Acesso) | - | 🟡 | 🟢 | 🟢 | 🟡 | 🟢 | 🔴 |
| **Dashboard** (Métricas / Exportação) | 🟢 | - | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 |
| **Faturamento** (Regras Financeiras) | 🟢 | 🟡 | - | 🟢 | 🟢 | 🟢 | 🟡 |
| **OS / Prestadores** (Contratos Externos) | 🟢 | 🟡 | 🟢 | - | 🟢 | 🟢 | 🟢 |
| **Proposta** (Conversão de Serviços) | 🟢 | 🔴 | 🔴 | 🟢 | - | 🟢 | 🟢 |
| **Reembolso** (Cobrança de Taxas) | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | - | 🟡 |
| **Portal Cliente** (Visualização/Notificações) | 🔴 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | - |

## Análise de Gaps Arquiteturais
1. **O Efeito Cascata da Proposta:** Alterar como uma `Proposta` cria instâncias de `Servico` e `ServicoFinanceiro` tem impacto **Alto** (🔴) no `Faturamento` (que assume que o financeiro existe) e no `Dashboard` (que consolida estatísticas operacionais via Cursor CSV).
2. **Dependência do ACL:** O `Portal Cliente` possui dependência extrema (🔴) da modelagem de `UserAccess`. Qualquer mudança na forma como acessos de empresas/unidades são concedidos vai quebrar a view do cliente final.
3. **Isolamento de OS:** Apesar de essencial, o módulo de `OS / Prestadores` tem regras de pagamento desvinculadas do faturamento geral da Celic, apresentando baixo impacto arquitetural em outros módulos (mas alto valor de negócio interno).
