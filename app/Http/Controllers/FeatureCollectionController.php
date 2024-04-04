<?php

namespace App\Http\Controllers;

use App\Models\FeatureCollection;
use App\Models\ConfFeatureCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Exception;

class FeatureCollectionController extends Controller
{
    public function get($id)
    {
        $featureCollection = FeatureCollection::findOrFail($id);

        return response()->json($featureCollection);
    }

    public function conf()
    {
        $url = 'https://geohub.webmapp.it/api/app/webmapp/55/config.json';
        $jsonContent = file_get_contents($url);
        $geohubConfig = json_decode($jsonContent, true);

        // Ottieni i tuoi dati locali da ConfFeatureCollectio
        $confFeatureCollections = ConfFeatureCollection::all();

        $confFeatureCollections = $confFeatureCollections->map(function ($item) {
            // Converti ogni attributo dell'item in camelCase
            $convertedItem = [];
            foreach ($item->toArray() as $key => $value) {
                // Converti la chiave in camelCase
                $convertedKey = Str::camel($key);

                // Gestisci la conversione di eventuali sotto-array o oggetti, come 'label'
                if (is_array($value) || is_object($value)) {
                    $value = $this->convertToCamelCase($value);
                }
                if ($key === 'svg_path' && $value !== null) {
                    $filePath = storage_path('app/public/' . $value);
                    if (file_exists($filePath)) {
                        // Leggi il contenuto dell'SVG e aggiungilo all'attributo 'icon'
                        $convertedItem['icon'] = file_get_contents($filePath);
                    } else {
                        // Se il file non esiste, imposta 'icon' a null
                        $convertedItem['icon'] = null;
                    }
                } else {
                    $convertedItem[$convertedKey] = $value;
                }
                $convertedItem[$convertedKey] = $value;
            }
            $convertedItem['featureType'] = Str::camel($convertedItem['type']);
            $convertedItem['type'] = 'button';
            return $convertedItem;
        });
        $geohubConfig['MAP']['controls']['overlays'] = $confFeatureCollections;

        return response()->json($geohubConfig);
    }
    public function hexToRgba($hexColor, $opacity = 1.0)
    {
        $hexColor = ltrim($hexColor, '#');

        if (strlen($hexColor) === 6) {
            list($r, $g, $b) = sscanf($hexColor, "%02x%02x%02x");
        } elseif (strlen($hexColor) === 8) {
            list($r, $g, $b, $a) = sscanf($hexColor, "%02x%02x%02x%02x");
            $opacity = round($a / 255, 2);
        } else {
            throw new Exception('Invalid hex color format.');
        }

        $rgbaColor = "rgba($r, $g, $b, $opacity)";
        return $rgbaColor;
    }

    protected function convertToCamelCase($item)
    {
        if (is_array($item)) {
            $convertedArray = [];
            foreach ($item as $key => $value) {
                $convertedKey = Str::camel($key);
                $convertedArray[$convertedKey] = $value;
            }
            return $convertedArray;
        } elseif ($item instanceof \Illuminate\Database\Eloquent\Model) {
            return $this->convertToCamelCase($item->toArray());
        }
        return $item;
    }
    protected function convertValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return $this->convertToCamelCase($value);
        } elseif (is_string($value)) {
            $decodedJson = json_decode($value, false);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decodedJson;
            }
        }
        return $value;
    }
    protected function convertArrayOrObjectToCamelCase($value)
    {
        if (is_array($value)) {
            $convertedArray = [];
            foreach ($value as $subKey => $subValue) {
                $convertedArray[Str::camel($subKey)] = $this->convertArrayOrObjectToCamelCase($subValue);
            }
            return $convertedArray;
        } elseif (is_object($value)) {
            $convertedObject = new \stdClass();
            foreach ($value as $subKey => $subValue) {
                $convertedKey = Str::camel($subKey);
                $convertedObject->$convertedKey = $this->convertArrayOrObjectToCamelCase($subValue);
            }
            return $convertedObject;
        }
        return $value;
    }
}
