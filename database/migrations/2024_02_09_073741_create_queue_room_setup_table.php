<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueRoomSetupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_room_setup', function (Blueprint $table) {
            $table->id();
            $table->string('queue_room_name', 255);
            $table->datetime('queue_timezone');
            $table->tinyInteger('queue_starttype');
            $table->datetime('queue_start');
            $table->tinyInteger('queue_endtype');
            $table->datetime('queue_end');
            $table->integer('queue_template');
            $table->string('queue_template_name', 255);
            $table->string('queue_input_url', 255);
            $table->tinyInteger('queue_protection');
            $table->string('queue_target_offer', 255);
            $table->tinyInteger('queue_advance_setting');
            $table->integer('max_traffic');
            $table->tinyInteger('queue_bypass_room');
            $table->tinyInteger('pre_queue');
            $table->integer('pre_queue_start');
            $table->json('queue_language');
            $table->string('queue_language_default');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('queue_room_setup');
    }
}
