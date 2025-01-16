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
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 250)->nullable(); // Slug for settings
            $table->mediumText('single_leg')->nullable(); // Single leg settings
            $table->mediumText('binary')->nullable(); // Binary settings
            $table->mediumText('generation')->nullable(); // Generation settings
            $table->mediumText('product_base')->nullable(); // Product base settings
            $table->mediumText('universal')->nullable(); // Universal settings
            $table->mediumText('investment')->nullable(); // Investment settings
            $table->mediumText('repurchase')->nullable(); // Repurchase settings
            $table->mediumText('ecommerce')->nullable(); // E-commerce settings
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
        Schema::dropIfExists('website_settings');
    }
};
