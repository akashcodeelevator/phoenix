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
        Schema::create('users_info', function (Blueprint $table) {
            $table->id();
            $table->integer('u_code');
            $table->integer('u_sponsor')->nullable();
            $table->integer('Parentid')->nullable();
            $table->integer('pool_no')->default(0);
            $table->string('position', 250)->nullable();
            $table->string('matrix_pool', 200)->nullable();
            $table->string('matrix_position', 100)->nullable();
            $table->string('username', 255)->nullable();
            $table->string('user_type', 255)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('mobile', 222)->nullable();
            $table->string('website_url', 255)->nullable();
            $table->string('service_time', 255)->nullable();
            $table->string('password', 100)->nullable();
            $table->string('tx_password', 255)->nullable();
            $table->tinyInteger('is_fan')->default(1);
            $table->longText('address')->nullable();
            $table->longText('address2')->nullable();
            $table->string('father_name', 50)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('gender', 50)->nullable();
            $table->date('dob')->nullable();
            $table->string('post_code', 50)->nullable();
            $table->string('country', 200)->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('pan_no', 50)->nullable();
            $table->longText('img')->nullable();
            $table->string('state', 200)->nullable();
            $table->string('level', 255)->default('0');
            $table->integer('active_id')->nullable();
            $table->tinyInteger('active_status')->default(0);
            $table->tinyInteger('retopup_status')->default(0);
            $table->dateTime('retopup_date')->nullable();
            $table->tinyInteger('block_status')->notNullable();
            $table->integer('pass_status')->default(0);
            $table->tinyInteger('tx_pass_status')->default(0);
            $table->tinyInteger('profile_edited')->default(0);
            $table->dateTime('active_date')->nullable();
            $table->string('my_package', 50)->nullable();
            $table->string('my_rank', 200)->nullable();
            $table->tinyInteger('income_status')->default(1);
            $table->string('auto_register', 50)->nullable();
            $table->tinyInteger('notifications')->default(0);
            $table->tinyInteger('autopool_eligible_status')->default(0);
            $table->string('nominee_name', 255)->nullable();
            $table->string('nominee_relation', 50)->nullable();
            $table->dateTime('nominee_dob')->nullable();
            $table->string('facebook_link', 255)->nullable();
            $table->string('instagram_link', 255)->nullable();
            $table->string('twitter_link', 255)->nullable();
            $table->string('linkdin_link', 255)->nullable();
            $table->string('telegrame_link', 255)->nullable();
            $table->string('bio', 255)->nullable();
            $table->string('service_type', 255)->nullable();
            $table->mediumText('device_token')->nullable();
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
        Schema::dropIfExists('users_info');
    }
};
