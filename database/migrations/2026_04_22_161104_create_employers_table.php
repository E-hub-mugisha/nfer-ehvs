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
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('registration_number')->unique(); // RDB / official registration
            $table->string('tin_number', 20)->unique()->nullable(); // Tax ID
            $table->enum('company_type', [
                'private_company',
                'public_institution',
                'ngo',
                'parastatal',
                'embassy',
                'other'
            ])->default('private_company');
            $table->string('industry');
            $table->string('district');
            $table->string('city')->nullable();
            $table->text('address');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('contact_person'); // HR or authorized rep
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'suspended'])
                ->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('verification_status');
            $table->index('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};
