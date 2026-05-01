# Perguntas para Validação — SistemaCelic2

Este documento agrupa as lacunas de especificação e comportamentos de borda que precisam ser validados pela equipe de negócios/técnica.

## Módulo: Proposta
1. **Transações na Aprovação (🔴):** Atualmente a conversão de serviços a partir da aprovação de uma proposta não usa `DB::transaction`. Se o banco cair no meio, a proposta fica corrompida. Isso deve ser ajustado para um comportamento transacional rígido? Faça o ajuste que fique com mais eficiencia aqui. 

## Módulo: Faturamento
2. **Excedente Financeiro (🟡):** Ao faturar parcialmente, o sistema realmente bloqueia o usuário de inserir um valor maior do que o `valorAberto` do serviço, ou há casos onde é cobrado um valor a mais por multas/juros na mesma nota? Nao pode enviar um valor maior. 

## Módulo: Reembolso
3. **Falha no ZIP por Caracteres (🔴):** Se o cliente faz upload de um comprovante chamado `cópia_ç.pdf`, a geração do `.zip` está tratada para sanitizar o nome e não quebrar no Windows/Linux? Acredito nao estar. Se nao estiver, pode fazer esse ajuste. 

## Módulo: Cliente / Notificações
4. **Timeout do Webhook n8n (🔴):** A requisição ao n8n quando um cliente faz uma menção `@user` bloqueia a tela do usuário? Se o n8n estiver fora do ar, o comentário é salvo ou a tela retorna erro 500? Ele chama um webhook e segue. Pelo menos é pra funcionar dessa forma. 
