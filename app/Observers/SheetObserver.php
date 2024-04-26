<?php

namespace App\Observers;

use App\Models\Sheet;
use App\Jobs\ProcessZipFromUrl;

class SheetObserver
{
    public function updated(Sheet $sheet)
    {
        if ($sheet->wasChanged('file')) {
            ProcessZipFromUrl::dispatch($sheet->file);
        }
    }
}
