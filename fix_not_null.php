<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$statements = [
    "ALTER TABLE nfse_configurations MODIFY COLUMN intermediario_tipo VARCHAR(255) DEFAULT 'Intermediario nao informado'",
    "ALTER TABLE nfse_configurations MODIFY COLUMN local_prestacao VARCHAR(255) DEFAULT 'Brasil'",
    "ALTER TABLE nfse_configurations MODIFY COLUMN municipio_ibge VARCHAR(255) NULL"
];

foreach ($statements as $sql) {
    try {
        DB::statement($sql);
        echo "Success: $sql\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
