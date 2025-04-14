<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewayTable extends Migration
{
    public function up()
    {
        Schema::create('queuetb_payment_gateway', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_provider_name', 255)->nullable();
            $table->string('gateway_url', 255)->nullable();
            $table->string('payment_type', 255)->nullable();
            $table->string('payment_id', 255)->nullable();
            $table->string('payment_key', 255)->nullable();
            $table->text('payment_description')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('queuetb_payment_gateway');
    }
}