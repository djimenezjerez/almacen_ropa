<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->comment('Marca');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('order')->default(0)->comment('Orden');
            $table->boolean('video')->default(false);
        });
    }

    public function down()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropForeign('brand_id');
            $table->dropColumn(['brand_id', 'order', 'video']);
        });
    }
};
