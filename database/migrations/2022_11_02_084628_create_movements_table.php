<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->string('comment')->nullable()->comment('Glosa');
            $table->unsignedTinyInteger('movement_type_id')->comment('Tipo de movimiento');
            $table->foreign('movement_type_id')->references('id')->on('movement_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('user_id')->comment('Usuario que registró el movimiento');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('client_id')->nullable()->comment('Referencia al cliente');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('from_store_id')->nullable()->comment('Salida de tienda o almacén');
            $table->foreign('from_store_id')->references('id')->on('stores')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('to_store_id')->nullable()->comment('Ingreso a tienda o almacén');
            $table->foreign('to_store_id')->references('id')->on('stores')->onDelete('cascade')->onUpdate('cascade');
            $table->float('total_price', 12, 2)->comment('Costo total');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movements');
    }
};
