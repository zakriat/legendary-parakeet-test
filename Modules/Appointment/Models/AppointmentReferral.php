<?php

namespace Modules\Appointment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentReferral extends BaseModel
{
    use SoftDeletes;

    protected $table = 'appointment_referrals';

    protected $fillable = [
        'appointment_id',
        'referring_doctor_id',
        'receiving_doctor_id',
        'referral_specialty_id',
        'referral_type',
        'receiving_doctor_name',
        'receiving_doctor_speciality',
        'receiving_organisation_name',
        'receiving_doctor_email',
        'receiving_doctor_phone',
        'receiving_doctor_address',
        'referral_reason',
        'clinical_summary',
        'diagnosis',
        'requested_action',
        'urgency',
        'referred_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'referred_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(
            Appointment::class,
            'appointment_id'
        );
    }

    public function referringDoctor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'referring_doctor_id'
        );
    }

    public function receivingDoctor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'receiving_doctor_id'
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(
            ReferralSpecialty::class,
            'referral_specialty_id'
        );
    }
}