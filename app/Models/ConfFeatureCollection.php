<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ConfFeatureCollection extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['label'];
    protected $fillable = ['svg_path'];
}
