<?php

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
    return ['Laravel' => app()->version()];
});

Route::any('socket-io', function () {
    if (request('event')) {
        return \App\Helpers\SocketIo::trigger(request('event', 'hello'), request('data', '12345'));
    }

    return view('socket-io');
});
