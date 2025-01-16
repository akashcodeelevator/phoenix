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
        Schema::create('plan', function (Blueprint $table) {
            $table->id();
            $table->string('package_name', 50);
            $table->string('rank', 20)->nullable();
            $table->double('direct_income')->default(0);
            $table->integer('direct_requried')->default(0);
            $table->double('level_income')->nullable();
            $table->double('level_roi_income')->default(0);
            $table->double('roi_income')->default(0);
            $table->double('upline_income')->default(0);
            $table->integer('reward_direct')->default(0);
            $table->integer('reward_team')->default(0);
            $table->integer('team_requrired')->default(0);
            $table->text('reward')->nullable();
            $table->text('reward_prize')->nullable();
            $table->integer('month');
            $table->double('cto')->default(0);
            $table->integer('cto_bussiness')->default(0);
            $table->string('bonanza_rank', 50)->nullable();
            $table->integer('bonaza_business')->default(0);
            $table->integer('bonaza_bonus')->default(0);
            $table->date('bonaza_start_date')->nullable();
            $table->date('bonaza_end_date')->nullable();
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
        Schema::dropIfExists('plan');
    }
};
