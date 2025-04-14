<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    public function up()
    {
        Schema::create('queuetb_subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('number_of_queue_room')->default(0);
            $table->integer('monitor_queue_room')->default(0);
            $table->integer('inline_editing_queue_room')->default(0);
            $table->integer('sub_account')->default(0);
            $table->integer('plan_type')->default(0);
            $table->float('amount', 10, 4)->default(0.0000);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
}

