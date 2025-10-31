<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UgcPoiController;
use App\Http\Controllers\Api\UgcTrackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('api.')->group(function () {
    Route::get('app/webmapp/55/config.json', [App\Http\Controllers\FeatureCollectionController::class, 'conf'])->name('feature-collections.conf');
    Route::get('/sheets.json', [App\Http\Controllers\SheetController::class, 'get'])->name('sheets.get');
    Route::get('/feature-collections/{id}', [App\Http\Controllers\FeatureCollectionController::class, 'get'])->name('feature-collections.get');

    Route::prefix('v2')->name('v2.')->group(function () {
        Route::prefix('ugc')->name('ugc.')->middleware('auth:api')->group(function () {
            Route::prefix('media')->name('media.')->group(function () {
                Route::get('delete/{media}', [MediaController::class, 'destroy'])->name('destroy.legacy');
            });
        });
    });

    Route::prefix('v3')->group(function () {
        Route::name('ugc.')->prefix('ugc')->middleware('auth:api')->group(function () {
            Route::prefix('poi')->name('poi.')->group(function () {
                Route::post('edit', [UgcPoiController::class, 'updateV3'])->name('update');
            });
            Route::prefix('track')->name('track.')->group(function () {
                Route::post('edit', [UgcTrackController::class, 'updateV3'])->name('update');
            });
        });
    });
});
