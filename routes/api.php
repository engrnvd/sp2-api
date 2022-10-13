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

require __DIR__ . '/auth.php';

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/sitemaps/{id}/commands/save', [App\Http\Controllers\SitemapController::class, 'saveCommand']);
    Route::post('/sitemaps/{id}/commands/undo', [App\Http\Controllers\SitemapController::class, 'undoCommand']);
    Route::post('/sitemaps/{sitemap}/clone', [App\Http\Controllers\SitemapController::class, 'clone']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    require_once __DIR__ . "/crud-routes.php";
});


