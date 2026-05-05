<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "\nSERVICOS:\n";
echo implode(',', \Illuminate\Support\Facades\Schema::getColumnListing('servicos'));
echo "\nSERVICOS FINANCEIRO:\n";
echo implode(',', \Illuminate\Support\Facades\Schema::getColumnListing('servico_financeiros'));
