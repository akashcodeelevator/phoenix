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
        Schema::table('users', function (Blueprint $table) {
            // Add the columns to the 'users' table
            $table->integer('u_sponsor')->nullable()->default(null);
            $table->integer('parentid')->nullable()->default(null);
            $table->string('position', 250)->nullable()->default(null);
            $table->string('matrix_pool', 200)->nullable()->default(null);
            $table->string('matrix_position', 100)->nullable()->default(null);
            $table->string('username', 255)->nullable()->default(null);
            $table->string('eth_address', 255)->nullable()->default(null);
            $table->string('user_type', 255)->nullable()->default(null);
            $table->string('title', 255)->nullable()->default(null);            
            $table->string('mobile', 222)->nullable()->default(null);            
            $table->string('tx_password', 255)->nullable()->default(null);
            $table->tinyInteger('set_user_password_status')->default(0);
            $table->tinyInteger('is_fan')->default(1);
            $table->longText('address')->nullable()->default(null);
            $table->longText('address2')->nullable()->default(null);
            $table->string('father_name', 50)->nullable()->default(null);
            $table->string('city', 255)->nullable()->default(null);
            $table->string('gender', 50)->nullable()->default(null);
            $table->dateTime('dob')->nullable()->default(null);
            $table->string('email_code', 255)->nullable()->default(null);
            $table->integer('is_email_verify')->default(0);
            $table->string('post_code', 50)->nullable()->default(null);
            $table->string('country', 200)->nullable()->default(null);
            $table->string('country_code', 255)->nullable()->default(null);
            $table->string('marital_status', 50)->nullable()->default(null);
            $table->string('pan_no', 50)->nullable()->default(null);
            $table->integer('direct')->default(0);
            $table->longText('img')->nullable()->default(null);
            $table->string('state', 200)->nullable()->default(null);
            $table->tinyInteger('admin_register_status')->default(0);
            $table->integer('active_id')->nullable()->default(null);
            $table->tinyInteger('active_status')->default(0);
            $table->integer('trade_status')->default(0);
            $table->integer('rank_id')->default(0);
            $table->tinyInteger('retopup_status')->default(0);
            $table->string('fast_achiever_rank', 100)->nullable()->default(null);
            $table->integer('token_status')->default(0);
            $table->dateTime('retopup_date')->nullable()->default(null);
            $table->dateTime('installment_date')->nullable()->default(null);
            $table->tinyInteger('block_status')->default(0);
            $table->integer('pass_status')->default(0);
            $table->tinyInteger('tx_pass_status')->default(0);
            $table->tinyInteger('profile_edited')->default(0);
            $table->dateTime('active_date')->nullable()->default(null);
            $table->integer('sip_status')->default(0);
            $table->dateTime('sip_date')->nullable()->default(null);
            $table->double('my_package')->nullable()->default(null);
            $table->string('my_rank', 200)->nullable()->default(null);
            $table->string('salary_rank', 50)->nullable()->default(null);
            $table->integer('income_per')->default(200);
            $table->string('growth_rank', 100)->nullable()->default(null);
            $table->string('rank_bonus', 250)->nullable()->default(null);
            $table->integer('pkg_type')->default(0);
            $table->tinyInteger('income_status')->default(1);
            $table->integer('booster_status')->default(0);
            $table->integer('booster1_status')->default(0);
            $table->integer('booster2_status')->default(0);
            $table->integer('booster3_status')->default(0);
            $table->dateTime('booster1_date')->nullable()->default(null);
            $table->dateTime('booster2_date')->nullable()->default(null);
            $table->dateTime('booster3_date')->nullable()->default(null);
            $table->string('auto_register', 50)->nullable()->default(null);
            $table->tinyInteger('notifications')->default(0);
            $table->timestamp('updated_on')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('added_on')->useCurrent();
            $table->string('nominee_name', 255)->nullable()->default(null);
            $table->string('nominee_relation', 50)->nullable()->default(null);
            $table->dateTime('nominee_dob')->nullable()->default(null);
            $table->string('instagram_link', 255)->nullable()->default(null);
            $table->string('facebook_link', 255)->nullable()->default(null);
            $table->string('twitter_link', 255)->nullable()->default(null);
            $table->string('telegram_link', 255)->nullable()->default(null);
            $table->string('snap_chat', 255)->nullable()->default(null);
            $table->integer('roi_income')->default(1);
            $table->integer('booster_income')->default(1);
            $table->integer('level_roi_income')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop all the columns added in the 'up' method
            $table->dropColumn([
                'u_sponsor', 'parentid', 'position', 'matrix_pool', 'matrix_position', 'username',
                'eth_address', 'user_type', 'title', 'mobile', 'tx_password',
                'set_user_password_status', 'is_fan', 'address', 'address2', 'father_name', 'city', 'gender',
                'dob', 'email_code', 'is_email_verify', 'post_code', 'country', 'country_code', 'marital_status',
                'pan_no', 'direct', 'img', 'state', 'admin_register_status', 'active_id', 'active_status',
                'trade_status', 'rank_id', 'retopup_status', 'fast_achiever_rank', 'token_status', 'retopup_date',
                'installment_date', 'block_status', 'pass_status', 'tx_pass_status', 'profile_edited', 'active_date',
                'sip_status', 'sip_date', 'my_package', 'my_rank', 'salary_rank', 'income_per', 'growth_rank',
                'rank_bonus', 'pkg_type', 'income_status', 'booster_status', 'booster1_status', 'booster2_status',
                'booster3_status', 'booster1_date', 'booster2_date', 'booster3_date', 'auto_register', 'notifications',
                'updated_on', 'added_on', 'nominee_name', 'nominee_relation', 'nominee_dob', 'instagram_link',
                'facebook_link', 'twitter_link', 'telegram_link', 'snap_chat', 'roi_income', 'booster_income',
                'level_roi_income'
            ]);
        });
    }
};
