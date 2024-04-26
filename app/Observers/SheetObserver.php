<?php

namespace App\Observers;

use App\Jobs\ProcessZipFromUrl;
use App\Models\Sheet;

class SheetObserver
{
    public function updated(Sheet $sheet)
    {
        if ($sheet->wasChanged('file')) {
            ProcessZipFromUrl::dispatch($sheet->file);
        }
    }
}
