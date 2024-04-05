<?php

namespace App\Nova;

use App\Enums\FeatureCollectionType;
use App\Rules\ValidGeoJSON;
use Exception;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wm\MapMultiPolygon\MapMultiPolygon;

class FeatureCollection extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\FeatureCollection>
     */
    public static $model = \App\Models\FeatureCollection::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public $type = FeatureCollectionType::GeologyPoints->value;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Select::make('Type')
                ->options(FeatureCollectionType::asSelectArray())
                ->withMeta(['value' => $this->type])
                ->default($this->type),
            ...$this->jsonField($request),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    public function getGeojson($path)
    {
        // Assumi che il valore di $resource->features sia il percorso del file JSON.
        // Adatta questa logica se il percorso o il modo in cui salvi i file è diverso.
        $path = storage_path('app/public' . $path);
        try {
            if (file_exists($path)) {
                return file_get_contents($path);
            }
        } catch (Exception $e) {
            return '{}';
        }

        return '{}'; // Restituisci un oggetto JSON vuoto se il file non esiste.
    }

    public function jsonField(NovaRequest $request)
    {
        $geojson = $this->getGeojson($this->geojson_path);

        return [
            File::make('Carica JSON', 'geojson_path') // Usa 'geojson_path' invece di 'features'
                ->disk('public') // Assicurati di usare il disco corretto
                ->path('feature-collections')
                ->storeAs(function (Request $request) {
                    // Genera un nome di file univoco
                    return 'feature-collections-' . md5($request->geojson_path . microtime()) . '.json';
                })
                ->acceptedTypes('.json')
                ->hideFromIndex()
                ->hideFromDetail(),
            // formato SR predefinito: EPSG:4326 - WGS 84
            MapMultiPolygon::make('map')->withMeta([
                'center' => ['42.795977075', '10.326813853'],
                'attribution' => 'carg',
                'tiles' => 'https://tiles.webmapp.it/carg/{z}/{x}/{y}.png',
                'minZoom' => 6,
                'geojson' => $geojson,
            ])
                ->onlyOnDetail(),
            Code::make('Geojson', 'geojson_path')
                ->json()
                //->rules('required', 'json', new ValidGeoJSON)
                ->onlyOnDetail()
                ->hideFromDetail(function ($request) {
                    $isFeatureCollection = !strpos($request->headers->get('referer'), 'feature-collections/');

                    return $isFeatureCollection;
                })
                ->resolveUsing(function ($value, $resource, $attribute) {
                    // Assumi che il valore di $resource->features sia il percorso del file JSON.
                    // Adatta questa logica se il percorso o il modo in cui salvi i file è diverso.
                    $path = storage_path('app/public/' . $resource->geojson_path);
                    try {
                        if (file_exists($path)) {
                            return file_get_contents($path);
                        }
                    } catch (Exception $e) {
                        return '{}';
                    }

                    return '{}'; // Restituisci un oggetto JSON vuoto se il file non esiste.
                }),
        ];
    }
}
