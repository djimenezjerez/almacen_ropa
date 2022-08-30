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
            $table->boolean('active')->default(true)->comment('Estado activo');
            $table->unsignedBigInteger('product_name_id')->comment('Referencia al nombre de producto');
            $table->foreign('product_name_id')->references('id')->on('product_names')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('brand_id')->comment('Referencia a la marca');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedTinyInteger('gender_id')->comment('Referencia al género');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('size_id')->comment('Referencia la talla');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('color_id')->comment('Referencia al color');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('stock')->default(0)->comment('Stock actual');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
