<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Abstracts\UgcController;
use Wm\WmPackage\Models\UgcPoi;

class UgcPoiController extends UgcController
{
    protected function getModelIstance(): UgcPoi
    {
        return new UgcPoi;
    }
}
