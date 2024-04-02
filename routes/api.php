<?php

use Illuminate\Http\Request;
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

Route::get('/config.json', function () {
    return redirect('https://geohub.webmapp.it/api/app/webmapp/55/config.json');
});
Route::get('/sheets.json', [App\Http\Controllers\SheetController::class, 'get']);
Route::get('/feature-collections/{id}', [App\Http\Controllers\FeatureCollectionController::class, 'get'])->name('api.feature-collections.get');
