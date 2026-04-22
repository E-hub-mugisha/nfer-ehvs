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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('national_id', 20)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('nationality')->default('Rwandan');
            $table->string('district')->nullable();
            $table->string('sector')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('current_title')->nullable(); // current job title
            $table->text('bio')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->enum('employment_status', ['employed', 'unemployed', 'self_employed'])->default('unemployed');
            $table->boolean('is_verified')->default(false); // verified by admin
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('national_id');
            $table->index('employment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
