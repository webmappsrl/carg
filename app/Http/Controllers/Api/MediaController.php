<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Wm\WmPackage\Http\Controllers\Api\MediaController as WmMediaController;
use Wm\WmPackage\Models\Media;

class MediaController extends WmMediaController
{
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media): JsonResponse
    {
        $user = auth()->user();
        if ($media->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $media->delete();
        } catch (Exception $e) {
            return response()->json([
                'error' => "this media can't be deleted by api",
                'code' => 400,
            ], 400);
        }

        return response()->json(['success' => 'media deleted']);
    }
}
