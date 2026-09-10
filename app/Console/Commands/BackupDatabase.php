<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the database to a SQL file';

    public function handle()
    {
        $timestamp = now()->format('YmdHis');
        $backupFile = "backups/backup_{$timestamp}.sql";

        $this->info('Starting database backup...');

        $exitCode = \Artisan::call('db:dump', [
            '--output' => storage_path($backupFile),
        ]);

        if ($exitCode === 0) {
            $this->info('Database backup completed successfully.');
        } else {
            $this->error('Database backup failed.');
        }
    }
}