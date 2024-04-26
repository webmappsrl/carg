<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Jobs\ProcessZipFromUrl;
use App\Models\Sheet;

class DispatchProcessZipFromUrl extends Action
{
    use Queueable;

    /**
     * Perform the action on the specified models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, \Illuminate\Support\Collection $models)
    {
        foreach ($models as $model) {
            // Assicurati che il modello sia un'istanza di Sheet
            if ($model instanceof Sheet) {
                // Dispatch the job with the file path from the model
                ProcessZipFromUrl::dispatch($model->file);
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
