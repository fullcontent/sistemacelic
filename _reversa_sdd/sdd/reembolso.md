# Módulo: Reembolso

## Visão Geral
Gerencia a consolidação e cobrança das taxas pagas pela Celic em nome dos clientes. O módulo agrupa essas taxas e gera um arquivo ZIP pronto para download contendo o consolidado em PDF e todos os comprovantes.

## Responsabilidades
- Conduzir o Wizard de Fechamento de Reembolso (seleção de empresa, filtro de taxas, seleção de emissora).
- Gerar relatórios consolidados em formato PDF (DomPDF e PDFMerger).
- Empacotar PDFs e os anexos (comprovantes e boletos) originais das taxas em um arquivo `.zip`.
- Mascarar o ID real de reembolso na interface e documentos para evitar enumeração e passar impressão de volume.

## Interface
- **Modelos Principais:** `Reembolso`, `ReembolsoTaxa`, `Taxa`
- **Controladores:** `ReembolsoController`

## Regras de Negócio
- Uma taxa atrelada a um reembolso e marcada como fechada não pode ser incluída em um novo reembolso (Prevenção de dupla cobrança). 🟢
- O ID visível de um reembolso é sempre o `id_banco + 1000` (Mascaramento). 🟢
- Para ser empacotado no ZIP, o sistema procura pelos campos de arquivo `comprovante` ou, caso não exista, o arquivo original do `boleto`. 🟢

## Fluxo Principal (Geração de ZIP)
1. Usuário finaliza o wizard e solicita o download do ZIP do reembolso gerado.
2. O controlador cria um diretório temporário nomeado com o ID (mascarado).
3. O controlador gera a capa (Resumo em PDF) e salva na pasta.
4. Para cada taxa incluída, copia o arquivo físico (comprovante/boleto) do diretório `uploads/taxas` para a pasta temporária, renomeando de forma padronizada.
5. Usa `ZipArchive` para compactar a pasta.
6. Retorna o arquivo ZIP via resposta de download.

## Fluxos Alternativos
- **Arquivo Não Encontrado:** Caso a taxa não possua anexo físico no disco, a iteração apenas ignora e continua o empacotamento das demais.

## Dependências
- `Taxa` — A origem dos dados financeiros.
- Extensões PHP — `ZipArchive`, `DomPDF`.

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Performance | Manipulação intensiva de I/O de disco para arquivos grandes | `ReembolsoController@downloadZip` | 🟡 |
| Segurança | Mascaramento de ID sequencial | `ReembolsoController@fillWithZeros` | 🟢 |

## Cenários de Borda
1. **Nomes de arquivo com caracteres especiais:** O processo de geração do ZIP DEVE aplicar sanitização (slugify/regex) nos nomes originais de arquivos que possuam acentos ou espaços para evitar quebra no filesystem (Linux) ou no ZipArchive. 🟢

## Critérios de Aceitação

```gherkin
Dado que o usuário tem um reembolso gerado com 2 taxas, ambas contendo comprovantes em PDF
Quando ele clica em "Baixar Reembolso Completo"
Então o sistema deve gerar e iniciar o download de um arquivo .zip
E o ZIP deve conter exatamente 3 arquivos (1 capa consolidada e 2 comprovantes)
```

## Prioridade

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Bloqueio de Taxas Reutilizadas | Must | Evita problemas financeiros e de relacionamento com cliente |
| Geração de ZIP | Must | Automação que economiza horas de trabalho manual |
| Mascaramento de ID | Could | Requisito de UX/Business secundário |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/Http/Controllers/ReembolsoController.php` | `ReembolsoController@downloadZip` | 🟢 |
| `app/Models/Reembolso.php` | `Reembolso` | 🟢 |
