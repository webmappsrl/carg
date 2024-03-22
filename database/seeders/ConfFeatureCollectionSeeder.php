<?php

namespace Database\Seeders;

use App\Enums\FeatureCollectionType;
use App\Models\ConfFeatureCollection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfFeatureCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ConfFeatureCollection::create([
            'type' => FeatureCollectionType::GeologyPoints->value,
            'label' => '{"it":"Geologia punti","en":"Geology points"}',
            'fill_color' => '#d9d9d91A',
            'stroke_color' => '##0000ff',
            'stroke_width' => 1,
        ]);
        ConfFeatureCollection::create([
            'type' => FeatureCollectionType::GeologyLines->value,
            'label' => '{"it":"Geologia linee","en":"Geology lines"}',
            'fill_color' => '#ff9900',
            'stroke_color' => '#0467A6',
            'stroke_width' => 5,
        ]);
        ConfFeatureCollection::create([
            'type' => FeatureCollectionType::GeologyPolygons->value,
            'label' => '{"it":"Geologia poligoni","en":"Geology poligons"}',
            'fill_color' => '#d9d9d91A',
            'stroke_color' => '#ff99001a',
            'stroke_width' => 5,
        ]);
        ConfFeatureCollection::create([
            'type' => FeatureCollectionType::GeomorfologyPoints->value,
            'label' => '{"it":"Geomorfologia punti","en":"Geomorfology points"}',
            'fill_color' => '#d9d9d91A',
            'stroke_color' => '##0000ff',
            'stroke_width' => 1,
        ]);
        ConfFeatureCollection::create([
            'type' => FeatureCollectionType::GeomorfologyLines->value,
            'label' => '{"it":"Geomorfology linee","en":"Geomorfology lines"}',
            'fill_color' => '#ff9900',
            'stroke_color' => '#0467A6',
            'stroke_width' => 5,
        ]);
        ConfFeatureCollection::create([
            'type' => FeatureCollectionType::GeomorfologyPolygons->value,
            'label' => '{"it":"Geomorfology poligoni","en":"Geomorfology poligons"}',
            'fill_color' => '#d9d9d91A',
            'stroke_color' => '#ff99001a',
            'stroke_width' => 5,
        ]);
        ConfFeatureCollection::create([
            'type' => FeatureCollectionType::ResourceProspections->value,
            'label' => '{"it":"Risorse prospezioni","en":"Resource prospection"}',
            'fill_color' => '#d9d9d91A',
            'stroke_color' => '#ff99001a',
            'stroke_width' => 5,
        ]);
    }
}
