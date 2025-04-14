<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('queuetb_payment_details', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_provider_name', 255);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('booking_id');
            $table->float('amount', 10, 4);
            $table->tinyInteger('payment_status')->default(0);
            $table->timestamp('payment_time')->nullable();
            $table->timestamps();           
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
}