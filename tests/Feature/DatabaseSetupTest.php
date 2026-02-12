<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class DatabaseSetupTest extends TestCase
{
    /**
     * Test that we can sync the production database to local.
     * 
     * @return void
     */
    public function test_sync_production_database()
    {
        // This test will trigger the sync process.
        // It relies on the environment variables defined in .env 
        // Or you can pass them as parameters here.

        $exitCode = Artisan::call('db:sync-prod', [
            '--p_host' => env('PROD_DB_HOST'),
            '--p_user' => env('PROD_DB_USERNAME'),
            '--p_pass' => env('PROD_DB_PASSWORD'),
            '--p_db' => env('PROD_DB_DATABASE'),
        ]);

        $this->assertEquals(0, $exitCode, "Failed to sync production database.");

        // Optionally, check if a table from production exists locally.
        // $this->assertTrue(\Schema::hasTable('users'));
    }
}
