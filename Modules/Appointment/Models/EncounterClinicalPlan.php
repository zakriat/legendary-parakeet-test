<?php

namespace Modules\Appointment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncounterClinicalPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'encounter_id',
        'appointment_id',
        'patient_id',
        'doctor_id',

        'doctor_history',
        'examination_findings',
        'diagnosis',
        'treatment',
        'advice',

        'follow_up_required',
        'follow_up_interval',
        'follow_up_interval_unit',
        'follow_up_date',
        'follow_up_reason',
        'follow_up_status',
        'follow_up_appointment_id',

        'prescriber_name',
        'prescriber_gmc_number',
        'recorded_at',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'follow_up_required' => 'boolean',
        'follow_up_interval' => 'integer',
        'follow_up_date' => 'date',
        'recorded_at' => 'datetime',
    ];

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(
            PatientEncounter::class,
            'encounter_id'
        );
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(
            Appointment::class,
            'appointment_id'
        );
    }

    public function followUpAppointment(): BelongsTo
    {
        return $this->belongsTo(
            Appointment::class,
            'follow_up_appointment_id'
        );
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'patient_id'
        );
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'doctor_id'
        );
    }
}