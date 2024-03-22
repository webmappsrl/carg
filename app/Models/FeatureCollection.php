<?php

namespace App\Models;

use App\Enums\FeatureCollectionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureCollection extends Model
{
    use HasFactory;

    protected $default_type = null;

    protected $fillable = ['sheet_id', 'type', 'geojson'];

    protected $casts = [
        'type' => FeatureCollectionType::class,
        'geojson' => 'array',
    ];

    public function sheet()
    {
        return $this->belongsTo(Sheet::class);
    }

    public function setDefaultType($type)
    {
        $this->default_type = $type;
    }

    public function getDefaultType()
    {
        return $this->default_type;
    }
}
