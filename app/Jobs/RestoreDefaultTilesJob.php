<?php

namespace App\Jobs;

use App\Models\Sheet;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RestoreDefaultTilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected $sheet;

    public function __construct(Sheet $sheet)
    {
        $this->sheet = $sheet;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $logger = Log::channel('rasters');
        $tgen = Storage::disk('tgen');
        $blankmap = Storage::disk('blankmap');
        $s3 = Storage::disk('s3');

        $dirName = Str::before($this->sheet->file, '.zip');

        if (!$dirName) {
            $logger->error('Directory name not found');
            return; // Esce dal job se il nome della directory non è valido
        }

        // Controlla se la directory esiste nel disco tgen
        if (!$tgen->exists($dirName)) {
            $logger->error('Directory ' . $dirName . ' not found in tgen disk');
            return; // Esce dal job se la directory non esiste
        }

        try {
            // Ottieni tutti i files nella directory del disco tgen
            $files = $tgen->allFiles($dirName);

            foreach ($files as $relativePath) {

                if (strpos($relativePath, '..') !== false) {
                    $logger->warning("Percorso non valido rilevato: $relativePath");
                    continue; // Salta i percorsi potenzialmente problematici
                }

                $sourcePath = $relativePath; // Percorso relativo nel disco tgen
                $blankmapPath = str_replace($dirName . '/', '', $relativePath); // Percorso relativo nel disco blankmap

                // Verifica se il file di default esiste nella mappa muta (blankmap)
                if ($blankmap->exists($blankmapPath)) {
                    // Assicurati che la directory di destinazione esista su tgen
                    $tgen->makeDirectory(dirname($sourcePath));

                    // Copia il file dalla mappa muta (blankmap) al percorso attuale su tgen
                    $tgen->put($sourcePath, $blankmap->get($blankmapPath));
                    $logger->info("Sostituito: $sourcePath con file da mappa muta: $blankmapPath");
                } else {
                    $logger->error("File mappa muta non trovato per: $relativePath");
                }
            }

            // Cancellazione dello zip originale
            if ($s3->exists($this->sheet->file)) {
                $logger->info("Cancellazione dello zip originale su S3: {$this->sheet->file} ...");
                $s3->delete($this->sheet->file);
            }

            $this->sheet->file = null;
            $this->sheet->save();
            $logger->info("Cancellazione dello zip originale su S3 completata");
        } catch (\Exception $e) {
            // Registra l'errore nel log
            $logger->error('Errore durante l\'iterazione dei file: ' . $e->getMessage());
            throw $e;
        }
    }
}
