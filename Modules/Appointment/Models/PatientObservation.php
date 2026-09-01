<?php

namespace Modules\Appointment\Models;

class PatientObservation extends PatientClinicalRecord
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'encounter_id',
        'recorded_by',
        'source',
        'is_active',
        'height_cm',
        'weight_kg',
        'bmi',
        'systolic',
        'diastolic',
        'heart_rate',
        'temperature_c',
        'oxygen_saturation',
        'observed_at',
        'notes',
    ];

    protected $casts = [
        'height_cm' => 'decimal:2',
        'weight_kg' => 'decimal:2',
        'bmi' => 'decimal:2',
        'systolic' => 'integer',
        'diastolic' => 'integer',
        'heart_rate' => 'integer',
        'temperature_c' => 'decimal:1',
        'oxygen_saturation' => 'integer',
        'observed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public static function calculateBmi(
        float|int|null $heightCm,
        float|int|null $weightKg
    ): ?float {
        if (!$heightCm || !$weightKg) {
            return null;
        }

        $heightMetres = $heightCm / 100;

        return round(
            $weightKg / ($heightMetres * $heightMetres),
            2
        );
    }
}