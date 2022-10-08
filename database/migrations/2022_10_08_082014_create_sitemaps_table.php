<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sitemaps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('owner_id');
            $table->index('owner_id');
            $table->boolean('is_template');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sitemaps');
    }
};
