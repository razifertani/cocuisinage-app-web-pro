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
        Schema::create('professional_notifications_params', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('professional_id');
            $table->foreign('professional_id')->references('id')->on('professional')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('notification_type_id');
            $table->foreign('notification_type_id')->references('id')->on('notifications_types')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('establishment_id');
            $table->foreign('establishment_id')->references('id')->on('establishments')->onUpdate('cascade')->onDelete('cascade');

            $table->boolean('active')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('professional_notifications_params');
    }
};
