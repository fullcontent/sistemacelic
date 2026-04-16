<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$statements = [
    "CREATE TABLE IF NOT EXISTS `nfse_configurations` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `dados_castro_id` int(10) unsigned DEFAULT NULL,
        `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'plugnotas',
        `emit_as` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `simples_regime` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `tomador_tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `intermediario_tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `local_prestacao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Brasil',
        `municipio_nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `municipio_ibge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `codigo_tributacao_nacional` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `suspensao_exigibilidade_issqn` tinyint(1) NOT NULL DEFAULT '0',
        `item_nbs` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `issqn_exigibilidade_suspensa` tinyint(1) NOT NULL DEFAULT '0',
        `issqn_retido` tinyint(1) NOT NULL DEFAULT '0',
        `beneficio_municipal` tinyint(1) NOT NULL DEFAULT '0',
        `pis_cofins_situacao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `aliquota_simples` decimal(6,2) NOT NULL DEFAULT '9.90',
        `valor_aproximado_tributos` decimal(10,2) DEFAULT NULL,
        `ativo` tinyint(1) NOT NULL DEFAULT '1',
        `inscricao_municipal` varchar(255) NULL,
        `email_emitente` varchar(255) NULL,
        `telefone_emitente` varchar(255) NULL,
        `logradouro` varchar(255) NULL,
        `numero` varchar(255) NULL,
        `bairro` varchar(255) NULL,
        `codigo_cidade` varchar(255) DEFAULT '4202008',
        `cep` varchar(255) NULL,
        `uf` varchar(255) DEFAULT 'SC',
        `regime_tributario` int DEFAULT 1,
        `login_prefeitura` varchar(255) NULL,
        `senha_prefeitura` varchar(255) NULL,
        `certificado` varchar(255) NULL,
        `producao` tinyint(1) DEFAULT 0,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_nfse_config_dados_ativo` (`dados_castro_id`,`ativo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "CREATE TABLE IF NOT EXISTS `nfse_emissions` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `faturamento_id` int(10) unsigned NOT NULL,
        `nfse_configuration_id` int(10) unsigned NOT NULL,
        `modo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `opcao_automatica` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processando',
        `payload` longtext COLLATE utf8mb4_unicode_ci,
        `retorno` longtext COLLATE utf8mb4_unicode_ci,
        `zip_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `observacoes` text COLLATE utf8mb4_unicode_ci,
        `mensagem_erro` text NULL,
        `valor_total` decimal(14,2) DEFAULT '0.00',
        `pdf_url` text NULL,
        `xml_url` text NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "CREATE TABLE IF NOT EXISTS `nfse_emission_items` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `nfse_emission_id` int(10) unsigned NOT NULL,
        `servico_id` int(10) unsigned DEFAULT NULL,
        `faturamento_servico_id` int(10) unsigned DEFAULT NULL,
        `cnpj_tomador` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `descricao_servico` text COLLATE utf8mb4_unicode_ci,
        `valor_servico` decimal(14,2) DEFAULT NULL,
        `numero_nf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `external_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
        `pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `xml_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `mensagem_erro` text COLLATE utf8mb4_unicode_ci,
        `additional_data` longtext COLLATE utf8mb4_unicode_ci,
        `pdf_url` text NULL,
        `xml_url` text NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_nfse_items_servico_status` (`servico_id`,`status`),
        KEY `idx_nfse_items_external_id` (`external_id`),
        CONSTRAINT `nfse_emission_items_nfse_emission_id_foreign` FOREIGN KEY (`nfse_emission_id`) REFERENCES `nfse_emissions` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

foreach ($statements as $sql) {
    try {
        DB::statement($sql);
        echo "Executed: " . substr($sql, 0, 50) . "...\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
