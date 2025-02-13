<?php

namespace App\Models;

use App\Http\Controllers\FeatureCollectionController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ConfFeatureCollection extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['label'];

    protected $fillable = ['svg_path'];

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($confFeatureCollection) {
            $featureCollectionController = new FeatureCollectionController();
            $featureCollectionController->generateConf();
        });

        static::deleted(function ($confFeatureCollection) {
            $featureCollectionController = new FeatureCollectionController();
            $featureCollectionController->generateConf();
        });
    }
}
