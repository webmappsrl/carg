<?php

namespace App\Observers;

use App\Http\Controllers\FeatureCollectionController;
use Wm\WmPackage\Models\App;

class AppFeatureCollectionConfigObserver
{
    /**
     * Handle the App "saved" event.
     */
    public function saved(App $app): void
    {
        $featureCollectionController = new FeatureCollectionController;
        $featureCollectionController->generateConf();
    }
}
