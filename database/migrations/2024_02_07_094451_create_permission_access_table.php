<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionAccessTable extends Migration
{
    public function up()
    {
        Schema::create('queuetb_permission_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('has_permission');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permission_access');
    }
}
