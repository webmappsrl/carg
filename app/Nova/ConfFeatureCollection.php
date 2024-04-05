<?php

namespace App\Nova;

use App\Enums\FeatureCollectionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Kongulov\NovaTabTranslatable\NovaTabTranslatable;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ConfFeatureCollection extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ConfFeatureCollection>
     */
    public static $model = \App\Models\ConfFeatureCollection::class;

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
        'id', 'name',
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
            Select::make('Type')
                ->options(FeatureCollectionType::asSelectArray())
                ->displayUsingLabels(),
            NovaTabTranslatable::make([
                Text::make(__('Label'), 'label'),
            ]),
            Color::make('Fill Color')->hideWhenCreating(),
            Color::make('Stroke Color'),
            Number::make('Stroke width')->hideWhenCreating(),
            Text::make('Icon', 'svg_path', function () {
                $url = Storage::disk('public')->url($this->svg_path);
                return "<object data='{$url}' width='300' height='300'></object>";
            })->asHtml()->onlyOnDetail(),
            File::make('SVG File', 'svg_path')
                ->disk('public')
                ->path('icons')
                ->storeAs(function (Request $request) {
                    return $request->type.'.svg';
                })
                ->rules('mimes:svg', 'max:1024'),
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
}
