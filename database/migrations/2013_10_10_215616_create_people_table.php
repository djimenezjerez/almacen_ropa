<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre completo');
            $table->string('document')->nullable()->comment('Documento de Identidad');
            $table->string('address')->nullable()->comment('Dirección');
            $table->string('email')->nullable()->comment('Email');
            $table->unsignedBigInteger('phone')->nullable()->comment('Teléfono');
            $table->unsignedTinyInteger('city_id')->nullable()->comment('Ciudad de expedición');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedTinyInteger('document_type_id')->nullable()->comment('Tipo de documento');
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('people');
    }
};
