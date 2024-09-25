<?php

namespace App\Nova\Actions;

use App\Jobs\RestoreDefaultTilesJob;
use App\Models\Sheet;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;

class RestoreDefaultTiles extends Action
{
    use Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, \Illuminate\Support\Collection $models)
    {
        foreach ($models as $model) {

            if ($model instanceof Sheet) {
                RestoreDefaultTilesJob::dispatch($model);
            }
        }

        return Action::message('Processo di caricamento avviato!');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
