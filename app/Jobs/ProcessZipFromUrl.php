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
    protected $reset;

    public function __construct($zipUrl, $reset = false)
    {
        $this->zipUrl = $zipUrl;
        $this->reset = $reset;
    }

    public function handle()
    {
        $logger = Log::channel('rasters');
        $tempZipPath = tempnam(sys_get_temp_dir(), 'zip');

        Log::info("Processing ZIP file: {$this->zipUrl}");
        $logger->info("Attempting to download file from S3: {$this->zipUrl}");

        try {
            // Check if file exists on S3
            if (!Storage::disk('s3_ispra')->exists($this->zipUrl)) {
                $logger->error("File does not exist on S3: {$this->zipUrl}");
                return;
            }

            // Download file directly from S3 using Storage facade
            $zipContent = Storage::disk('s3_ispra')->get($this->zipUrl);

            if ($zipContent === false || $zipContent === null) {
                $logger->error("Failed to download file content from S3: {$this->zipUrl}");
                return;
            }

            file_put_contents($tempZipPath, $zipContent);
            $logger->info("File downloaded successfully to temp path: {$tempZipPath}");
        } catch (Exception $e) {
            $logger->error("Exception while downloading file from S3: {$e->getMessage()}");
            return;
        }

        $zip = new ZipArchive;

        if ($zip->open($tempZipPath) === true) {
            $tempDir = storage_path('app/tempZip/' . uniqid());
            $zip->extractTo($tempDir);
            $zip->close();
            $logger->info("temp directory: {$tempDir}");

            // Se reset è true, elimina i file, altrimenti fai il merge
            if ($this->reset) {
                $logger->info("Reset mode: deleting files from cargmap disk");
                $this->deleteContents($tempDir, Storage::disk('cargmap'));
            } else {
                $logger->info("Merge mode: copying files to cargmap disk");
                $this->mergeContents($tempDir, Storage::disk('cargmap'));
            }

            Storage::deleteDirectory($tempDir);
        } else {
            Log::error("Unable to open the ZIP file from URL: {$zipUrlPath}");
        }

        unlink($tempZipPath);
    }

    protected function mergeContents($sourceDir, $disk)
    {
        $logger = Log::channel('rasters');
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $logger->info("mergeContents sourceDir: {$sourceDir}");
        foreach ($files as $fileInfo) {
            $relativePath = str_replace('Mapnik' . DIRECTORY_SEPARATOR, '', $files->getSubPathName());
            $logger->info("mergeContents relativePath: {$relativePath}");
            if ($fileInfo->isDir() && ! is_numeric(basename($relativePath))) {
                continue;
            }
            if ($fileInfo->isDir()) {
                $logger->info("makeDirectory: {$relativePath}");
                $disk->makeDirectory($relativePath, 0755, true);
            } else {
                Log::info("file extension: {$fileInfo->getExtension()}");
                // Verifica che l'estensione sia '.png'
                if ($fileInfo->getExtension() === 'png') {
                    // Leggi il file dalla directory sorgente
                    $contents = file_get_contents($fileInfo->getRealPath());

                    // Assicurati che la directory di destinazione esista (crea se non esiste)
                    $directoryPath = dirname($relativePath);
                    if (! $disk->exists($directoryPath)) {
                        $logger->info("makeDirectory: {$directoryPath}");
                        $disk->makeDirectory($directoryPath, 0755, true);  // Assicurati che il driver supporti questa operazione
                    }
                    try {
                        // Usa Storage facade per scrivere il file nel disco configurato
                        $disk->put($relativePath, $contents);
                    } catch (Exception $e) {
                        $logger->error("ERROR: {$e->getMessage()}");
                    }
                }
            }
        }
    }

    protected function deleteContents($sourceDir, $disk)
    {
        $logger = Log::channel('rasters');
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $logger->info("deleteContents sourceDir: {$sourceDir}");

        foreach ($files as $fileInfo) {
            $relativePath = str_replace('Mapnik' . DIRECTORY_SEPARATOR, '', $files->getSubPathName());
            $logger->info("deleteContents relativePath: {$relativePath}");

            // Salta le directory non numeriche come fa mergeContents
            if ($fileInfo->isDir() && ! is_numeric(basename($relativePath))) {
                continue;
            }

            // Se è un file con estensione .png, eliminalo dal disco
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'png') {
                if ($disk->exists($relativePath)) {
                    try {
                        $disk->delete($relativePath);
                        $logger->info("Deleted file: {$relativePath}");
                    } catch (Exception $e) {
                        $logger->error("Error deleting file {$relativePath}: {$e->getMessage()}");
                    }
                } else {
                    $logger->info("File does not exist (skipping): {$relativePath}");
                }
            }
        }

        $logger->info("Delete operation completed");
    }
}
