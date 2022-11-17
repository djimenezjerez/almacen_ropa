<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movement_types', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name')->comment('Nombre');
            $table->string('code')->unique()->comment('Código');
            $table->boolean('active')->default(true)->comment('Estado activo');
            $table->boolean('entry')->nullable()->comment('1/0: Ingreso de stock/Salida de stock, Null: ambos');
            $table->unsignedTinyInteger('order')->default(0)->comment('Orden');
        });
    }

    public function down()
    {
        Schema::dropIfExists('movement_types');
    }
};
