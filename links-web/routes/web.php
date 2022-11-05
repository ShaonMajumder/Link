<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\PeopleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::prefix('users')->group(function(){
    Route::get('{id}', [LinkController::class, 'showUser']);
});
Route::prefix('links')->group(function(){
    Route::get('/', [LinkController::class, 'listIndex'])->name('links.list');
    Route::get('update/{link}', [LinkController::class, 'linkEdit']);
    Route::get('new', [PeopleController::class, 'create']);
    Route::post('insert', [LinkController::class, 'insert']);
    Route::get('tags/fix', [LinkController::class, 'tagFix']);
    Route::get('tags/count-total', [LinkController::class, 'tagCount']);
    Route::post('bulk-input', [LinkController::class, 'bulkInput']);
    Route::post('check-unique', [LinkController::class, 'checkUniqueLink']);
    
    Route::get('listtags', [LinkController::class, 'listTags']);
    Route::get('show/random', [LinkController::class, 'randomPage']);
    Route::post('pick/random', [LinkController::class, 'randomChoose']);
    
    

    Route::prefix('tags')->group(function(){
        Route::get('/', [LinkController::class, 'tagsIndex']);    
        Route::get('/{tag}', [LinkController::class, 'tagEditPage']);
        Route::post('/{tag}/update', [LinkController::class, 'tagUpdate']);
        Route::post('/{tag}/select-all-parent-tags', [LinkController::class, 'selectAllParents']);
    });
    

});
