<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shopping_carts', function (Blueprint $table) {
            $table->renameColumn('voucher', 'attachment_file');
            $table->string('attachment_type')->nullable()->comment('Tipo de archivo de comprobante');
        });
    }

    public function down()
    {
        Schema::table('shopping_carts', function (Blueprint $table) {
            $table->renameColumn('attachment_file', 'voucher');
            $table->dropColumn(['attachment_type']);
        });
    }
};
