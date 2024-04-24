<?php

namespace App\Http\Controllers;

use App\Models\Sheet;
use Illuminate\Support\Facades\DB;

class SheetController extends Controller
{
    public function get()
    {
        $relations = ['geologyPoints', 'geologyLines', 'geologyPolygons', 'geomorfologyPoints', 'geomorfologyLines', 'geomorfologyPolygons', 'resourceProspections'];

        $sheets = Sheet::with($relations)
            ->select(
                'id',
                'carg_code',
                DB::raw('ST_AsGeoJSON(geometry) as geojson')
            )->get();

        $features = $sheets
            ->map(function ($sheet) use ($relations) {
                $featureCollections = [];
                foreach ($relations as $relation) {
                    $featureCollection = $sheet->$relation;
                    if (isset($featureCollection) && isset($featureCollection->geojson_path)) {
                        $featureCollections[$relation] = url('api/' . $featureCollection->geojson_path);
                    }
                }

                return [
                    'type' => 'Feature',
                    'geometry' => json_decode($sheet->geojson),
                    'properties' => [
                        'id' => $sheet->id,
                        'carg_code' => $sheet->carg_code,
                        'featureCollections' => $featureCollections,
                    ],
                ];
            });

        $featureCollection = [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];

        return response()->json($featureCollection);
    }
}
