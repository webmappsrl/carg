<?php

namespace App\Http\Controllers;

use App\Models\FeatureCollection;

class FeatureCollectionController extends Controller
{
    public function get($id)
    {
        $featureCollection = FeatureCollection::findOrFail($id);

        return response()->json($featureCollection);
    }
}
