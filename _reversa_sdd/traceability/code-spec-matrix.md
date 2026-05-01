# Matriz de Rastreabilidade (Code-Spec Matrix)

A tabela abaixo mapeia os principais arquivos de código do SistemaCelic2 com as respectivas especificações SDD geradas pelo Reversa.
Esta matriz permite garantir a cobertura de documentação e identificar arquivos "órfãos" de arquitetura que podem precisar de refatoração ou documentação futura.

**Legenda de Cobertura:**
- 🟢 Coberto e analisado
- 🟡 Parcialmente coberto (análise indireta)
- 🔴 Não documentado / Órfão

| Arquivo (`app/`) | Spec Correspondente (`sdd/`) | Cobertura | Observações |
| :--- | :--- | :---: | :--- |
| `Http/Controllers/Auth/LoginController.php` | `sdd/auth.md` | 🟢 | Controla o redirecionamento de papéis |
| `Http/Controllers/DashboardController.php` | `sdd/dashboard.md` | 🟢 | Renderiza front e KPIs |
| `Http/Controllers/AdminController.php` | `sdd/dashboard.md` | 🟢 | Centraliza relatórios pesados |
| `Http/Controllers/FaturamentoController.php`| `sdd/faturamento.md` | 🟢 | Wizard e API PlugNotas |
| `Http/Controllers/OrdemServicoController.php`| `sdd/ordem-servico.md` | 🟢 | Fluxo e pagamentos de OS |
| `Http/Controllers/PrestadorController.php` | `sdd/prestadores.md` | 🟢 | Cadastro e avaliação via Mediana |
| `Http/Controllers/PropostasController.php` | `sdd/proposta.md` | 🟢 | Motor de aprovação e clonagem |
| `Http/Controllers/ReembolsoController.php` | `sdd/reembolso.md` | 🟢 | Geração de PDFs e ZIP |
| `Http/Controllers/ClienteController.php` | `sdd/cliente.md` | 🟢 | Filtros de visão e webhook n8n |
| `Http/Controllers/RelatoriosController.php` | `sdd/relatorios.md` | 🟢 | Extrações simples de pendência |
| `Http/Controllers/SolicitantesController.php`| `sdd/solicitantes.md` | 🟢 | Tabelas auxiliares |
| `Models/User.php` | `sdd/auth.md` | 🟢 | - |
| `Models/UserAccess.php` | `sdd/auth.md`, `sdd/cliente.md` | 🟢 | Ponto central de ACL Horizontal |
| `Models/Empresa.php` | Várias Specs | 🟡 | Central para o domínio |
| `Models/Unidade.php` | Várias Specs | 🟡 | Central para o domínio |
| `Models/Servico.php` | Várias Specs | 🟡 | Entidade mais conectada do sistema |
| `Models/Faturamento.php` | `sdd/faturamento.md` | 🟢 | - |
| `Models/ServicoFinanceiro.php` | `sdd/faturamento.md`, `sdd/dashboard.md`| 🟢 | Lógica de saldo financeiro |
| `Models/OrdemServico.php` | `sdd/ordem-servico.md` | 🟢 | - |
| `Models/Prestador.php` | `sdd/prestadores.md` | 🟢 | - |
| `Models/Proposta.php` | `sdd/proposta.md` | 🟢 | - |
| `Models/Reembolso.php` | `sdd/reembolso.md` | 🟢 | - |
| `Models/Historico.php` | `sdd/dashboard.md`, `sdd/cliente.md`| 🟢 | Usado como log e status machine |
| `Models/Pendencia.php` | `sdd/proposta.md`, `sdd/relatorios.md` | 🟢 | Geração em cascata |

## Arquivos Não Documentados (Candidatos a refatoração/análise futura)
- Demais controllers na pasta `Auth/` (`RegisterController`, `ResetPasswordController`). Embora façam parte do scaffolding do Laravel, possuem regras de envio de e-mail que não foram profundamente cobertas devido ao foco no negócio *core*.
- `Models/Arquivo.php` e Lógica de Uploads Genérica.
