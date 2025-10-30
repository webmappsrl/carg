<?php

namespace App\Models;

use App\Enums\FeatureCollectionType;
use App\Jobs\ProcessZipFromUrl;
use App\Models\FeatureCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

        static::deleting(function ($sheet) {            
            if($sheet->file !== null) {
                ProcessZipFromUrl::dispatch($sheet->file, true);
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
