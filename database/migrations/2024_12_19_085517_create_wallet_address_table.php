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
        Schema::create('wallet_address', function (Blueprint $table) {
            $table->id();
            $table->longText('btc_address')->nullable(); // Bitcoin address (nullable)
            $table->dateTime('added')->nullable(); // Nullable date of addition
            $table->bigInteger('userid'); // User ID (bigint, not nullable)
            $table->longText('xpub')->nullable(); // XPUB key (nullable)
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
        Schema::dropIfExists('wallet_address');
    }
};
