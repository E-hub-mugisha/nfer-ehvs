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
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_record_id')->constrained('employment_records')->onDelete('cascade');
            $table->foreignId('raised_by')->constrained('users')->onDelete('cascade');
            $table->enum('dispute_type', [
                'incorrect_dates',
                'wrong_exit_reason',
                'unfair_feedback',
                'false_record',
                'other'
            ]);
            $table->text('description');
            $table->string('supporting_document')->nullable();
            $table->enum('status', ['open', 'under_review', 'resolved', 'dismissed'])
                ->default('open');
            $table->text('admin_resolution')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
