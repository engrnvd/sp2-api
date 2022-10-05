<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */
?>

Route::resource('{{$table->slug()}}', '{{$table->studly(true)}}Controller');
Route::post('/{{$table->slug()}}/bulk-edit', '{{$table->studly(true)}}Controller@bulkEdit');
Route::post('/{{$table->slug()}}/bulk-delete', '{{$table->studly(true)}}Controller@bulkDelete');
