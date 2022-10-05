<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('seeders', function (Blueprint $table) {
            $table->string('class');
            $table->dateTime('ran_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seeders');
    }
};
