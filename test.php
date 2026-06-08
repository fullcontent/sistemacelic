<?php require 'vendor/autoload.php'; require 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo \App\Models\NfseConfiguration::first()->inscricao_municipal;
