<?php

namespace App\Nova\Actions;

use App\Jobs\ProcessZipFromUrl;
use App\Models\Sheet;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class DispatchProcessZipFromUrlReset extends Action
{
    use Queueable;

    public $name = 'Reset Zip (Delete Files)';

    /**
     * Perform the action on the specified models.
     *
     * @param  ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, \Illuminate\Support\Collection $models)
    {
        foreach ($models as $model) {
            // Assicurati che il modello sia un'istanza di Sheet
            if ($model instanceof Sheet) {
                // Dispatch the job with the file path from the model and reset = true
                ProcessZipFromUrl::dispatch($model->file, true);
            }
        }

        return Action::message('Processo di eliminazione avviato!');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}

