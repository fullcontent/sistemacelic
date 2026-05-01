# C4 Model: Diagrama de Contexto (Nível 1)

O Diagrama de Contexto mostra o SistemaCelic2 no centro de seu ambiente, destacando os usuários (personas) que interagem com ele e os sistemas externos com os quais ele se integra.

```mermaid
C4Context
    title Diagrama de Contexto: SistemaCelic2

    Person(admin, "Usuário Interno (Admin)", "Equipe da Celic que gerencia operações, faturamento e propostas.")
    Person(cliente, "Usuário Externo (Cliente)", "Cliente final que acompanha andamento de serviços e aprova propostas através do Portal.")
    Person(prestador, "Prestador de Serviço", "Terceirizado que executa as Ordens de Serviço (OS).")

    System(celic2, "SistemaCelic2", "Plataforma central de gestão de serviços, licenciamento, propostas comerciais e faturamento.")

    System_Ext(plugnotas, "PlugNotas", "API para emissão automatizada de Notas Fiscais de Serviço (NFS-e).")
    System_Ext(n8n, "n8n (Automação)", "Sistema de automação para orquestração de Webhooks e envio de e-mails/notificações.")
    System_Ext(gmaps, "Google Maps API", "Serviço de geocodificação e visualização (StreetView) para unidades de negócios.")
    System_Ext(positionstack, "PositionStack API", "Serviço de geocodificação secundário/fallback.")

    Rel(admin, celic2, "Gerencia serviços, emite propostas e faturas", "HTTPS")
    Rel(cliente, celic2, "Acompanha status, interage via comentários e baixa relatórios", "HTTPS")
    Rel(prestador, celic2, "Recebe OS e envia comprovantes", "HTTPS/Offline")

    Rel(celic2, plugnotas, "Envia dados de faturamento para emissão de NF", "REST/HTTPS")
    Rel(celic2, n8n, "Dispara eventos e menções (@user) via Webhook", "HTTP POST")
    Rel(celic2, gmaps, "Busca imagens e coordenadas de unidades", "REST/HTTPS")
    Rel(celic2, positionstack, "Realiza fallback de geocodificação", "REST/HTTPS")

    UpdateElementStyle(celic2, $fontColor="white", $bgColor="#0b5c0a", $borderColor="#073b06")
```
