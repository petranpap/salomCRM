<?php

namespace App\Console\Commands;

use App\Support\TempPdf;
use Illuminate\Console\Command;

class PruneTempPdfs extends Command
{
    protected $signature = 'temp-pdfs:prune';

    protected $description = 'Delete expired print-preview PDFs from storage/app/temp-pdfs';

    public function handle(): int
    {
        TempPdf::sweep();

        $this->info('Expired temp PDFs pruned.');

        return self::SUCCESS;
    }
}
