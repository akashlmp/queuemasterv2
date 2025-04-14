<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBypassQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bypass_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_room_id')->constrained('queue_room_setup');
            $table->string('bypass_template_name', 255);
            $table->string('bypass_url', 255);
            $table->string('bypass_file', 255)->nullable();
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
        Schema::dropIfExists('bypass_queue');
    }
}
