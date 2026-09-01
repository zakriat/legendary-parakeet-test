<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'consultation_tariffs',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('clinic_service_id')
                    ->constrained('clinics_services')
                    ->cascadeOnDelete();

                /*
                 * Null means this tariff applies to every clinic
                 * or doctor offering the service.
                 */
                $table->foreignId('clinic_id')
                    ->nullable()
                    ->constrained('clinic')
                    ->cascadeOnDelete();

                $table->foreignId('doctor_id')
                    ->nullable()
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->string('name', 150);

                $table->unsignedSmallInteger(
                    'duration_minutes'
                );

                $table->enum('consultation_mode', [
                    'in_clinic',
                    'video',
                    'home_visit',
                ])->default('in_clinic');

                $table->enum('rate_type', [
                    'standard',
                    'out_of_hours',
                    'night',
                    'bank_holiday',
                ])->default('standard');

                $table->decimal('price', 10, 2);

                $table->enum('deposit_type', [
                    'none',
                    'fixed',
                    'percentage',
                ])->default('none');

                $table->decimal('deposit_value', 10, 2)
                    ->default(0);

                /*
                 * Optional time window. For example:
                 * out-of-hours 17:00 until 23:59.
                 */
                $table->time('starts_at')->nullable();
                $table->time('ends_at')->nullable();

                /*
                 * Optional array such as:
                 * ["monday", "tuesday", "wednesday"]
                 */
                $table->json('days_of_week')->nullable();

                /*
                 * Higher priority wins when several tariffs match.
                 */
                $table->unsignedSmallInteger('priority')
                    ->default(0);

                $table->boolean('status')->default(true);

                $table->unsignedBigInteger('created_by')
                    ->nullable();

                $table->unsignedBigInteger('updated_by')
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'clinic_service_id',
                    'duration_minutes',
                    'consultation_mode',
                    'rate_type',
                    'status',
                ], 'consultation_tariff_lookup');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_tariffs');
    }
};