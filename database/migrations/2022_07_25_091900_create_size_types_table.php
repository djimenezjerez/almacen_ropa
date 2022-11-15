<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('size_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->comment('Tipo de talla');
            $table->unsignedTinyInteger('order')->default(0)->comment('Orden');
        });
    }

    public function down()
    {
        Schema::dropIfExists('size_types');
    }
};
