<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionStatusTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_status', function (Blueprint $table) {
            $table->id();
            $table->integer('name');
            $table->tinyInteger('status')->default(0);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_status');
    }
}
