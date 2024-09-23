<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropForeign('model_has_roles_store_id_foreign');
            $table->dropPrimary('model_has_roles_role_model_type_primary');
            $table->bigInteger('store_id')->unsigned()->nullable(true)->change();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade')->onUpdate('cascade');
            $table->primary(
                ['role_id', 'model_id', 'model_type'],
                'model_has_roles_role_model_type_primary'
            );
        });
        DB::statement('ALTER TABLE `model_has_roles` MODIFY COLUMN `store_id` bigint(20) unsigned DEFAULT NULL NULL;');
    }

    public function down()
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropForeign('model_has_roles_store_id_foreign');
            $table->dropPrimary('model_has_roles_role_model_type_primary');
            $table->unsignedBigInteger('store_id')->nullable(false)->change();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->primary(
                ['store_id', 'role_id', 'model_id', 'model_type'],
                'model_has_roles_role_model_type_primary'
            );
        });
    }
};
