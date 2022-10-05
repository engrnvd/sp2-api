<?php

use Illuminate\Support\Facades\Route;

Route::resource('users', App\Http\Controllers\UserController::class);
Route::post('/users/bulk-edit', [App\Http\Controllers\UserController::class, 'bulkEdit']);
Route::post('/users/bulk-delete', [App\Http\Controllers\UserController::class, 'bulkDelete']);

