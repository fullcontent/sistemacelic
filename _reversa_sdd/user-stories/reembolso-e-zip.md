# User Story: Fechamento de Reembolso e Exportação ZIP

**Como** analista administrativo (Admin)
**Quero** fechar um lote de taxas pagas pela Celic para uma empresa
**Para** enviar ao cliente um pacote ZIP organizado contendo o resumo em PDF e todos os comprovantes para o ressarcimento financeiro.

## Regras de Negócio e Contexto
- Taxas marcadas com "reembolso: sim" entram na lista de taxas a faturar do cliente.
- Ao gerar o reembolso, as taxas são bloqueadas para não serem incluídas em outro reembolso acidentalmente.
- O ID do reembolso que o cliente vê sofre um offset (+1000) por questões de sigilo e UX.

## Cenários de Aceitação

### Cenário 1: Fechamento com Múltiplas Taxas e Comprovantes
**Dado** que a Empresa "Contoso" tem 2 taxas pendentes de reembolso (Taxa Bombeiros e Alvará)
**E** ambas possuem o upload do comprovante anexado via sistema
**Quando** o usuário finaliza o Step 4 do Wizard de Reembolso
**Então** o status de reembolso dessas 2 taxas é atualizado
**E** a interface exibe o "ID do Reembolso: 1045" (Sendo 45 o ID real no banco)
**E** o botão "Baixar PDF e Comprovantes" fica disponível.

### Cenário 2: Download e Empacotamento do ZIP
**Dado** que o lote de reembolso 45 foi gerado (Visível 1045)
**Quando** o usuário clica no botão de download
**Então** o sistema deve gerar dinamicamente a capa em PDF usando DomPDF
**E** deve copiar os arquivos `uploads/taxas/comprovante_bombeiro.pdf` e `uploads/taxas/comprovante_alvara.png` para uma pasta temporária `1045/`
**E** deve renomear os arquivos na pasta temporária para "Item 1 - Comprovante.pdf" e "Item 2 - Comprovante.png"
**E** deve compactar a pasta em `Reembolso-1045.zip`
**E** devolver o pacote pronto para o navegador do usuário.

### Cenário 3: Ausência de Comprovante (Fallback para Boleto)
**Dado** que uma taxa no lote não possui o arquivo no campo `comprovante`, mas possui no campo `boleto_arquivo`
**Quando** o script de empacotamento do ZIP rodar
**Então** o sistema deve fazer o fallback e empacotar o arquivo do boleto
**E** renomeá-lo como "Item X - Boleto.ext".

### Cenário 4: Prevenção de Duplicidade (Reembolso Duplo)
**Dado** que a Taxa de Alvará já foi incluída no Lote 45
**Quando** o analista for gerar um novo reembolso para a Empresa "Contoso" no mês seguinte
**Então** a Taxa de Alvará NÃO deve aparecer no Step 2 do wizard.
