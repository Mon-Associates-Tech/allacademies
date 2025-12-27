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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('')->group(function () {
    Route::get('/academic-groups', [\App\Http\Controllers\Api\ExaminationApiController::class, 'getGroups']);
    Route::get('/academic-groups/{group}/levels', [\App\Http\Controllers\Api\ExaminationApiController::class, 'getLevels']);
    Route::get('/academic-levels/{level}/subjects', [\App\Http\Controllers\Api\ExaminationApiController::class, 'getSubjects']);
    Route::get('/academic-subjects/{subject}/topics', [\App\Http\Controllers\Api\ExaminationApiController::class, 'getTopics']);

    Route::post('/questions/generate', [\App\Http\Controllers\Api\ExaminationApiController::class, 'generate']);
});
