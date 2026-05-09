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
    Schema::create('student_identities', function (Blueprint $table) {
        $table->id();
        $table->string('lrn')->unique();
        $table->string('fullname');
        $table->date('dob');
        $table->string('level'); // Junior or Senior High
        // Link this student to a specific teacher/adviser
        $table->foreignId('adviser_id')->constrained('teacher_identities');
        $table->boolean('is_active')->default(0);
        $table->softDeletes(); // This enables your "Archive" feature
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_identities');
    }
};
