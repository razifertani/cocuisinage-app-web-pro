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
        if (!Schema::hasTable('professional')) {
            Schema::create('professional', function (Blueprint $table) {
                $table->id();

                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();

                $table->string('api_token')->nullable();
                $table->string('profile_photo_path')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('address_line_one')->nullable();
                $table->string('address_line_two')->nullable();
                $table->string('zip_code')->nullable();
                $table->string('country')->nullable();

                $table->unsignedBigInteger('company_id');
                $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('professional');
    }
};
