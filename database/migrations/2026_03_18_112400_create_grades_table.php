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
    Schema::create('grades', function (Blueprint $table) {
        $table->id();
        $table->string('lrn'); // Link to student
        $table->string('subject');
        $table->decimal('grade', 5, 2);
        $table->string('semester'); // e.g., 1st or 2nd
        $table->boolean('is_submitted_to_admin')->default(0); // For the "Send" button
        $table->boolean('is_published')->default(0); // For Admin's "Forward" button
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
