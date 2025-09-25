<?php

use App\CodebookXMLDecorator;
use App\Http\Controllers\SearchController;
use App\OptionsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('list-fields', [SearchController::class, 'viewFields']);
Route::get('sortable-fields', [SearchController::class, 'sortableFields']);
Route::post('surveys', [SearchController::class, 'search']);
Route::any('xml-surveys/{survey}', [SearchController::class, 'xmlShow']);
Route::any('xml-surveys', [SearchController::class, 'xmlSearch']);
Route::get('options', function (OptionsRepository $optionsRepository) {
    return response()->json($optionsRepository->options());
});
// Route::get('cache-clear', function() { Artisan::call('cache:clear');});