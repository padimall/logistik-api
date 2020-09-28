<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('type');
            $table->string('origin');
            $table->string('sender');
            $table->string('sender_contact');
            $table->string('receiver');
            $table->string('receiver_contact');
            $table->string('receiver_province');
            $table->string('receiver_city');
            $table->string('receiver_district');
            $table->string('receiver_post_code');
            $table->string('address');
            $table->integer('weight');
            $table->integer('price');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
