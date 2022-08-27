<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique()->comment('Nombre de usuario');
            $table->string('password')->comment('Contraseña');
            $table->unsignedTinyInteger('access_attempts')->default(0)->comment('Intentos de acceso');
            $table->boolean('active')->default(true)->comment('Estado activo');
            $table->unsignedBigInteger('person_id')->unique()->comment('Ciudad de expedición');
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedSmallInteger('remember_role_id')->nullable()->comment('Último rol de login');
            $table->foreign('remember_role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('remember_store_id')->nullable()->comment('Última tienda de login');
            $table->foreign('remember_store_id')->references('id')->on('stores')->onDelete('cascade')->onUpdate('cascade');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
