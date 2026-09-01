<?php

namespace Modules\Appointment\Models;

class PatientFamilyHistory extends PatientClinicalRecord
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'encounter_id',
        'recorded_by',
        'source',
        'is_active',
        'relationship',
        'condition_name',
        'age_at_diagnosis',
        'notes',
    ];

    protected $casts = [
        'age_at_diagnosis' => 'integer',
        'is_active' => 'boolean',
    ];
}