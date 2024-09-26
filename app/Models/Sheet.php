<?php

namespace App\Models;

use App\Models\FeatureCollection;
use App\Enums\FeatureCollectionType;
use App\Jobs\RestoreDefaultTilesJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'carg_code',
        'geometry',
        'file',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($sheet) {
            $isRasterDeleted = $sheet->isDirty('file') && $sheet->getOriginal('file') !== null && $sheet->file === null;

            if ($isRasterDeleted) {
                RestoreDefaultTilesJob::dispatch($sheet);
            }
        });
    }

    public function geologyPoints()
    {
        return $this->hasOne(FeatureCollection::class)->where('type', 'geology_points');
    }

    public function geologyLines()
    {
        return $this->hasOne(FeatureCollection::class)->where('type', 'geology_lines');
    }

    public function geologyPolygons()
    {
        return $this->hasOne(FeatureCollection::class)->where('type', 'geology_polygons');
    }

    public function geomorfologyPoints()
    {
        return $this->hasOne(FeatureCollection::class)->where('type', 'geomorfology_points');
    }

    public function geomorfologyLines()
    {
        return $this->hasOne(FeatureCollection::class)->where('type', 'geomorfology_lines');
    }

    public function geomorfologyPolygons()
    {
        return $this->hasOne(FeatureCollection::class)->where('type', 'geomorfology_polygons');
    }

    public function resourceProspections()
    {
        return $this->hasOne(FeatureCollection::class)->where('type', 'resource_prospections');
    }
}
