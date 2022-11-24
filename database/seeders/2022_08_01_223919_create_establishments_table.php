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
        if (!Schema::hasTable('establishments')) {
            Schema::create('establishments', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('company_id');
                $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

                $table->string('name');
                $table->string('slogan')->nullable();
                $table->string('type')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('zip_code')->nullable();
                $table->string('longitude');
                $table->string('latitude');
                $table->string('description')->nullable();
                $table->string('image_path')->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('establishments');
    }
};
