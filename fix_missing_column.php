<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement("ALTER TABLE nfse_emissions ADD COLUMN numero_nf VARCHAR(255) NULL AFTER xml_url");
    echo "Success: numero_nf added to nfse_emissions.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
