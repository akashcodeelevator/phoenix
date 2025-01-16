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
        Schema::create('wallet_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250)->nullable(); // Name of the wallet type
            $table->string('slug', 255)->nullable(); // Slug for the wallet type
            $table->string('count_in_wallet', 255)->nullable(); // Count in wallet
            $table->string('wallet_type', 250)->nullable(); // Wallet type description
            $table->tinyInteger('status')->default(1); // Status with default 1
            $table->string('wallet_column', 55)->nullable(); // Wallet column
            $table->tinyInteger('binary')->default(1); // Binary wallet status
            $table->tinyInteger('single_leg')->default(1); // Single leg status
            $table->tinyInteger('generation')->default(1); // Generation status
            $table->integer('product_base')->default(1); // Product base with default 1
            $table->tinyInteger('universal')->nullable(); // Universal wallet
            $table->tinyInteger('investment')->nullable(); // Investment wallet
            $table->tinyInteger('repurchase')->nullable(); // Repurchase wallet
            $table->tinyInteger('ecommerce')->nullable(); // E-commerce wallet
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
        Schema::dropIfExists('wallet_types');
    }
};
