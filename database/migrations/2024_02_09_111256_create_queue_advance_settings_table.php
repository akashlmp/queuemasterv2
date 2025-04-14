<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueAdvanceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_advance_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_room_id')->constrained('queue_room_setup');
            $table->text('queue_path');
            $table->string('queue_parameters', 255);
            $table->enum('queue_contain_condition', [0, 1])->default(0);
            $table->tinyInteger('status');
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue_advance_settings');
    }
}
