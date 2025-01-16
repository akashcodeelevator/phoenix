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
        Schema::create('rank', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('u_code')->default(0);
            $table->integer('active_id')->default(0);
            $table->string('rank', 200)->nullable();
            $table->double('rank_per', 8, 2)->default(0);
            $table->integer('rank_id')->default(0);
            $table->string('rank_type', 50)->nullable();
            $table->tinyInteger('is_complete')->default(1);
            $table->timestamp('complete_date')->nullable();
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
        Schema::dropIfExists('rank');
    }
};
