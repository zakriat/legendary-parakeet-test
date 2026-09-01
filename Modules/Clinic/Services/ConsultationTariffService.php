<?php

namespace Modules\Clinic\Services;

use Illuminate\Support\Facades\DB;
use Modules\Clinic\Models\ClinicsService;

class ConsultationTariffService
{
    public function sync(
        ClinicsService $service,
        array $tariffs
    ): void {
        DB::transaction(function () use (
            $service,
            $tariffs
        ) {
            $savedIds = [];

            foreach ($tariffs as $tariff) {
                $tariffId = $tariff['id'] ?? null;

                $values = [
                    'name' => $tariff['name'],

                    'duration_minutes' =>
                        (int) $tariff[
                            'duration_minutes'
                        ],

                    'consultation_mode' =>
                        $tariff[
                            'consultation_mode'
                        ],

                    'rate_type' =>
                        $tariff['rate_type'],

                    'price' =>
                        round(
                            (float) $tariff['price'],
                            2
                        ),

                    'deposit_type' =>
                        $tariff['deposit_type']
                            ?? 'none',

                    'deposit_value' =>
                        round(
                            (float) (
                                $tariff[
                                    'deposit_value'
                                ] ?? 0
                            ),
                            2
                        ),

                    'starts_at' =>
                        $tariff['starts_at']
                            ?: null,

                    'ends_at' =>
                        $tariff['ends_at']
                            ?: null,

                    'priority' =>
                        (int) (
                            $tariff['priority']
                            ?? 0
                        ),

                    'status' =>
                        (bool) (
                            $tariff['status']
                            ?? true
                        ),

                    'updated_by' => auth()->id(),
                ];

                /*
                 * This service form creates generic tariffs.
                 * Doctor/clinic overrides can be added later
                 * without changing the database design.
                 */
                $values['doctor_id'] = null;
                $values['clinic_id'] = null;

                if ($tariffId) {
                    $record = $service
                        ->consultationTariffs()
                        ->whereKey($tariffId)
                        ->firstOrFail();

                    $record->update($values);
                } else {
                    $record = $service
                        ->consultationTariffs()
                        ->create(
                            array_merge($values, [
                                'created_by' =>
                                    auth()->id(),
                            ])
                        );
                }

                $savedIds[] = $record->id;
            }

            /*
             * Remove tariff rows deleted from the form.
             */
            $service->consultationTariffs()
                ->when(
                    !empty($savedIds),
                    fn ($query) =>
                        $query->whereNotIn(
                            'id',
                            $savedIds
                        )
                )
                ->when(
                    empty($savedIds),
                    fn ($query) => $query
                )
                ->delete();
        });
    }
}