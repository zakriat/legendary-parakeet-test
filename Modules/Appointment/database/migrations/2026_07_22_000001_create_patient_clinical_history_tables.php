<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function commonColumns(Blueprint $table): void
    {
        $table->id();

        $table->foreignId('patient_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('appointment_id')
            ->constrained('appointments')
            ->cascadeOnDelete();

        $table->foreignId('encounter_id')
            ->nullable()
            ->constrained('patient_encounters')
            ->nullOnDelete();

        $table->foreignId('recorded_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->string('source', 30)->default('booking');
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->softDeletes();

        $table->index(['patient_id', 'is_active']);
        $table->index(['appointment_id', 'is_active']);
        $table->index(['encounter_id', 'is_active']);
    }

    public function up(): void
    {
        Schema::create('patient_conditions', function (Blueprint $table) {
            $this->commonColumns($table);

            $table->string('condition_name');
            $table->date('diagnosed_at')->nullable();
            $table->string('status', 30)->default('active');
            $table->text('notes')->nullable();
        });

        Schema::create('patient_medications', function (Blueprint $table) {
            $this->commonColumns($table);

            $table->string('medication_name');
            $table->string('dose')->nullable();
            $table->string('frequency')->nullable();
            $table->string('route')->nullable();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->string('status', 30)->default('current');
            $table->text('notes')->nullable();
        });

        Schema::create('patient_allergies', function (Blueprint $table) {
            $this->commonColumns($table);

            $table->string('allergen');
            $table->string('reaction')->nullable();
            $table->string('severity', 30)->default('unknown');
            $table->date('identified_at')->nullable();
            $table->text('notes')->nullable();
        });

        Schema::create('patient_social_histories', function (Blueprint $table) {
            $this->commonColumns($table);

            $table->string('smoking_status', 30)->default('never');
            $table->unsignedInteger('cigarettes_per_day')->nullable();
            $table->string('alcohol_status', 30)->default('none');
            $table->decimal('alcohol_units_per_week', 6, 2)->nullable();
            $table->string('occupation')->nullable();
            $table->text('other_details')->nullable();
        });

        Schema::create('patient_family_histories', function (Blueprint $table) {
            $this->commonColumns($table);

            $table->string('relationship');
            $table->string('condition_name');
            $table->unsignedSmallInteger('age_at_diagnosis')->nullable();
            $table->text('notes')->nullable();
        });

        Schema::create('patient_observations', function (Blueprint $table) {
            $this->commonColumns($table);

            $table->decimal('height_cm', 6, 2)->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->decimal('bmi', 5, 2)->nullable();
            $table->unsignedSmallInteger('systolic')->nullable();
            $table->unsignedSmallInteger('diastolic')->nullable();
            $table->unsignedSmallInteger('heart_rate')->nullable();
            $table->decimal('temperature_c', 4, 1)->nullable();
            $table->unsignedTinyInteger('oxygen_saturation')->nullable();
            $table->dateTime('observed_at')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_observations');
        Schema::dropIfExists('patient_family_histories');
        Schema::dropIfExists('patient_social_histories');
        Schema::dropIfExists('patient_allergies');
        Schema::dropIfExists('patient_medications');
        Schema::dropIfExists('patient_conditions');
    }
};