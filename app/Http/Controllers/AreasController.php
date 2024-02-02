<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AreasController extends Controller
{

    //creare api carg.dev.maphub.it/api/areas.json
    // -> questo controller deve fare il merge di tutte le features presenti in ogni geojson "geologia poligoni"
    // nelle properties di ogni features devi inserire l'id del foglio sheet_id
    public function fetchAndTransformAreas()
    {
        // feature collection URL
        $url = //??; Non ho trovato url per geojson "geologia poligoni" (overlays?)

            $response = Http::get($url);
        $featureCollection = json_decode($response->body(), true);

        //da dove prendo id del foglio sheet_id? 
        //per il momento ne creo uno nuovo
        $sheet = Sheet::create();

        //aggiungo sheet_id a ogni feature
        $transformedFeature = [];
        foreach ($featureCollection['features'] as $feature) {
            $feature['properties']['sheet_id'] = $sheet->id;
            $transformedFeature[] = $feature;
        }

        //salvo nel database il json
        $sheet->areas = $transformedFeature;
        $sheet->save();

        // Restituisco la Feature modificata come risposta JSON
        return response()->json($transformedFeature);
    }
}
