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
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('u_code')->nullable();
            $table->string('attached_doc', 55)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('disribute_no', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('father_name', 255)->nullable();
            $table->string('dob', 255)->nullable();
            $table->mediumText('personal_remark')->nullable();
            $table->string('id_no', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('front_image', 255)->nullable();
            $table->string('front_image_pan', 255)->nullable();
            $table->string('front_image_bank', 255)->nullable();
            $table->string('back_image_bank', 255)->nullable();
            $table->string('back_image', 255)->nullable();
            $table->string('upload_images', 255)->nullable();
            $table->string('account_image', 255)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('account_holder_name', 255)->nullable();
            $table->string('account_no', 50)->nullable();
            $table->string('ifsc_code', 25)->nullable();
            $table->string('bank_branch', 240)->nullable();
            $table->string('kyc_status', 55)->default('pending');
            $table->string('kyc_remark', 50)->nullable();
            $table->string('tax_id', 255)->nullable();
            $table->string('paytm_no', 55)->nullable();
            $table->string('btc_address', 100)->nullable();
            $table->string('eth_address', 100)->nullable();
            $table->string('tron_address', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->mediumText('bank_img')->nullable();
            $table->string('bank_remark', 255)->nullable();
            $table->string('pan_no', 200)->nullable();
            $table->string('pan_name', 200)->nullable();
            $table->string('pan_image', 255)->nullable();
            $table->string('account_type', 50)->nullable();
            $table->dateTime('pan_kyc_date')->nullable();
            $table->string('pan_kyc_status', 100)->default('pending');
            $table->string('pan_remark', 255)->nullable();
            $table->string('adhaar_name', 200)->nullable();
            $table->string('adhaar_no', 200)->nullable();
            $table->string('adhaar_address', 255)->nullable();
            $table->string('adhaar_image', 255)->nullable();
            $table->mediumText('adhaar_back_image')->nullable();
            $table->dateTime('adhaar_kyc_date')->nullable();
            $table->string('adhaar_kyc_status', 55)->default('pending');
            $table->string('adhaar_remark', 255)->nullable();
            $table->string('passport_no', 50)->nullable();
            $table->string('passport_img', 255)->nullable();
            $table->string('passport_remark', 255)->nullable();
            $table->string('passport_kyc_status', 255)->default('pending');
            $table->dateTime('passport_kyc_date')->nullable();
            $table->string('nominee_name', 255)->nullable();
            $table->string('nominee_relation', 255)->nullable();
            $table->string('nominee_dob', 255)->nullable();
            $table->mediumText('nominee_remark')->nullable();
            $table->string('kyc_edited_pan', 255)->default('0');
            $table->integer('kyc_edited_identity')->default(0);
            $table->integer('kyc_edited_bank')->default(0);
            $table->integer('kyc_edited_nominee')->default(0);
            $table->integer('kyc_edited_personal')->default(0);
            $table->string('kyc_status_identity', 20)->nullable();
            $table->string('kyc_status_nominee', 20)->nullable();
            $table->string('kyc_status_personal', 50)->nullable();
            $table->string('kyc_status_bank', 20)->nullable();
            $table->string('kyc_status_pan', 20)->nullable();
            $table->timestamp('updated_on')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('added_on')->useCurrent();
            $table->string('bank_kyc_status', 100)->default('pending');
            $table->dateTime('bank_kyc_date')->nullable();
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
        Schema::dropIfExists('user_accounts');
    }
};
