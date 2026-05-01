# Fluxogramas: Reembolso de Taxas

## 1. Processo de Fechamento de Reembolso
Fluxo do Wizard de Reembolso (4 etapas).

```mermaid
graph TD
    Step1[Step 1: Selecionar Empresa] --> Step2[Step 2: Filtrar Taxas por Período]
    Step2 --> Filter[Filtrar Taxas em Aberto e Pagas]
    Filter --> Step3[Step 3: Revisar Itens e Selecionar Emissora]
    Step3 --> Step4[Step 4: Confirmar e Gerar Reembolso]
    
    Step4 --> Save[Criar Registro Reembolso e ReembolsoTaxa]
    Save --> Success[Exibir Resumo e Opções de Download]
```

## 2. Lógica de Empacotamento (ZIP)
Como o sistema organiza os arquivos para o cliente.

```mermaid
graph TD
    Start((Início)) --> CreateDir[Criar Diretório Temporário id/ ]
    CreateDir --> GenPDF[Gerar Relatório Consolidado PDF]
    GenPDF --> SavePDF[Salvar no Diretório: 'Reembolso - [Nome].pdf']
    
    SavePDF --> Loop[Para cada Taxa no Reembolso...]
    Loop --> HasComp{Tem Comprovante?}
    HasComp -- Sim --> CopyComp[Copiar e Renomear: 'Item X - Comprovante.ext']
    HasComp -- Não --> HasBoleto{Tem Boleto?}
    
    CopyComp --> HasBoleto
    HasBoleto -- Sim --> CopyBoleto[Copiar e Renomear: 'Item X - Boleto.ext']
    HasBoleto -- Não --> Loop
    
    CopyBoleto --> Loop
    Loop -- Fim --> Zip[Compactar Diretório em .ZIP]
    Zip --> Download[Disponibilizar para Download]
```
