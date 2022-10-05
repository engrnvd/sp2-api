<?php
Route::resource('users', 'UserController');
Route::post('/users/bulk-edit', 'UserController@bulkEdit');
Route::post('/users/bulk-delete', 'UserController@bulkDelete');

