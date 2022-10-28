<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SitemapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/sitemaps/{id}/commands/save', [SitemapController::class, 'saveCommand']);
    Route::post('/sitemaps/{id}/commands/undo', [SitemapController::class, 'undoCommand']);
    Route::post('/sitemaps/{sitemap}/clone', [SitemapController::class, 'clone']);
    Route::post('/sitemaps/{sitemap}/archive', [SitemapController::class, 'archive']);
    Route::get('/sitemaps/{sitemap}/to-sitemap-xml', [SitemapController::class, 'toSitemapXml']);
    Route::post('/sitemaps/import', [SitemapController::class, 'import']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/profile/update', [AuthController::class, 'update']);
    Route::post('/profile/change-password', [AuthController::class, 'changePassword']);
    Route::post('/profile/delete-account', [AuthController::class, 'deleteAccount']);

    require_once __DIR__ . "/crud-routes.php";
});

Route::post('trigger-socket-event', function () {
    $forUser = \request('forUser');
    $data = \Illuminate\Foundation\Inspiring::quote();
    $event = $forUser ? "user-event" : "public-event";
    $forUser ? \App\Helpers\SocketIo::forCurrentUser($event, $data) : \App\Helpers\SocketIo::trigger($event, $data);
});

Route::post('public/find-sitemap', [SitemapController::class, 'findSitemap']);
