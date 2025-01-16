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
        Schema::create('advanced_info', function (Blueprint $table) {
            $table->id();
            $table->longText('title')->nullable();
            $table->longText('name')->nullable();
            $table->longText('label')->nullable();
            $table->longText('type')->nullable();
            $table->longText('options')->nullable();
            $table->longText('image')->nullable();
            $table->longText('value')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('admin_status')->default(0)->nullable();
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
        Schema::dropIfExists('advanced_info');
    }
};
