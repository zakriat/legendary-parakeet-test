<?php

namespace Modules\Appointment\Models;

class PatientMedication extends PatientClinicalRecord
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'encounter_id',
        'recorded_by',
        'source',
        'is_active',
        'medication_name',
        'dose',
        'frequency',
        'route',
        'started_at',
        'ended_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
        'is_active' => 'boolean',
    ];
}