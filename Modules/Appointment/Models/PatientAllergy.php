<?php

namespace Modules\Appointment\Models;

class PatientAllergy extends PatientClinicalRecord
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'encounter_id',
        'recorded_by',
        'source',
        'is_active',
        'allergen',
        'reaction',
        'severity',
        'identified_at',
        'notes',
    ];

    protected $casts = [
        'identified_at' => 'date',
        'is_active' => 'boolean',
    ];
}