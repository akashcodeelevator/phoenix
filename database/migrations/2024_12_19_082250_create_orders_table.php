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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('u_code', 255)->nullable();
            $table->integer('active_id')->nullable();
            $table->string('tx_type', 255)->nullable();
            $table->string('package_type', 50)->nullable();
            $table->string('tx_user_id', 55)->nullable();
            $table->double('order_mrp')->default(0);
            $table->double('order_amount')->default(0);
            $table->double('order_bv')->nullable();
            $table->double('pv')->nullable();
            $table->double('roi')->default(0);
            $table->integer('day_diff')->default(3);
            $table->double('token')->default(0);
            $table->longText('order_details')->nullable();
            $table->longText('order_address')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('payment_status')->default(0);
            $table->longText('payment_response')->nullable()->comment('payment slip too');
            $table->integer('payout_id')->nullable();
            $table->tinyInteger('payout_status')->default(0);
            $table->integer('closing_status')->default(0);
            $table->integer('silver_closing')->default(0);
            $table->integer('gold_closing')->default(0);
            $table->integer('pearl_closing')->default(0);
            $table->integer('diamond_closing')->default(0);
            $table->integer('invoice_no')->nullable();
            $table->timestamp('date_accept')->nullable();
            $table->timestamp('date_dispatch')->nullable();
            $table->timestamp('date_approve')->nullable();
            $table->timestamp('date_reject')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
