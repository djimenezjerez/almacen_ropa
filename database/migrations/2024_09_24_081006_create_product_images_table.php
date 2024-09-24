<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_name_id')->comment('Referencia al nombre de producto');
            $table->foreign('product_name_id')->references('id')->on('product_names')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('color_id')->comment('Referencia al color');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->string('path')->nullable()->comment('Ruta del archivo de imagen');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_images');
    }
};
