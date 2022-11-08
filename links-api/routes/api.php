<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Api\V1\Auth\LoginController;
use App\Http\Controllers\LinkController;
use Symfony\Component\HttpFoundation\Response;

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

Route::post('login',[LoginController::class,'login']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(function(){
    Route::any('logout', [LoginController::class, "logout"]);
    Route::prefix('links')->name('links.')->group(function(){
        Route::get('/', [LinkController::class, "listLinks"]);
        Route::get('/tags', [LinkController::class, "listTags"]);
        Route::post('/store', [LinkController::class, "store"]);

        Route::get('/{id}', [LinkController::class, "getLink"]);
        Route::post('/add', [LinkController::class, "addLink"]);
        Route::put('/update/{id}',  [LinkController::class, "updateLink"]);
        Route::delete('/delete/{id}',  [LinkController::class, "deleteLink"]);
    });
    
});