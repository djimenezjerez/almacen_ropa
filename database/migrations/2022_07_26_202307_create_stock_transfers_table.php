<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origin_store_id')->nullable()->comment('Referencia a la tienda origen');
            $table->foreign('origin_store_id')->references('id')->on('stores')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('destiny_store_id')->nullable()->comment('Referencia a la tienda destino');
            $table->foreign('destiny_store_id')->references('id')->on('stores')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('origin_warehouse_id')->nullable()->comment('Referencia al almacén origen');
            $table->foreign('origin_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('destiny_warehouse_id')->nullable()->comment('Referencia al almacén destino');
            $table->foreign('destiny_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Referencia al usuario');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_transfers');
    }
};
