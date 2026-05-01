# Arquitetura do Sistema — SistemaCelic2

Este documento descreve a visão geral da arquitetura do SistemaCelic2, abrangendo padrões, integrações e dívidas técnicas identificadas.

## Visão Geral
O SistemaCelic2 é uma aplicação monolitica baseada no framework **Laravel (PHP)**, seguindo o padrão MVC (Model-View-Controller). A interface é renderizada no servidor utilizando **Blade Templates**.

### Pilares Tecnológicos
- **Backend:** PHP 7.x/8.x com Laravel.
- **Banco de Dados:** MySQL/MariaDB.
- **Frontend:** Blade, jQuery, CSS Vanilla.
- **Processamento de Documentos:** Dompdf (PDF), PDFMerger, ZipArchive.
- **Integração:** n8n (Webhooks/E-mail), Google Maps (Geolocalização).

## Estilos Arquiteturais
- **Monolito Modular:** O sistema é organizado em módulos lógicos (Auth, Faturamento, OS), mas compartilha a mesma base de código e banco de dados.
- **Persistence-Driven Stage:** Muitas transições de estado são inferidas diretamente do banco de dados ou de logs de auditoria (`Historico`), em vez de uma máquina de estados formal no código.

## Integrações Externas
O sistema atua como um hub centralizando dados de múltiplas APIs:

| Sistema | Propósito | Protocolo |
| :--- | :--- | :--- |
| **n8n** | Orquestração de notificações e e-mails | Webhook (HTTP POST) |
| **Google Maps** | StreetView e Geocodificação de Unidades | API REST |
| **PositionStack** | Backup para Geocodificação | API REST |
| **PlugNotas** | Emissão de Notas Fiscais de Serviço (NFS-e) | API REST |

## Dívidas Técnicas Identificadas

### 1. Fat Controllers (Controllers Obesos)
Controladores como `AdminController` (> 1600 linhas) e `FaturamentoController` concentram muita lógica de negócio, dificultando a manutenção e testes unitários.
- **Recomendação:** Extrair lógica para `Services` ou `Actions`.

### 2. Lógica de Estado Frágil
A dependência de strings exatas na tabela `historicos` (ex: `"Alterou situacao para \"finalizado\""`) para determinar marcos de processo é arriscada. Alterações simples no texto do log podem quebrar funcionalidades de relatório.
- **Recomendação:** Implementar campos de data específicos ou uma tabela de transição formal.

### 3. Acoplamento com o Sistema de Arquivos
O uso de `public_path()` e manipulação direta de arquivos via `File` ou `ZipArchive` dificulta a escalabilidade horizontal (ex: migração para S3).
- **Recomendação:** Abstrair via Laravel `Storage` (Filesystem).

### 4. Consultas Pesadas em Views
Algumas views realizam consultas ou cálculos pesados que poderiam ser pré-processados ou cacheados.

## Escala de Confiança
- **Estilo Arquitetural:** 🟢 CONFIRMADO
- **Integrações:** 🟢 CONFIRMADO (via código e segredos encontrados)
- **Dívidas Técnicas:** 🟢 CONFIRMADO (evidenciado pelo tamanho dos arquivos e padrões de consulta)
