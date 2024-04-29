<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use ZipArchive;

class ProcessZipFromUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected $zipUrl;

    public function __construct($zipUrl)
    {
        $this->zipUrl = $zipUrl;
    }

    public function handle()
    {
        $prefix = 'https://cargziptiles.s3.eu-central-1.amazonaws.com/';
        $tempZipPath = tempnam(sys_get_temp_dir(), 'zip');
        $zipUrlPath = $prefix . $this->zipUrl;

        Log::info("Attempting to open URL: {$zipUrlPath}");
        $zipFileStream = @fopen($zipUrlPath, 'r');

        if ($zipFileStream === false) {
            Log::error("Failed to open file at: {$zipUrlPath}");

            return;
        }

        file_put_contents($tempZipPath, $zipFileStream);
        fclose($zipFileStream);

        $zip = new ZipArchive;

        if ($zip->open($tempZipPath) === true) {
            $tempDir = storage_path('app/tempZip/' . uniqid());
            $zip->extractTo($tempDir);
            $zip->close();

            // Continua con la tua logica di unione dei contenuti...
            $this->mergeContents($tempDir, Storage::disk('tgen'));
            Storage::deleteDirectory($tempDir);
        } else {
            Log::error("Unable to open the ZIP file from URL: {$zipUrlPath}");
        }

        unlink($tempZipPath);
    }

    public function handle2()
    {
        $tempZipPath = tempnam(sys_get_temp_dir(), 'zip');
        file_put_contents($tempZipPath, fopen($this->zipUrl, 'r'));
        $zip = new ZipArchive;

        if ($zip->open($tempZipPath) === true) {
            // Estrai lo ZIP in una directory temporanea
            $tempDir = storage_path('app/tempZip');
            $zip->extractTo($tempDir);
            $zip->close();

            // Merge dei contenuti dello ZIP con la cartella 'tiles'
            $this->mergeContents($tempDir, Storage::disk('tgen'));

            // Pulizia: rimuovi il file ZIP temporaneo
            unlink($tempZipPath);

            // Pulizia: rimuovi la directory temporanea
            Storage::deleteDirectory('tempZip');
        } else {
            Log::error("Unable to open the ZIP file from URL: {$this->zipUrl}");
        }
    }

    protected function mergeContents($sourceDir, $disk)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $fileInfo) {
            $relativePath = str_replace('Mapnik' . DIRECTORY_SEPARATOR, '', $files->getSubPathName());
            if ($fileInfo->isDir() && !is_numeric(basename($relativePath))) {
                continue;
            }
            if ($fileInfo->isDir()) {
                // Tentativo di creare la directory, ignora se esiste già (il driver FTP potrebbe non supportare 'exists')
                $disk->makeDirectory($relativePath);
            } else {
                // Verifica che l'estensione sia '.png'
                if ($fileInfo->getExtension() === 'png') {
                    // Leggi il file dalla directory sorgente
                    $contents = file_get_contents($fileInfo->getRealPath());

                    // Assicurati che la directory di destinazione esista (crea se non esiste)
                    $directoryPath = dirname($relativePath);
                    if (!$disk->exists($directoryPath)) {
                        $disk->makeDirectory($directoryPath);  // Assicurati che il driver supporti questa operazione
                    }

                    // Usa Storage facade per scrivere il file nel disco configurato
                    $disk->put($relativePath, $contents);
                }
            }
        }
    }

    protected function mergeContents2($sourceDir, $targetDir)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $fileInfo) {
            // Ottieni il percorso relativo senza la cartella "Mapnik"
            $relativePath = str_replace('Mapnik' . DIRECTORY_SEPARATOR, '', $files->getSubPathName());

            // Costruisci il percorso di destinazione
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $relativePath;

            // Se è una directory e non è numerica, continua con il prossimo file
            if ($fileInfo->isDir() && !is_numeric(basename($relativePath))) {
                continue;
            }

            // Crea la directory di destinazione se non esiste
            if ($fileInfo->isDir() && !file_exists($targetPath)) {
                mkdir($targetPath, 0755, true);
                continue;
            }

            // Per i file, verifica che l'estensione sia '.png'
            if (!$fileInfo->isDir() && $fileInfo->getExtension() !== 'png') {
                continue;
            }

            // Assicurati che la directory di destinazione esista
            if (!$fileInfo->isDir()) {
                $directoryPath = dirname($targetPath);
                if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }

                // Copia il file nella destinazione
                if (!copy($fileInfo->getRealPath(), $targetPath)) {
                    Log::error("Failed to copy {$fileInfo->getRealPath()} to {$targetPath}");
                }
            }
        }
    }
}
