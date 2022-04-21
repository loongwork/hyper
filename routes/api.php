<?php

use App\Http\Controllers;
use App\Http\Procedures;
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

Route::group(['prefix' => 'fawe'], static function () {
    Route::post('upload.php', [Controllers\FaweController::class, 'upload']);

    Route::get('/', [Controllers\FaweController::class, 'download']);

    Route::get('uploads/{file}', [Controllers\FaweController::class, 'download']);
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], static function () {
    Route::rpc('endpoint', [
        Procedures\TennisProcedure::class,
        Procedures\UserProcedure::class,
        Procedures\GameProcedure::class,
        Procedures\AccountLinkProcedure::class,
    ])->name('rpc.endpoint');
});
