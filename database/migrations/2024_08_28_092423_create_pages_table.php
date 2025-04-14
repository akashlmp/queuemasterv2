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
        Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Equivalent to title
    $table->string('slug')->unique();
    $table->text('page_data'); // Equivalent to content
    $table->boolean('status')->default(true); // You can adjust the default value if necessary
    $table->timestamps(); // Automatically handles created_at and updated_at columns
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
