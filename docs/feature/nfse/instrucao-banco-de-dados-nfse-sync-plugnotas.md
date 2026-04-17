# Instrucoes de Banco de Dados - Sincronizacao de Empresa NFSe com PlugNotas

## Objetivo

Documentar as alteracoes de banco necessarias para o fluxo de sincronizacao de empresas NFSe com a PlugNotas, considerando execucao manual (sem migrations).

## Escopo desta entrega

Ajuste de persistencia do status de sincronizacao na tabela `nfse_configurations`.

### Tabelas alteradas

- `nfse_configurations` (ALTER TABLE)

### Tabelas verificadas sem alteracao nesta entrega

- `dados_castros` (sem novas colunas, sem alteracao de tipo, sem indices novos neste ajuste)

Observacao: existe migration historica antiga para `dados_castros` no projeto (`2026_04_09_193032_add_ativo_to_dados_castros_table.php`), mas ela nao faz parte deste ajuste de sincronizacao PlugNotas.

## Script SQL manual (idempotente)

Executar no banco alvo (local/producao), preferencialmente em janela de manutencao.

```sql
-- 1) Coluna booleana de status de sincronizacao
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.columns
      WHERE table_schema = DATABASE()
        AND table_name = 'nfse_configurations'
        AND column_name = 'plugnotas_empresa_sincronizada'
    ),
    'SELECT ''coluna plugnotas_empresa_sincronizada ja existe''',
    'ALTER TABLE nfse_configurations ADD COLUMN plugnotas_empresa_sincronizada TINYINT(1) NOT NULL DEFAULT 0'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2) Coluna de data/hora da ultima sincronizacao
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.columns
      WHERE table_schema = DATABASE()
        AND table_name = 'nfse_configurations'
        AND column_name = 'plugnotas_empresa_sync_at'
    ),
    'SELECT ''coluna plugnotas_empresa_sync_at ja existe''',
    'ALTER TABLE nfse_configurations ADD COLUMN plugnotas_empresa_sync_at DATETIME NULL'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3) Coluna para ultima mensagem de erro
SET @sql := (
  SELECT IF(
    EXISTS(
      SELECT 1
      FROM information_schema.columns
      WHERE table_schema = DATABASE()
        AND table_name = 'nfse_configurations'
        AND column_name = 'plugnotas_empresa_sync_error'
    ),
    'SELECT ''coluna plugnotas_empresa_sync_error ja existe''',
    'ALTER TABLE nfse_configurations ADD COLUMN plugnotas_empresa_sync_error TEXT NULL'
  )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
```

## Validacao pos-execucao

```sql
DESCRIBE nfse_configurations;
```

Esperado:

- `plugnotas_empresa_sincronizada` -> `tinyint(1)` default `0`
- `plugnotas_empresa_sync_at` -> `datetime` nullable
- `plugnotas_empresa_sync_error` -> `text` nullable

Consulta focada:

```sql
SELECT
  column_name,
  column_type,
  is_nullable,
  column_default
FROM information_schema.columns
WHERE table_schema = DATABASE()
  AND table_name = 'nfse_configurations'
  AND column_name IN (
    'plugnotas_empresa_sincronizada',
    'plugnotas_empresa_sync_at',
    'plugnotas_empresa_sync_error'
  )
ORDER BY column_name;
```

## Rollback manual

```sql
ALTER TABLE nfse_configurations
  DROP COLUMN plugnotas_empresa_sync_error,
  DROP COLUMN plugnotas_empresa_sync_at,
  DROP COLUMN plugnotas_empresa_sincronizada;
```

## Confirmacao sobre dados_castros

Para este ajuste especifico de sincronizacao com PlugNotas:

- Nao houve alteracao de estrutura em `dados_castros`.
- Nenhuma coluna nova foi adicionada.
- Nenhum indice novo foi criado.
- Nenhum dado foi migrado entre `dados_castros` e outras tabelas.

## Referencias tecnicas no codigo

- Controller de sincronizacao: `app/Http/Controllers/NfseController.php`
- Servico de integracao: `app/Services/NfseService.php`
- Model com novos campos: `app/Models/NfseConfiguration.php`
- Migration criada para o ajuste (opcional): `database/migrations/2026_04_17_120000_add_plugnotas_sync_status_to_nfse_configurations.php`
