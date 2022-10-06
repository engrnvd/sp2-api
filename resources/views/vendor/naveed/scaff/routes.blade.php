<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */
?>

Route::resource('{{$table->slug()}}', {{config('naveed-scaff.controller-namespace')}}\{{$table->studly(true)}}Controller::class);
Route::post('/{{$table->slug()}}/bulk-edit', [{{config('naveed-scaff.controller-namespace')}}\{{$table->studly(true)}}Controller::class, 'bulkEdit']);
Route::post('/{{$table->slug()}}/bulk-delete', [{{config('naveed-scaff.controller-namespace')}}\{{$table->studly(true)}}Controller::class, 'bulkDelete']);
