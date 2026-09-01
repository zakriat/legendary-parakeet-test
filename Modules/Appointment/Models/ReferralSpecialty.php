<?php

namespace Modules\Appointment\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralSpecialty extends BaseModel
{
    protected $table =
        'referral_specialties';

    protected $fillable = [
        'category',
        'name',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function referrals(): HasMany
    {
        return $this->hasMany(
            AppointmentReferral::class,
            'referral_specialty_id'
        );
    }
}