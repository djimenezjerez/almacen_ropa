<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shopping_cart_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopping_cart_id')->comment('Carrito de compra');
            $table->foreign('shopping_cart_id')->references('id')->on('shopping_carts')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('product_id')->comment('Producto');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('quantity')->default(0)->comment('Cantidad');
            $table->float('sell_price', 10, 2)->default(0)->comment('Precio de compra');
            $table->float('subtotal', 10, 2)->default(0)->comment('Subtotal del producto');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shopping_cart_products');
    }
};
