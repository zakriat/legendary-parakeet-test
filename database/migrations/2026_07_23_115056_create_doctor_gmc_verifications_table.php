<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'doctor_gmc_verifications',
            function (Blueprint $table) {
                $table->id();

                // users.id of the doctor.
                $table->foreignId('doctor_user_id')
                    ->unique()
                    ->constrained('users')
                    ->cascadeOnDelete();

                // GMC value at the time it was checked.
                $table->string(
                    'verified_gmc_number',
                    7
                );

                $table->string('registered_name')->nullable();

                $table->string(
                    'registration_status'
                )->nullable();

                $table->boolean(
                    'has_licence_to_practise'
                )->nullable();

                $table->enum('verification_status', [
                    'pending',
                    'verified',
                    'not_licensed',
                    'mismatch',
                    'expired',
                    'unable_to_verify',
                ])->default('pending');

                $table->string('verification_method')
                    ->default('manual_official_register');

                $table->text('official_register_url');

                $table->timestamp('checked_at')->nullable();
                $table->timestamp('expires_at')->nullable();

                $table->foreignId('checked_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                // Private supporting document.
                $table->text('certificate_path')->nullable();

                $table->string(
                    'certificate_original_name'
                )->nullable();

                $table->string(
                    'certificate_mime_type'
                )->nullable();

                $table->string(
                    'certificate_checksum',
                    64
                )->nullable();

                $table->timestamp(
                    'certificate_uploaded_at'
                )->nullable();

                $table->text('notes')->nullable();

                // Your project uses these auditing columns.
                $table->unsignedInteger(
                    'created_by'
                )->nullable();

                $table->unsignedInteger(
                    'updated_by'
                )->nullable();

                $table->unsignedInteger(
                    'deleted_by'
                )->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index('verified_gmc_number');
                $table->index('verification_status');
                $table->index('expires_at');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'doctor_gmc_verifications'
        );
    }
};