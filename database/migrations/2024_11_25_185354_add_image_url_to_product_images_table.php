<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->string('url')->nullable()->comment('URL de imagen de producto');
        });
        $rows = DB::table('product_images')->whereNotNull('path')->whereNull('url')->get();
        foreach ($rows as $row) {
            DB::table('product_images')->where('id', $row->id)->update(['url' => asset($row->path)]);
        }
    }

    public function down()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn(['url']);
        });
    }
};
