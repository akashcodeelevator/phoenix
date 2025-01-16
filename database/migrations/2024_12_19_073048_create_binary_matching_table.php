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
        Schema::create('binary_matching', function (Blueprint $table) {
            $table->id();
            $table->integer('u_code')->nullable();
            $table->integer('tx_id')->nullable();
            $table->double('matching')->default(0);
            $table->double('ben_matching')->default(0);
            $table->double('flash')->default(0);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('binary_matching');
    }
};
