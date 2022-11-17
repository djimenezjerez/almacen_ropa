<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movement_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movement_id')->comment('Referencia al movimiento');
            $table->foreign('movement_id')->references('id')->on('movements')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('product_id')->comment('Referencia al producto');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->BigInteger('stock')->comment('Cantidad');
            $table->unsignedBigInteger('store_id')->nullable()->comment('Tienda o almacén');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movement_details');
    }
};
