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
        Schema::create('pin_details', function (Blueprint $table) {
            $table->id();
            $table->string('pin_type', 50)->nullable();
            $table->double('pin_rate')->nullable();
            $table->integer('max_pin_rate')->default(0);
            $table->double('business_volumn')->default(0);
            $table->double('pin_value')->default(0);
            $table->integer('pkg_type')->default(0);
            $table->string('pool_type', 50)->nullable();
            $table->integer('pool_id')->default(0);
            $table->string('package_name', 50)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->double('roi')->default(0);
            $table->double('refferal')->default(0);
            $table->double('booster_roi')->default(0);
            $table->double('days')->default(0);
            $table->integer('jago_trade')->default(1);
            $table->string('package_type', 50)->nullable();
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
        Schema::dropIfExists('pin_details');
    }
};
