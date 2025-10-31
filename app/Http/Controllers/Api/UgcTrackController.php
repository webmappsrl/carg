<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Abstracts\UgcController;
use Wm\WmPackage\Models\UgcTrack;

class UgcTrackController extends UgcController
{
    protected function getModelIstance(): UgcTrack
    {
        return new UgcTrack;
    }
}
