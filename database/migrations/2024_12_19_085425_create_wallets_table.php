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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('u_code')->nullable(); // Nullable for flexibility
            $table->string('name', 200)->nullable(); // Nullable
            $table->string('slug', 250)->unique(); // Unique constraint for slug
            $table->string('section', 200)->nullable(); // Nullable
            $table->float('value')->default(0); // Float with a default value of 0
            $table->tinyInteger('status')->default(1); // Status with default value 1
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
        Schema::dropIfExists('wallets');
    }
};
