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
        Schema::create('admin', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('user', 100)->nullable();
            $table->string('type', 200)->default('controler');
            $table->string('password', 100)->nullable();
            $table->string('u_code', 100)->nullable();
            $table->integer('amount')->nullable();
            $table->binary('rights')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 244)->nullable();
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
        Schema::dropIfExists('admin');
    }
};
