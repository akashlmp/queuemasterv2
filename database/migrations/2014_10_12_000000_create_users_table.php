<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('queuetb_users', function (Blueprint $table) {
            $table->id();            
            $table->string('email',255)->unique();
            $table->string('company_name',100)->nullable();
            $table->string('company_address',255)->nullable();
            $table->string('company_person_name',50)->nullable();
            $table->string('company_person_mobile', 15)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('role')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('t_c_check')->default(0);
            $table->tinyInteger('verify')->default(0);
            $table->string('ip_address',40)->nullable();
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
