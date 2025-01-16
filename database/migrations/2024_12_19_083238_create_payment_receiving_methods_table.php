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
        Schema::create('payment_receiving_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('unique_name', 100)->nullable();
            $table->longText('method_details')->nullable();
            $table->longText('field_required')->nullable();
            $table->string('field_type', 100)->nullable();
            $table->string('method_type', 100)->nullable();
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
        Schema::dropIfExists('payment_receiving_methods');
    }
};
