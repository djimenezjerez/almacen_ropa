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
            $table->enum('movement_type', ['entry', 'adjustment', 'transfer', 'sell', 'cancel_sell'])->comment('Tipo de movimiento');
            $table->unsignedBigInteger('user_id')->comment('Usuario que registró el movimiento');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('client_id')->nullable()->comment('Referencia al cliente');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade')->onUpdate('cascade');
            $table->nullableMorphs('fromable');
            $table->nullableMorphs('toable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movements');
    }
};
