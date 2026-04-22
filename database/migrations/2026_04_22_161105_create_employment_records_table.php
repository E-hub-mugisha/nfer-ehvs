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
        Schema::create('employment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('employers')->onDelete('cascade');
            $table->string('job_title');
            $table->string('department')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable(); // null = currently employed here
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship'])
                ->default('full_time');
            $table->decimal('starting_salary', 12, 2)->nullable();
            $table->decimal('ending_salary', 12, 2)->nullable();
            $table->enum('exit_reason', [
                'resigned',
                'terminated',
                'contract_ended',
                'redundancy',
                'retirement',
                'mutual_agreement',
                'misconduct',
                'absconded',
                'deceased',
                'other'
            ])->nullable();
            $table->text('exit_details')->nullable(); // Employer's detailed exit note
            $table->enum('status', ['active', 'closed'])->default('active');
            // Verification chain: employee confirms the record
            $table->enum('employee_confirmation', ['pending', 'confirmed', 'disputed'])
                ->default('pending');
            $table->timestamp('employee_confirmed_at')->nullable();
            $table->boolean('is_visible')->default(true); // admin can hide records
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'employer_id']);
            $table->index('status');
            $table->index('exit_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_records');
    }
};
