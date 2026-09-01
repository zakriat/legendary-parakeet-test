<?php

namespace Modules\Clinic\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorGmcVerification extends BaseModel
{
    use SoftDeletes;

    protected $table = 'doctor_gmc_verifications';

    protected $fillable = [
        'doctor_user_id',
        'verified_gmc_number',
        'registered_name',
        'registration_status',
        'has_licence_to_practise',
        'verification_status',
        'verification_method',
        'official_register_url',
        'checked_at',
        'expires_at',
        'checked_by',
        'certificate_path',
        'certificate_original_name',
        'certificate_mime_type',
        'certificate_checksum',
        'certificate_uploaded_at',
        'notes',
    ];

    protected $casts = [
        'doctor_user_id' => 'integer',
        'has_licence_to_practise' => 'boolean',
        'checked_by' => 'integer',
        'checked_at' => 'datetime',
        'expires_at' => 'datetime',
        'certificate_uploaded_at' => 'datetime',
    ];

    protected $appends = [
        'is_current',
    ];

    public function doctorUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'doctor_user_id'
        );
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'checked_by'
        );
    }

    public function getIsCurrentAttribute(): bool
    {
        return $this->verification_status === 'verified'
            && $this->has_licence_to_practise === true
            && $this->expires_at?->isFuture();
    }
}