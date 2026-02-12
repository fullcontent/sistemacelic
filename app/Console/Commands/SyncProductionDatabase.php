<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Process as SymfonyProcess;

class SyncProductionDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:sync-prod 
                            {--p_host= : Production Host} 
                            {--p_user= : Production Username} 
                            {--p_pass= : Production Password} 
                            {--p_db= : Production Database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps production database and restores it into the local database configured in .env';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $localConfig = config('database.connections.mysql');

        $prodHost = $this->option('p_host') ?: env('PROD_DB_HOST');
        $prodUser = $this->option('p_user') ?: env('PROD_DB_USERNAME');
        $prodPass = $this->option('p_pass') ?: env('PROD_DB_PASSWORD');
        $prodDb = $this->option('p_db') ?: env('PROD_DB_DATABASE');

        if (!$prodHost || !$prodUser || !$prodDb) {
            $this->error('Production credentials are missing. Please provide them via options or set PROD_DB_* in your .env');
            return 1;
        }

        $this->info("Starting dump from production: {$prodDb}@{$prodHost}");

        $tempFile = storage_path('app/temp_prod_dump.sql');

        // Step 1: Dump production
        $this->info("Executing dump...");

        $dumpProcess = new SymfonyProcess([
            'mysqldump',
            "--host={$prodHost}",
            "--user={$prodUser}",
            "--password={$prodPass}",
            '--column-statistics=0',
            $prodDb,
            '--result-file=' . $tempFile
        ]);

        // Alternatively, use env for password if it still fails due to CLI length/chars
        // $dumpProcess->setEnv(['MYSQL_PWD' => $prodPass]);

        $dumpProcess->setTimeout(300);
        $dumpProcess->run();

        if (!$dumpProcess->isSuccessful()) {
            $this->error("Failed to dump production database.");
            $this->error($dumpProcess->getErrorOutput());
            return 1;
        }

        $this->info("Dump completed. Restoring to local database: {$localConfig['database']}");

        // Step 2: Restore local
        // Using fromShellCommandline to allow shell redirection (<) without loading file into PHP memory
        $restoreCmd = sprintf(
            'mysql --host=%s --user=%s --password=%s %s < %s',
            escapeshellarg($localConfig['host']),
            escapeshellarg($localConfig['username']),
            escapeshellarg($localConfig['password']),
            escapeshellarg($localConfig['database']),
            escapeshellarg($tempFile)
        );

        $this->info("Restoring database...");
        $restoreProcess = SymfonyProcess::fromShellCommandline($restoreCmd);
        $restoreProcess->setTimeout(600); // 10 minutes
        $restoreProcess->run();

        if (!$restoreProcess->isSuccessful()) {
            $this->error("Failed to restore local database.");
            $this->error($restoreProcess->getErrorOutput());
            return 1;
        }

        $this->info("Database sync successful!");

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        return 0;
    }
}
