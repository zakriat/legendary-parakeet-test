<?php

namespace Modules\Clinic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationTariff extends Model
{
    protected $fillable = [
        'clinic_service_id',
        'clinic_id',
        'doctor_id',
        'name',
        'duration_minutes',
        'consultation_mode',
        'rate_type',
        'price',
        'deposit_type',
        'deposit_value',
        'starts_at',
        'ends_at',
        'days_of_week',
        'priority',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'clinic_service_id' => 'integer',
        'clinic_id' => 'integer',
        'doctor_id' => 'integer',
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'deposit_value' => 'decimal:2',
        'days_of_week' => 'array',
        'priority' => 'integer',
        'status' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(
            ClinicsService::class,
            'clinic_service_id'
        );
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(
            Clinics::class,
            'clinic_id'
        );
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'doctor_id'
        );
    }

    public function calculateDeposit(): float
    {
        $price = (float) $this->price;
        $value = (float) $this->deposit_value;

        return match ($this->deposit_type) {
            'fixed' => min($value, $price),

            'percentage' => round(
                $price * ($value / 100),
                2
            ),

            default => 0.0,
        };
    }
}