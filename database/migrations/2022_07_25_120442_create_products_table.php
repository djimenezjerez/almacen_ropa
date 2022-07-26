<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre del producto');
            $table->boolean('active')->default(true)->comment('Estado activo');
            $table->unsignedBigInteger('category_id')->comment('Referencia a la categoría');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('brand_id')->comment('Referencia a la marca');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('size_id')->comment('Referencia la talla');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('size_type_id')->comment('Referencia al tipo de talla');
            $table->foreign('size_type_id')->references('id')->on('size_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('color_id')->comment('Referencia al color');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
