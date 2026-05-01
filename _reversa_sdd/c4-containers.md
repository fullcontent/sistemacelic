# C4 Model: Diagrama de Containers (Nível 2)

Detalha as peças estruturais que compõem o SistemaCelic2. Como se trata de um monolito tradicional, a estrutura de containers é relativamente simples, focando na separação entre a aplicação, o banco de dados e o armazenamento de arquivos.

```mermaid
C4Container
    title Diagrama de Containers: SistemaCelic2

    Person(admin, "Administrador")
    Person(cliente, "Cliente")

    System_Boundary(celic2_boundary, "SistemaCelic2") {
        Container(web_app, "Aplicação Web Monolítica", "PHP / Laravel", "Fornece a interface HTML/Blade, expõe endpoints internos e executa toda a lógica de negócio.")
        ContainerDb(database, "Banco de Dados Relacional", "MySQL / MariaDB", "Armazena usuários, serviços, faturamentos, propostas e histórico de auditoria.")
        Container(file_storage, "Armazenamento Local", "File System (Uploads)", "Guarda comprovantes, laudos, boletos e relatórios ZIP/PDF temporários e persistentes.")
    }

    System_Ext(apis, "APIs Externas", "PlugNotas, n8n, Google Maps")

    Rel(admin, web_app, "Acessa interface administrativa", "HTTPS")
    Rel(cliente, web_app, "Acessa portal do cliente", "HTTPS")

    Rel(web_app, database, "Lê e escreve dados estruturados", "SQL / TCP")
    Rel(web_app, file_storage, "Salva arquivos de upload e gera PDFs", "I/O Local")
    Rel(web_app, apis, "Comunica-se com integrações de terceiros", "REST / HTTP POST")

    UpdateElementStyle(web_app, $fontColor="white", $bgColor="#438dd8", $borderColor="#2b5a8a")
    UpdateElementStyle(database, $fontColor="white", $bgColor="#f29c38", $borderColor="#b87221")
```
