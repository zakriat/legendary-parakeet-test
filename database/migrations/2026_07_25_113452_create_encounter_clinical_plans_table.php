<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'encounter_clinical_plans',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('encounter_id')
                    ->unique()
                    ->constrained('patient_encounters')
                    ->cascadeOnDelete();

                $table->foreignId('appointment_id')
                    ->nullable()
                    ->constrained('appointments')
                    ->nullOnDelete();

                $table->foreignId('patient_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('doctor_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->longText('doctor_history')
                    ->nullable();

                $table->longText('examination_findings')
                    ->nullable();

                $table->longText('diagnosis')
                    ->nullable();

                $table->longText('treatment')
                    ->nullable();

                $table->longText('advice')
                    ->nullable();

                $table->boolean('follow_up_required')
                    ->default(false);

                $table->unsignedSmallInteger(
                    'follow_up_interval'
                )->nullable();

                $table->enum('follow_up_interval_unit', [
                    'days',
                    'weeks',
                    'months',
                ])->nullable();

                $table->date('follow_up_date')
                    ->nullable();

                $table->text('follow_up_reason')
                    ->nullable();

                $table->enum('follow_up_status', [
                    'not_required',
                    'planned',
                    'booked',
                    'completed',
                    'cancelled',
                ])->default('not_required');

                $table->foreignId('follow_up_appointment_id')
                    ->nullable()
                    ->constrained('appointments')
                    ->nullOnDelete();

                // Snapshot of the responsible doctor.
                $table->string('prescriber_name')
                    ->nullable();

                $table->string('prescriber_gmc_number', 20)
                    ->nullable();

                $table->timestamp('recorded_at')
                    ->nullable();

                $table->unsignedBigInteger('created_by')
                    ->nullable();

                $table->unsignedBigInteger('updated_by')
                    ->nullable();

                $table->unsignedBigInteger('deleted_by')
                    ->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index('follow_up_date');
                $table->index('follow_up_status');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'encounter_clinical_plans'
        );
    }
};