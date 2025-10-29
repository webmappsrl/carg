<?php

namespace App\Nova;

use Ahmedkandel\NovaS3MultipartUpload\NovaS3MultipartUpload;
use App\Models\Sheet as ModelsSheet;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceDetailRequest;
use Wm\MapMultiPolygon\MapMultiPolygon;

class Sheet extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = ModelsSheet::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'carg_code';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'carg_code',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make(__('Carg code'), 'carg_code')
                ->sortable()
                ->rules('required', 'max:255'),
            NovaS3MultipartUpload::make('Zip Raster', 'file')
                ->keepOriginalName()
                ->disk('s3_ispra')
                ->server()
                ->restrictions([
                    'maxFileSize' => 1024 * 1024 * 1024,
                    'minFileSize' => 50 * 1024,
                    'maxTotalFileSize' => 10 * 1024 * 1024 * 1024,
                    'maxNumberOfFiles' => 1,
                    'minNumberOfFiles' => 1,
                    'allowedFileTypes' => [
                        '.zip',
                    ],
                ]),
            MapMultiPolygon::make(__('BBOX'), 'geometry')->withMeta([
                'center' => ['42.795977075', '10.326813853'],
                'attribution' => 'carg',
                'tiles' => 'https://carg.geosciences-ir.it/storage/cargmap/{z}/{x}/{y}.png',
                'minZoom' => 6,
            ]),
            HasOne::make(__('Geology Points'), 'geologyPoints', GeologyPointsFeatureCollection::class)
                ->showOnDetail(function (ResourceDetailRequest $request, ModelsSheet $sheet) {
                    return isset($sheet->geologyPoints);
                }),
            HasOne::make(__('Geology Lines'), 'geologyLines', GeologyLinesFeatureCollection::class)
                ->showOnDetail(function (ResourceDetailRequest $request, $sheet) {
                    return isset($sheet->geologyLines);
                }),
            HasOne::make(__('Geology Polygons'), 'geologyPolygons', GeologyPolygonsFeatureCollection::class)
                ->showOnDetail(function (ResourceDetailRequest $request, $sheet) {
                    return isset($sheet->geologyLines);
                }),
            HasOne::make(__('Geomorfology Points'), 'geomorfologyPoints', GeomorfologyPointsFeatureCollection::class)
                ->showOnDetail(function (ResourceDetailRequest $request, $sheet) {
                    return isset($sheet->geomorfologyPoints);
                }),
            HasOne::make(__('Geomorfology Lines'), 'geomorfologyLines', GeomorfologyLinesFeatureCollection::class)
                ->showOnDetail(function (ResourceDetailRequest $request, $sheet) {
                    return isset($sheet->geomorfologyLines);
                }),
            HasOne::make(__('Geomorfology Polygons'), 'geomorfologyPolygons', GeomorfologyPolygonsFeatureCollection::class)
                ->showOnDetail(function (ResourceDetailRequest $request, $sheet) {
                    return isset($sheet->geomorfologyPolygons);
                }),
            HasOne::make(__('Resource Prospections'), 'resourceProspections', ResourceProspectionsFeatureCollection::class)
                ->showOnDetail(function (ResourceDetailRequest $request, $sheet) {
                    return isset($sheet->resourceProspections);
                }),

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
        return [
            new Actions\DispatchProcessZipFromUrl,
            new Actions\DispatchProcessZipFromUrlReset,
        ];
    }
}
