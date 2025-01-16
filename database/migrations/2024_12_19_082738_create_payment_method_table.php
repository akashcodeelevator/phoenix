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
        Schema::create('payment_method', function (Blueprint $table) {
            $table->id();
            $table->string('parent_method', 50)->nullable();
            $table->string('slug', 50)->nullable();
            $table->string('name', 50)->nullable();
            $table->string('image', 256)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('address', 250)->nullable();
            $table->tinyInteger('is_parent')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('payment_method');
    }
};
