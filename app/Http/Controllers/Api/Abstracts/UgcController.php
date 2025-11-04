<?php

namespace App\Http\Controllers\Api\Abstracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Wm\WmPackage\Http\Controllers\Api\Abstracts\UgcController as WmUgcController;

abstract class UgcController extends WmUgcController
{
    protected function validateGeojson(Request $request, $additionalRules = [])
    {
        // Get data based on request type
        $data = $request->all();
        if (isset($data['feature'])) {
            $data = json_decode($data['feature'], true);
        }

        // Se presente l'header app-id, ha priorità e sovrascrive properties.app_id
        $headerAppId = $request->header('app-id');
        if (! empty($headerAppId)) {
            $data['properties'] = $data['properties'] ?? [];
            $data['properties']['app_id'] = is_numeric($headerAppId) ? (int) $headerAppId : $headerAppId;
        }

        // Set up validation rules
        $rules = [
            'type' => 'required|string',
            'properties' => 'required|array',
            'properties.name' => 'required|string|max:255',
            'geometry' => 'required|array',
            'geometry.type' => 'required|string',
            'geometry.coordinates' => 'required|array',
            ...$additionalRules,
        ];

        // Add properties.app_id validation only if not an update request. This is to avoid validation error when updating from the app those UGCs that were created from Nova.
        if (! str_contains($request->route()->getName(), 'update')) {
            $rules['properties.app_id'] = 'required|exists:apps,id';
        }

        // Create validator instance
        $validator = Validator::make($data, $rules);

        // Return all validation errors when it fails
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessage = '';
            foreach ($errors as $field => $messages) {
                $errorMessage .= "$field: [".implode(', ', $messages)."]\n";
            }
            abort(400, trim($errorMessage));
        }

        return $data;
    }

    /**
     * Update resource for API v3
     */
    public function updateV3(Request $request): JsonResponse
    {
        $validated = $this->validateGeojson($request, ['properties.id' => 'required|exists:'.$this->getModelIstance()->getTable().',id']);
        $model = $this->getModelIstance()->find($validated['properties']['id']);
        $this->validateUser($model);

        $model = $this->fillModelWithRequest($model, $request, $validated);

        return response()->json(['id' => $model->id, 'message' => 'Updated successfully'], 200);
    }
}
