<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SitemapController;
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
    Route::post('/sitemaps/{id}/commands/save', [SitemapController::class, 'saveCommand']);
    Route::post('/sitemaps/{id}/commands/undo', [SitemapController::class, 'undoCommand']);
    Route::post('/sitemaps/{sitemap}/clone', [SitemapController::class, 'clone']);
    Route::post('/sitemaps/{sitemap}/archive', [SitemapController::class, 'archive']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/profile/update', [AuthController::class, 'update']);
    Route::post('/profile/change-password', [AuthController::class, 'changePassword']);
    Route::post('/profile/delete-account', [AuthController::class, 'deleteAccount']);

    require_once __DIR__ . "/crud-routes.php";
});


