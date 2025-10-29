<?php

namespace App\Console\Commands;

use App\Jobs\ProcessZipFromUrl;
use App\Models\Sheet;
use Illuminate\Console\Command;

class ProcessAllSheetsZip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sheets:process-all-zip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch ProcessZipFromUrl job for each Sheet with a ZIP file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sheets = Sheet::whereNotNull('file')->get();

        if ($sheets->isEmpty()) {
            $this->warn('Nessun Sheet trovato.');

            return 0;
        }

        $processedCount = 0;

        foreach ($sheets as $sheet) {
            try {
                ProcessZipFromUrl::dispatch($sheet->file);
                $processedCount++;
            } catch (\Exception $e) {
                $this->error("Errore Sheet {$sheet->id}: {$e->getMessage()}");
            }
        }

        $this->info("Job lanciati: {$processedCount}/{$sheets->count()}");

        return 0;
    }
}
