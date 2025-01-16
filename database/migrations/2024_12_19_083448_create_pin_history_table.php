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
        Schema::create('pin_history', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 200)->nullable();
            $table->integer('tx_user')->nullable();
            $table->integer('credit')->default(0);
            $table->integer('debit')->default(0);
            $table->string('pin_type', 200)->nullable();
            $table->longText('tx_type')->nullable();
            $table->longText('remark')->nullable();
            $table->string('prev_pin', 50)->nullable();
            $table->string('curr_pin', 200)->nullable();
            $table->tinyInteger('retrieve_status')->default(0);
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
        Schema::dropIfExists('pin_history');
    }
};
