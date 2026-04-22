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
        Schema::create('employer_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_record_id')->constrained('employment_records')->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('employers')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            // Ratings 1-5 on key professional attributes
            $table->unsignedTinyInteger('rating_punctuality')->nullable();      // 1-5
            $table->unsignedTinyInteger('rating_teamwork')->nullable();         // 1-5
            $table->unsignedTinyInteger('rating_communication')->nullable();    // 1-5
            $table->unsignedTinyInteger('rating_integrity')->nullable();        // 1-5
            $table->unsignedTinyInteger('rating_performance')->nullable();      // 1-5
            $table->decimal('overall_rating', 3, 2)->nullable();  // computed average
            $table->text('general_remarks')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_of_improvement')->nullable();
            $table->boolean('would_rehire')->nullable();
            $table->enum('conduct_flag', ['clean', 'minor_issue', 'major_issue', 'blacklisted'])
                ->default('clean');
            $table->boolean('is_public')->default(true); // can employee hide sensitive remarks?
            $table->timestamps();

            $table->index(['employee_id', 'employer_id']);
            $table->index('conduct_flag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_feedbacks');
    }
};
