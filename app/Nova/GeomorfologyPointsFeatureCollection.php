<?php

namespace App\Nova;

use App\Enums\FeatureCollectionType;
use Illuminate\Http\Request;

class GeomorfologyPointsFeatureCollection extends FeatureCollection
{
    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $this->type = FeatureCollectionType::GeomorfologyPoints->value;

        return parent::fields($request);
    }
}
