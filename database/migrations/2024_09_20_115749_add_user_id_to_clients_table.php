<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->unique()->comment('Usuario asociado al cliente');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->dropForeign(['person_id']);
            $table->dropColumn('person_id');
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->unsignedBigInteger('person_id')->unique()->comment('Referencia a los datos de persona');
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
