<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('queuetb_subscription_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_id');
            $table->unsignedBigInteger('user_id');
            $table->float('amount', 10, 4);
            $table->datetime('subscription_create_date');
            $table->datetime('subscription_end_date');
            $table->unsignedBigInteger('payment_type');
            $table->unsignedBigInteger('subscriber_status');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('queuetb_subscription_details');
    }
}
