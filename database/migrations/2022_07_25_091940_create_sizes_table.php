<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Identificador de la talla');
            $table->unsignedSmallInteger('size_type_id')->comment('Referencia al tipo de talla');
            $table->foreign('size_type_id')->references('id')->on('size_types')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('numeric')->comment('Tipo de talla numérica');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sizes');
    }
};
