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
        Schema::create('payment_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('subscription_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('txn_id')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('amount_total')->nullable();
            $table->string('currency')->nullable();
            $table->string('end_date')->nullable();
            $table->enum('payment_status', ['1', '2', '3'])->comment("1 for Pending, 2 for Complite or 3 for Fail")->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_information');
    }
};
