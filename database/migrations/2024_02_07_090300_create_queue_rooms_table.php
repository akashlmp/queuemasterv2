<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('queuetb_queue_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('room')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('queue_rooms');
    }
}
