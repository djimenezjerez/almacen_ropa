<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name')->comment('Nombre');
            $table->string('code')->unique()->comment('Código');
            $table->unsignedTinyInteger('order')->default(0)->comment('Orden');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_types');
    }
};
