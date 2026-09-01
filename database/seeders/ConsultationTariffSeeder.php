<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Clinic\Models\ClinicsService;

class ConsultationTariffSeeder extends Seeder
{
    public function run(): void
    {
        $service = ClinicsService::first();

        if (!$service) {
            return;
        }

        $tariffs = [
            [
                'name' => 'Standard 10 minutes',
                'duration_minutes' => 10,
                'consultation_mode' => 'in_clinic',
                'rate_type' => 'standard',
                'price' => 100,
                'deposit_type' => 'fixed',
                'deposit_value' => 10,
                'priority' => 10,
                'status' => true,
            ],
            [
                'name' => 'Standard 30 minutes',
                'duration_minutes' => 30,
                'consultation_mode' => 'in_clinic',
                'rate_type' => 'standard',
                'price' => 200,
                'deposit_type' => 'fixed',
                'deposit_value' => 10,
                'priority' => 10,
                'status' => true,
            ],
            [
                'name' => 'Out of hours',
                'duration_minutes' => 30,
                'consultation_mode' => 'in_clinic',
                'rate_type' => 'out_of_hours',
                'price' => 300,
                'deposit_type' => 'fixed',
                'deposit_value' => 10,
                'starts_at' => '17:00',
                'ends_at' => '23:59',
                'priority' => 20,
                'status' => true,
            ],
        ];

        foreach ($tariffs as $tariff) {
            $service->consultationTariffs()
                ->updateOrCreate(
                    [
                        'name' => $tariff['name'],
                    ],
                    $tariff
                );
        }
    }
}