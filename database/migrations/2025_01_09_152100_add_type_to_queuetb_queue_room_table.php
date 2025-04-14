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
        Schema::table('queuetb_queue_room', function (Blueprint $table) {
            $table->string('queue_type')->after('queue_room_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queuetb_queue_room', function (Blueprint $table) {
            $table->dropColumn('queue_type');
        });
    }
};
