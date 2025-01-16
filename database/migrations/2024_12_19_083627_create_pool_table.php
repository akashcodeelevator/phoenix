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
        Schema::create('pool', function (Blueprint $table) {
            $table->id();
            $table->integer('pool_id')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('pool_type', 55)->nullable();
            $table->integer('pool_parent')->nullable();
            $table->integer('pool_position')->nullable();
            $table->integer('u_id')->nullable();
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
        Schema::dropIfExists('pool');
    }
};
