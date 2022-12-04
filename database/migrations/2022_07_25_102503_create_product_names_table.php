<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_names', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombres de productos');
            $table->unsignedBigInteger('category_id')->comment('Referencia a la categoría');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->float('sell_price', 10, 2)->default(1)->comment('Precio de venta');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_names');
    }
};
