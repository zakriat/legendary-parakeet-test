<?php

namespace Modules\Appointment\Models;

class PatientSocialHistory extends PatientClinicalRecord
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'encounter_id',
        'recorded_by',
        'source',
        'is_active',
        'smoking_status',
        'cigarettes_per_day',
        'alcohol_status',
        'alcohol_units_per_week',
        'occupation',
        'other_details',
    ];

    protected $casts = [
        'cigarettes_per_day' => 'integer',
        'alcohol_units_per_week' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}