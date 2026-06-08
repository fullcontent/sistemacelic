<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$logPath = storage_path('logs/laravel.log');
if (!file_exists($logPath)) {
    // Try to find any log file
    $files = glob(storage_path('logs/*.log'));
    if (empty($files)) {
        echo "No log files found in storage/logs/";
        exit;
    }
    $logPath = end($files);
}

echo "Reading log: $logPath\n\n";
$content = file_get_contents($logPath);
preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \w+\.(ERROR|CRITICAL): (.*)/', $content, $matches);

if (!empty($matches[0])) {
    $lastError = end($matches[0]);
    echo "LATEST ERROR:\n$lastError\n\n";
    
    // Find the stack trace for the last error
    $pos = strrpos($content, $lastError);
    echo substr($content, $pos, 1000);
} else {
    echo "No obvious errors found with regex pattern.";
}
