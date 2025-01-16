<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->string('tx_u_code', 25)->nullable();
            $table->string('u_code', 25)->nullable();
            $table->string('tx_type', 100)->nullable();
            $table->string('debit_credit', 55)->nullable();
            $table->string('source', 25)->nullable();
            $table->string('wallet_type', 100)->default('main_wallet');
            $table->decimal('amount', 10, 4)->default(0.0000);
            $table->double('token', 8, 2)->default(0);
            $table->decimal('tx_charge', 10, 2)->default(0.00);
            $table->double('retopup_charge', 8, 2)->default(0);
            $table->string('cripto_type', 255)->nullable();
            $table->string('cripto_address', 255)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->longText('payment_slip');
            $table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('tds_status')->default(0);
            $table->longText('txs_res')->nullable();
            $table->string('txs_status', 200)->nullable();
            $table->longText('bank_details')->nullable();
            $table->string('my_rank', 50)->nullable();
            $table->string('pan_number', 55)->nullable();
            $table->double('open_wallet', 8, 2)->nullable();
            $table->double('closing_wallet', 8, 2)->nullable();
            $table->longText('remark')->nullable();
            $table->double('distribute_per', 8, 2)->nullable();
            $table->integer('total_monthly_payount')->default(0);
            $table->double('user_prsnt', 8, 2)->nullable();
            $table->longText('api_response')->nullable();
            $table->longText('tx_record')->nullable();
            $table->integer('pool_level')->default(0);
            $table->longText('reason')->nullable();
            $table->integer('payout_id')->nullable();
            $table->integer('payment_id')->nullable();
            $table->tinyInteger('payout_status')->default(0);
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
        Schema::dropIfExists('transaction');
    }
};
