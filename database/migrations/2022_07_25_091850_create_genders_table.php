<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('genders', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name')->comment('Nombres de los géneros');
            $table->unsignedTinyInteger('order')->default(0)->comment('Orden');
        });
    }

    public function down()
    {
        Schema::dropIfExists('genders');
    }
};
