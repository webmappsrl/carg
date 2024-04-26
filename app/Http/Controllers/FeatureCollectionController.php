<?php

namespace App\Http\Controllers;

use App\Models\ConfFeatureCollection;
use App\Models\FeatureCollection;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeatureCollectionController extends Controller
{
    public function get($name)
    {
        $geojson = Storage::disk('public')->get('feature-collections/'.$name);

        return $geojson;
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
                    $filePath = storage_path('app/public/'.$value);
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
        $geohubConfig['MAP']['controls']['tiles'] = json_decode('[
            {
              "label": {
                "it": "Tipo di mappa",
                "en": "Map type"
              },
              "type": "title"
            },
            {
              "name": "CARG",
              "label": {
                "it": "Foglio CARG"
              },
              "icon": "<svg version=\\"1.1\\" id=\\"Livello_1\\" xmlns=\\"http://www.w3.org/2000/svg\\" xmlns=\\"http://www.w3.org/1999/xlink\\" x=\\"0px\\" y=\\"0px\\" viewBox=\\"0 0 40 40\\" style=\\"enable-background:new 0 0 40 40;\\" xml=\\"preserve\\"><g><path d=\\"M13.9,21.5c0.1,0.5,0.4,0.6,1.2,0.7c0.2,0,0.4,0.1,0.6,0.1c0.4,0.1,0.9,0.1,1.3,0.2c0.1,0,0.3,0,0.4,0c5.2-0.4,11.2-0.8,16.7-3.1c0.2-0.1,0.3-0.1,0.5-0.2c0.7-0.2,1.1-0.4,1.2-1c0.1-1-1.1-2.7-3-4.1c-1.2-0.9-2.4-1.5-3.2-1.5c-0.2,0-0.4,0-0.5,0.1c-4.4,1.9-9.4,4.3-14,7.4C14.3,20.6,13.8,21,13.9,21.5z\\"/><path d=\\"M17.5,15c2.5-1.3,5-2.6,7.5-3.7c0.7-0.3,2.1-1,2.1-1.5c0-0.2-0.2-0.7-1.5-1.6c-1.3-0.9-2.6-1.9-3.7-3.1c-1.3-1.4-2.4-1.4-4-0.1c-2,1.7-4.1,3.5-6.1,5.1c-1.9,1.5-3.7,3.1-5.6,4.7l-0.4,0.4c-1.5,1.2-2.4,2.1-1.8,3.5c0.7,1.5,2.3,1.9,3.7,2.2c0.3,0.1,0.6,0.1,0.9,0.2c1.4,0.2,1.7-0.3,2.2-1.1c1.3-2.1,3.4-3.3,5.1-4.2L17.5,15z\\"/><path d=\\"M34.5,22.5c-0.2,0.1-0.4,0.2-0.5,0.2l0,0c-5.8,1.9-10.6,2.7-15.6,2.6l-0.5,0c-0.5,0-1.3,0-2,0c-0.3,0-0.6,0-0.9,0c-0.2,0-0.4,0-0.6,0c-0.5,0-0.9,0.1-1.1,0.6c-0.4,0.9,0.1,1.8,0.8,2.6c1.7,2.2,3.6,4.4,5.4,6.4l0.3,0.3c0.4,0.5,1.3,1.2,2.3,0.4c1.2-1,2.4-1.9,3.6-2.8c3.3-2.6,6.7-5.2,9.6-8.3l0.1-0.1c0.8-0.9,1.1-1.4,0.7-1.9C35.6,22.1,35.3,22.2,34.5,22.5z\\"/></g></svg>",
              "url": "https://tiles.webmapp.it/carg/{z}/{x}/{y}.png",
              "type": "button"
            }]', true);
        $geohubConfig['MAP']['tiles'] = json_decode('[{"CARG": "https://tiles.webmapp.it/carg/{z}/{x}/{y}.png"}]', true);
        $geohubConfig['MAP']['minZoom'] = 5;
        $geohubConfig['MAP']['defZoom'] = 6;
        $geohubConfig['MAP']['bbox'] = [6.7499552751, 36.619987291, 18.4802470232, 47.1153931748];

        return response()->json($geohubConfig);
    }

    public function hexToRgba($hexColor, $opacity = 1.0)
    {
        $hexColor = ltrim($hexColor, '#');

        if (strlen($hexColor) === 6) {
            list($r, $g, $b) = sscanf($hexColor, '%02x%02x%02x');
        } elseif (strlen($hexColor) === 8) {
            list($r, $g, $b, $a) = sscanf($hexColor, '%02x%02x%02x%02x');
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
