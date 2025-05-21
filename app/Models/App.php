<?php

namespace App\Models;

use Wm\WmPackage\Models\App as WmApp;

class App extends WmApp
{
    protected static function boot()
    {
        // Intentionally left empty to bypass parent observer registration
    }
}
