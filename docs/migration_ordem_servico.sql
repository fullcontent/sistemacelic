-- 1. Renomeando as tabelas principais
RENAME TABLE `ordem_compras` TO `ordem_servicos`;
RENAME TABLE `ordem_compra_pagamentos` TO `ordem_servico_pagamentos`;
RENAME TABLE `ordem_compra_vinculos` TO `ordem_servico_vinculos`;

-- 2. Alterando os nomes das colunas de chave estrangeira
ALTER TABLE `ordem_servico_pagamentos` CHANGE `ordemCompra_id` `ordemServico_id` bigint(20) unsigned NOT NULL;
ALTER TABLE `ordem_servico_vinculos` CHANGE `ordemCompra_id` `ordemServico_id` bigint(20) unsigned NOT NULL;
ALTER TABLE `prestador_comentarios` CHANGE `ordemCompra_id` `ordemServico_id` bigint(20) unsigned DEFAULT NULL;

-- 3. Caso existam chaves estrangeiras com nome antigo, você pode precisar dropá-las antes e recriá-las. 
-- Em alguns SGBDs (como versões recentes do MySQL e MariaDB) o MySQL mantém a constraint com o nome antigo ou a atualiza.
-- Se desejar atualizar o nome da constraint de Foreign Key explicitamente (substitua os nomes conforme o banco):
-- ALTER TABLE `ordem_servico_pagamentos` DROP FOREIGN KEY `ordem_compra_pagamentos_ordemcompra_id_foreign`;
-- ALTER TABLE `ordem_servico_pagamentos` ADD CONSTRAINT `ordem_servico_pagamentos_ordemservico_id_foreign` FOREIGN KEY (`ordemServico_id`) REFERENCES `ordem_servicos` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `ordem_servico_vinculos` DROP FOREIGN KEY `ordem_compra_vinculos_ordemcompra_id_foreign`;
-- ALTER TABLE `ordem_servico_vinculos` ADD CONSTRAINT `ordem_servico_vinculos_ordemservico_id_foreign` FOREIGN KEY (`ordemServico_id`) REFERENCES `ordem_servicos` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `prestador_comentarios` DROP FOREIGN KEY `prestador_comentarios_ordemcompra_id_foreign`;
-- ALTER TABLE `prestador_comentarios` ADD CONSTRAINT `prestador_comentarios_ordemservico_id_foreign` FOREIGN KEY (`ordemServico_id`) REFERENCES `ordem_servicos` (`id`) ON DELETE CASCADE;
