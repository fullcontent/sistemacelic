<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['nfse_emissions', 'nfse_emission_items'];

foreach ($tables as $table) {
    echo "TABLE: $table\n";
    try {
        $cols = DB::select("DESCRIBE $table");
        foreach ($cols as $col) {
            echo "  {$col->Field} - {$col->Type}\n";
        }
    } catch (Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
