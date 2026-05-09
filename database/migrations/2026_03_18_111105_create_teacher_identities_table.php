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
    Schema::create('teacher_identities', function (Blueprint $table) {
        $table->id();
        $table->string('employee_id')->unique();
        $table->string('fullname');
        $table->string('role')->default('Teacher');
        $table->date('dob')->nullable();
        // 0 = Inactive (Red), 1 = Active (Green)
        $table->boolean('is_active')->default(0); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_identities');
    }
};
