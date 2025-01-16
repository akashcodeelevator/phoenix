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
        Schema::create('company_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method_name', 250)->nullable();
            $table->string('unique_name', 250)->nullable();
            $table->tinyInteger('default_account')->default(0);
            $table->longText('company_accounts')->nullable();
            $table->longText('fields_required')->nullable();
            $table->string('image', 250)->nullable();
            $table->string('type', 250)->default('text');
            $table->enum('status', ['1', '0'])->default('1');
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
        Schema::dropIfExists('company_payment_methods');
    }
};
