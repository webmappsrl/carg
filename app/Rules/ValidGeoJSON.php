<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidGeoJSON implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Prima, tenta di decodificare il JSON fornito
        $data = json_decode($value, true);

        // Verifica se json_decode ha restituito un errore
        if (json_last_error() !== JSON_ERROR_NONE) {
            $fail('Il campo :attribute non è un JSON valido.');

            return;
        }

        // Implementa qui la logica specifica per verificare se è una FeatureCollection GeoJSON valida
        // Questo è un esempio basilare. Potresti voler aggiungere controlli più rigorosi.
        if (! isset($data['type']) || $data['type'] !== 'FeatureCollection') {
            $fail('Il campo :attribute deve essere di tipo FeatureCollection.');

            return;
        }

        if (! isset($data['features']) || ! is_array($data['features'])) {
            $fail('Il campo :attribute deve contenere un array di feature.');

            return;
        }

        // Puoi aggiungere ulteriori controlli qui, ad esempio, per verificare la validità di ogni feature,
        // se hanno le geometrie corrette, le proprietà, ecc.
    }
}
