<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClinicalBookingStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $statuses = [
            [
                'name' => 'called_in',
                'value' => 'Called In',
                'sequence' => 50,
            ],
            [
                'name' => 'seen',
                'value' => 'Seen',
                'sequence' => 60,
            ],
            [
                'name' => 'referred',
                'value' => 'Referred',
                'sequence' => 70,
            ],
            [
                'name' => 'dna',
                'value' => 'DNA — Did Not Attend',
                'sequence' => 80,
            ],
        ];

        $colors = [
            'called_in' => 'info',
            'seen' => 'success',
            'referred' => 'warning',
            'dna' => 'danger',
        ];

        foreach ($statuses as $status) {
            DB::table('constants')->updateOrInsert(
                [
                    'type' => 'BOOKING_STATUS',
                    'name' => $status['name'],
                ],
                [
                    'value' => $status['value'],
                    'sequence' => $status['sequence'],
                    'sub_type' => null,
                    'status' => 1,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        foreach ($colors as $status => $color) {
            DB::table('constants')->updateOrInsert(
                [
                    'type' => 'BOOKING_STATUS_COLOR',
                    'name' => $status,
                ],
                [
                    'value' => $color,
                    'sequence' => 0,
                    'sub_type' => null,
                    'status' => 1,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}