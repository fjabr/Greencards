<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerSubscriptionReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_subscription_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shop_id');
            $table->integer('package_id');
            $table->double('amount');
            $table->double('payed_amount');
            $table->string('coupon');
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
        Schema::dropIfExists('seller_subscription_reports');
    }
}
