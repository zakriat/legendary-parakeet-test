<?php

namespace Modules\Appointment\Models;

class PatientCondition extends PatientClinicalRecord
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'encounter_id',
        'recorded_by',
        'source',
        'is_active',
        'condition_name',
        'diagnosed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'diagnosed_at' => 'date',
        'is_active' => 'boolean',
    ];
}