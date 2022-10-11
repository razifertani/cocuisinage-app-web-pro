<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('establishment_id')->default(1)->after('id');
            $table->foreign('establishment_id')->references('id')->on('establishments')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('professional_roles_in_establishment', function (Blueprint $table) {
            $table->unsignedBigInteger('establishment_id');
            $table->foreign('establishment_id', 'professional_roles_in_establishment_id_foreign')->references('id')->on('establishments')->onDelete('cascade');

            $table->primary(['professional_id', 'role_id', 'establishment_id'], 'professional_roles_in_establishment_primary');
        });

        Schema::table('professional_permissions_in_establishment', function (Blueprint $table) {
            $table->unsignedBigInteger('establishment_id');
            $table->foreign('establishment_id', 'professional_permissions_in_establishment_id_foreign')->references('id')->on('establishments')->onDelete('cascade');

            $table->primary(['professional_id', 'permission_id', 'establishment_id'], 'professional_roles_in_establishment_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['establishment_id']);
            $table->dropColumn('establishment_id');
        });

        Schema::table('professional_roles_in_establishment', function (Blueprint $table) {
            $table->dropPrimary(['professional_id', 'role_id', 'establishment_id'], 'professional_roles_in_establishment_primary');

            $table->dropForeign('professional_roles_in_establishment_id_foreign');
            $table->dropColumn('establishment_id');
        });

        Schema::table('professional_permissions_in_establishment', function (Blueprint $table) {
            $table->dropPrimary(['professional_id', 'permission_id', 'establishment_id'], 'professional_roles_in_establishment_primary');

            $table->dropForeign('professional_permissions_in_establishment_id_foreign');
            $table->dropColumn('establishment_id');
        });

    }
};
