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
        Schema::create('support', function (Blueprint $table) {
            $table->id();
            $table->string('u_code', 100);
            $table->mediumText('message')->nullable();
            $table->string('first_name', 200)->nullable();
            $table->string('last_name', 200)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('contactno', 20)->nullable();
            $table->string('subject', 200)->nullable();
            $table->string('ticket', 100)->nullable();
            $table->dateTime('time')->nullable();
            $table->string('msg_by', 100)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('reply_status')->default(0);
            $table->date('approved_date')->nullable();
            $table->mediumText('reply')->nullable();
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
        Schema::dropIfExists('support');
    }
};
