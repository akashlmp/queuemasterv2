<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentStatusTable extends Migration
{
    public function up()
    {
        Schema::create('queuetb_payment_status', function (Blueprint $table) {
            $table->id();
            $table->integer('name')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_status');
    }
}