<?php

namespace App\Http\Controllers;

use App\Models\Sheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PoisController extends Controller
{
    //creare api che torna carg.dev.maphub.it/api/pois.json
    //-> questo controller deve fare il merge di tutte le features presenti in ogni geojson "geologia punti"
    //nelle properties di ogni features devi inserire l'id del foglio sheet_id
    public function fetchAndTransformPois()
    {
        // feature collection URL
        $url = 'https://geohub.webmapp.it/api/v1/app/55/pois.geojson';

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
        $sheet->pois = $transformedFeature;
        $sheet->save();

        // Restituisco la Feature modificata come risposta JSON
        return response()->json($transformedFeature);
    }
}
