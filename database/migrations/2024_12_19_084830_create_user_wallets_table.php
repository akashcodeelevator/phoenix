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
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('u_code')->nullable(); // Nullable for flexibility
            $table->integer('active_id')->default(0); // Default value of 0
            
            // Define wallet columns c1 to c27
            for ($i = 1; $i <= 27; $i++) {
                $default = ($i >= 21) ? 0 : 0; // All columns have default values of 0
                $table->double("c{$i}")->default($default);
            }
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
        Schema::dropIfExists('user_wallets');
    }
};
