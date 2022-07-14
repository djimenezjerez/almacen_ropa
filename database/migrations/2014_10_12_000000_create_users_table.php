<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->comment('Nombres');
            $table->string('last_name')->comment('Apellidos');
            $table->unsignedBigInteger('identity_card')->unique()->comment('Documento de Identidad');
            $table->string('password')->comment('Contraseña');
            $table->string('email')->nullable()->comment('Correo Electrónico');
            $table->unsignedBigInteger('phone')->nullable()->comment('Teléfono');
            $table->unsignedTinyInteger('city_id')->comment('Ciudad de expedición');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedSmallInteger('role_id')->comment('Rol');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
