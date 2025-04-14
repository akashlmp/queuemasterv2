<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemSettingTable extends Migration
{
    public function up()
    {
        Schema::create('queuetb_system_setting', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 255)->nullable();
            $table->string('name', 255);
            $table->datetime('system_time')->nullable();
            $table->string('language', 255)->nullable();
            $table->text('contact_address')->nullable();
            $table->string('contact_phone', 15)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('queuetb_system_setting');
    }
}