<?php

namespace Modules\Appointment\Services;

use Illuminate\Validation\ValidationException;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Clinic\Models\ConsultationTariff;

/**
 * Shared, server-side booking rules used by every appointment entry point.
 *
 * It intentionally accepts the legacy service calculation as a fallback, so
 * existing clients do not need to submit a tariff to keep working.
 */
class AppointmentBookingService
{
    use AppointmentTrait;

    public function pricing(array $data): array
    {
        $serviceData = $this->getServiceAmount(
            $data['service_id'],
            $data['doctor_id'],
            $data['clinic_id']
        );

        $tariff = null;
        if (!empty($data['consultation_tariff_id'])) {
            $tariff = ConsultationTariff::query()
                ->whereKey($data['consultation_tariff_id'])
                ->where('clinic_service_id', $data['service_id'])
                ->where('status', 1)
                ->where(fn ($query) => $query->whereNull('clinic_id')->orWhere('clinic_id', $data['clinic_id']))
                ->where(fn ($query) => $query->whereNull('doctor_id')->orWhere('doctor_id', $data['doctor_id']))
                ->first();

            if (!$tariff) {
                throw ValidationException::withMessages([
                    'consultation_tariff_id' => 'The selected consultation tariff is not available for this appointment.',
                ]);
            }
        }

        if (!$tariff) {
            return [
                'service_data' => $serviceData,
                'tariff' => null,
                'attributes' => [
                    'service_price' => $serviceData['service_price'],
                    'service_amount' => $serviceData['service_amount'],
                    'total_amount' => $serviceData['total_amount'],
                    'duration' => $serviceData['duration'],
                    'consultation_tariff_id' => null,
                    'consultation_mode' => null,
                    'rate_type' => null,
                    'tariff_name' => null,
                    'tariff_price' => null,
                    'deposit_type' => null,
                    'deposit_value' => null,
                    'deposit_amount' => 0,
                ],
            ];
        }

        $amount = round((float) $tariff->price, 2);
        $deposit = match ($tariff->deposit_type) {
            'fixed' => min(round((float) $tariff->deposit_value, 2), $amount),
            'percentage' => min(round($amount * ((float) $tariff->deposit_value / 100), 2), $amount),
            default => 0,
        };

        return [
            'service_data' => $serviceData,
            'tariff' => $tariff,
            'attributes' => [
                'service_price' => $amount,
                'service_amount' => $amount,
                'total_amount' => $amount,
                'duration' => (int) $tariff->duration_minutes,
                'consultation_tariff_id' => $tariff->id,
                'consultation_mode' => $tariff->consultation_mode,
                'rate_type' => $tariff->rate_type,
                'tariff_name' => $tariff->name,
                'tariff_price' => $amount,
                'deposit_type' => $tariff->deposit_type,
                'deposit_value' => $tariff->deposit_value ?? 0,
                'deposit_amount' => $deposit,
                'advance_payment_amount' => $tariff->deposit_value ?? 0,
                'advance_paid_amount' => $deposit,
                'remaining_payment_amount' => round($amount - $deposit, 2),
                'payble_amount' => $deposit > 0 ? $deposit : $amount,
                'advance_payment_status' => $deposit > 0 ? 1 : 0,
                'payment_status' => 0,
            ],
        ];
    }

    public function storeClinicalHistory(Appointment $appointment, array $input): void
    {
        app(PatientClinicalHistoryService::class)->storeBookingData($appointment, $input);
    }
}
