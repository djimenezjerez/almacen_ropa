<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_id')->comment('Referencia a la transferencia');
            $table->foreign('stock_transfer_id')->references('id')->on('stock_transfers')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('product_id')->comment('Referencia al producto');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('stock')->comment('Stock traspasado');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_transfer_details');
    }
};
