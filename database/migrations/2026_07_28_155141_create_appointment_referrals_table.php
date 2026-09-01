<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_referrals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')
                ->unique()
                ->constrained('appointments')
                ->cascadeOnDelete();

            /*
             * The CRM doctor creating/sending the referral.
             */
            $table->foreignId('referring_doctor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Optional receiving doctor from this CRM.
             * Remains null for an external doctor.
             */
            $table->foreignId('receiving_doctor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * "internal" means receiving_doctor_id is used.
             * "external" means the manually entered details are used.
             */
            $table->enum('referral_type', [
                'internal',
                'external',
            ])->default('external');

            /*
             * These snapshot fields are stored for both internal
             * and external referrals. The PDF therefore remains
             * historically accurate if a user profile changes.
             */
            $table->string('receiving_doctor_name');
            $table->string('receiving_doctor_speciality');

            $table->string('receiving_organisation_name')
                ->nullable();

            $table->string('receiving_doctor_email')
                ->nullable();

            $table->string('receiving_doctor_phone', 40)
                ->nullable();

            $table->text('receiving_doctor_address')
                ->nullable();

            $table->text('referral_reason');
            $table->longText('clinical_summary');

            $table->text('diagnosis')
                ->nullable();

            $table->string('requested_action')
                ->nullable();

            $table->enum('urgency', [
                'routine',
                'urgent',
                'emergency',
            ])->default('routine');

            $table->timestamp('referred_at')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('referral_type');
            $table->index('receiving_doctor_speciality');
            $table->index('urgency');
            $table->index('referred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_referrals');
    }
};