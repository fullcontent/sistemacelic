# Módulo: Dashboard

## Visão Geral
Centraliza indicadores de performance, geolocalização de unidades de negócio e geração de relatórios gerenciais complexos via exportação CSV.

## Responsabilidades
- Compilar métricas agregadas (Serviços a Vencer, Finalizados, Em Andamento).
- Gerenciar o mapa de navegação interativo integrado com Google Maps/PositionStack.
- Processar relatórios pesados (CSV e JSON) evitando estouro de memória utilizando Lazy Loading.
- Renderizar a timeline de auditoria baseada na tabela `historicos`.

## Interface
- **Modelos Principais:** `Servico`, `Pendencia`, `Historico`
- **Controladores:** `DashboardController`, `AdminController`
- **APIs Externas:** Google Maps StreetView API, PositionStack API

## Regras de Negócio
- Apenas serviços do tipo `licencaOperacao` são considerados no filtro de "Serviços a Vencer". 🟢
- O limiar para "A Vencer" é estipulado em **60 dias** antes da data de `licenca_validade`. 🟢
- Ao exportar o relatório `completoCSV`, o sistema realiza *self-healing* (autocorreção) criando registros zerados em `ServicoFinanceiro` para serviços que não o possuam. 🟢
- A "Etapa" do processo apresentada nos relatórios de pendências é inferida pela presença de `protocolo_anexo` e `laudo_anexo`. 🟢

## Fluxo Principal (Geração de CSV Completo)
1. O usuário (Admin) solicita a exportação do relatório completo.
2. O sistema inicializa uma `StreamResponse` e define os cabeçalhos HTTP.
3. O sistema usa `cursor()` para iterar sobre os milhares de registros de `Servico` no banco.
4. Para cada serviço, converte os IDs técnicos em nomes legíveis (ex: Solicitante).
5. Aplica lógicas de fallback e autocorreção (ex: injeta `ServicoFinanceiro`).
6. A linha é escrita diretamente no buffer de saída HTTP (`fputcsv(php://output)`).

## Fluxos Alternativos
- **Falta de API Key de Geolocalização:** O mapa falha silenciosamente ou exibe imagem padrão no StreetView.
- **Usuário sem empresas ativas:** Se o array de acessos for vazio, as queries retornarão coleções vazias em vez de quebrar a aplicação.

## Dependências
- `Servico`, `Unidade`, `Empresa` — Para composição da agregação dos relatórios.

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Escalabilidade | Streaming de CSV via `cursor()` para evitar OOM (Out Of Memory) | `AdminController@completoCSV` | 🟢 |
| Integração | Uso de chamadas de API de terceiros para imagens de mapa | Views do Dashboard | 🟢 |

> Inferido a partir do código. Validar com equipe de operações.

## Cenários de Borda

1. **Geração de CSV com serviço legados sem registro financeiro:**
   - *Comportamento:* O sistema detecta a ausência, cria a entidade "on-the-fly" e preenche com valores 0. O relatório segue sem interrupção. 🟢
2. **Consulta com milhares de interações (Histórico):**
   - *Comportamento:* Apenas as últimas 5 interações são apresentadas ativamente nas listagens rápidas para preservar performance. 🟢

## Critérios de Aceitação

```gherkin
Dado que o usuário administrador solicita o Relatório Completo CSV
Quando a quantidade de serviços no banco excede 10.000 registros
Então a aplicação deve entregar o arquivo via stream sem apresentar erros de Timeout ou Memória

Dado que um serviço possui licença de operação vencendo daqui a 30 dias
Quando o Dashboard é carregado
Então esse serviço deve constar na métrica "Serviços a Vencer" (limiar < 60 dias)
```

## Prioridade

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Streaming CSV | Must | Sem o streaming, relatórios de base legada quebrariam a aplicação |
| Métricas Vencimento | Must | Caminho crítico para operação do negócio |
| Mapa Interativo | Should | Auxiliar visual, mas operação pode seguir sem ele |
| Timeline de Histórico | Should | Ferramenta de auditoria importante |

> Prioridade inferida por frequência de chamada e posição na cadeia de dependências.

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/Http/Controllers/AdminController.php` | `AdminController@completoCSV` | 🟢 |
| `app/Http/Controllers/AdminController.php` | `AdminController@servicosVencer` | 🟢 |
| `app/Http/Controllers/DashboardController.php` | `DashboardController` | 🟢 |
