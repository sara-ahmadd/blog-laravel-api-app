<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RollbackMigration extends Command
{
    protected $signature = 'make:rollback-migration {filename}';

    protected $description = 'Rollback a specific migration file';

    public function handle(): int
    {
        $filename = $this->argument('filename');

        // Validate filename
        if (basename($filename) !== $filename || !str_ends_with($filename, '.php')) {
            $this->error('Invalid migration filename.');

            return self::FAILURE;
        }

        $path = 'database/migrations/' . $filename;

        // Check if migration exists
        if (!file_exists(base_path($path))) {
            $this->error('Migration file not found.');

            return self::FAILURE;
        }

        // Execute rollback
        return $this->call('migrate:rollback', [
            '--path' => $path,
        ]);
    }
}