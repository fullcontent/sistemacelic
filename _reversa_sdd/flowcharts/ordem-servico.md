# Fluxogramas: Ordem de Serviço

## 1. Ciclo de Criação e Parcelamento
Este fluxo descreve como uma OS é registrada e como suas parcelas financeiras são geradas.

```mermaid
graph TD
    Start((Início)) --> Input[Receber Dados da OS]
    Input --> CreateOS[Instanciar OrdemServico]
    CreateOS --> SaveOS[Salvar OS no Banco]
    
    SaveOS --> Principal{Tem Serviço Principal?}
    Principal -- Sim --> LinkPrincipal[Criar OrdemServicoVinculo Principal]
    LinkPrincipal --> MultiVinculo
    Principal -- Não --> MultiVinculo{Tem Outros Vínculos?}
    
    MultiVinculo -- Sim --> LoopVinculo[Para cada serviço vinculado...]
    LoopVinculo --> SaveVinculo[Criar OrdemServicoVinculo]
    SaveVinculo --> LoopVinculo
    LoopVinculo -- Fim --> MultiParcela
    MultiVinculo -- Não --> MultiParcela{Tem Parcelas?}
    
    MultiParcela -- Sim --> LoopParcela[Para cada parcela no request...]
    LoopParcela --> CreatePag[Instanciar OrdemServicoPagamento]
    CreatePag --> Upload{Tem Comprovante?}
    Upload -- Sim --> DoUpload[Fazer Upload e setar Status 'pago']
    Upload -- Não --> SetAberto[Setar Status 'aberto']
    DoUpload --> SavePag[Salvar Pagamento]
    SetAberto --> SavePag
    SavePag --> LoopParcela
    LoopParcela -- Fim --> End((Fim))
    MultiParcela -- Não --> End
```

## 2. Validação de Atualização
O sistema impede que a soma dos serviços vinculados exceda o valor total da OS.

```mermaid
graph TD
    Start((Início)) --> GetValues[Obter Valor Total e Vínculos]
    GetValues --> CalcSum[Somar Valor Principal + Vínculos Adicionais]
    CalcSum --> Check{Soma > Total + 0.01?}
    Check -- Sim --> Error[Retornar Erro de Validação]
    Check -- Não --> Update[Prosseguir com Atualização]
    Update --> SyncVinculos[Remover Vínculos Antigos e Criar Novos]
    SyncVinculos --> SyncPagamentos[Remover Pagamentos Antigos e Criar Novos]
    SyncPagamentos --> End((Sucesso))
```

## 3. Lógica de Rating (Mediana)
Algoritmo utilizado para calcular a reputação do prestador.

```mermaid
graph TD
    Start((Início)) --> GetRatings[Obter lista de Ratings]
    GetRatings --> Sort[Ordenar Valores]
    Sort --> Count{Total de itens?}
    Count -- Zero --> Result0[Rating = 0]
    Count -- Ímpar --> ResultMiddle[Pegar valor central]
    Count -- Par --> ResultAvg[Média dos dois valores centrais]
    Result0 --> End((Fim))
    ResultMiddle --> End
    ResultAvg --> End
```
