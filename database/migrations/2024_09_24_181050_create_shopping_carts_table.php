<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->comment('Cliente comprador');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade')->onUpdate('cascade');
            $table->float('total', 10, 2)->default(0)->comment('Precio total de la venta');
            $table->enum('state', ['open', 'closed', 'paid', 'waiting', 'rejected', 'finished'])->default('open');
            $table->string('voucher')->nullable()->comment('Comprobante de pago');
            $table->text('comment')->nullable()->comment('Observacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shopping_carts');
    }
};
