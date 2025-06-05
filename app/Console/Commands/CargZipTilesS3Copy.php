<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter; // Per il type hinting
use Throwable; // Per catturare qualsiasi tipo di eccezione/errore

class CargZipTilesS3Copy extends Command
{
    /**
     * The name and signature of the console command.
     * Permette di specificare i dischi sorgente e destinazione come opzioni.
     *
     * @var string
     */
    protected $signature = 'carg:copy-zip-tiles {--source-disk=cargziptiles_legacy} {--target-disk=s3_ispra}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy zip tiles from a source S3 bucket to a target S3 bucket';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sourceDiskName = $this->option('source-disk');
        $targetDiskName = $this->option('target-disk');

        $this->info("Attempting to copy files from disk '{$sourceDiskName}' to disk '{$targetDiskName}'.");
        $this->warn("Please ensure that the disk '{$sourceDiskName}' is configured in 'config/filesystems.php' to point to the source S3 bucket (e.g., cargziptiles.s3.eu-central-1.amazonaws.com) with appropriate credentials defined in your .env file (e.g., LEGACY_CARG_AWS_ACCESS_KEY_ID).");
        $this->warn("And that the disk '{$targetDiskName}' is configured for the client\'s target bucket (e.g., 's3_ispra').");

        if (!$this->confirm("Do you want to proceed with copying all files from '{$sourceDiskName}' to '{$targetDiskName}'? This might take a while and could overwrite existing files on the target if they have the same name.", true)) {
            $this->info('Operation cancelled by user.');
            return Command::SUCCESS;
        }

        try {
            $sourceDisk = Storage::disk($sourceDiskName);
            $targetDisk = Storage::disk($targetDiskName);

            $this->line("Fetching all files from source disk '{$sourceDiskName}'...");
            $allFiles = $sourceDisk->allFiles();

            if (empty($allFiles)) {
                $this->info("No files found in the source disk '{$sourceDiskName}'. Nothing to copy.");
                return Command::SUCCESS;
            }

            $this->info(count($allFiles) . " files found. Starting copy process...");

            $progressBar = $this->output->createProgressBar(count($allFiles));
            $progressBar->start();

            $copiedCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($allFiles as $filePath) {
                try {
                    $stream = $sourceDisk->readStream($filePath);
                    if ($stream === false) {
                        $this->error("\nFailed to read stream for file: {$filePath} from source disk '{$sourceDiskName}'. Skipping.");
                        $errorCount++;
                        $errors[] = "Read stream failed for: {$filePath}";
                        $progressBar->advance();
                        continue;
                    }

                    $success = $targetDisk->putStream($filePath, $stream);

                    if (is_resource($stream)) {
                        fclose($stream);
                    }

                    if ($success) {
                        $copiedCount++;
                    } else {
                        $this->error("\nFailed to write file: {$filePath} to target disk '{$targetDiskName}'.");
                        $errorCount++;
                        $errors[] = "Write failed for: {$filePath}";
                    }
                } catch (Throwable $e) {
                    $this->error("\nError copying file {$filePath}: " . $e->getMessage());
                    $errorCount++;
                    $errors[] = "Exception for {$filePath}: " . $e->getMessage();
                }
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->info("\n\nCopy process finished.");
            $this->info("Successfully copied files: {$copiedCount}");
            if ($errorCount > 0) {
                $this->warn("Failed to copy files: {$errorCount}");
                if ($this->option('verbose')) {
                    $this->warn("Specific errors:");
                    foreach ($errors as $err) {
                        $this->line("- " . $err);
                    }
                }
            }
        } catch (Throwable $e) {
            $this->error("\nAn error occurred during setup or listing files: " . $e->getMessage());
            $this->error("Please check your disk configurations for '{$sourceDiskName}' and '{$targetDiskName}', S3 permissions, and that the disks exist.");
            return Command::FAILURE;
        }

        return $errorCount > 0 ? Command::WARNING : Command::SUCCESS;
    }
}
