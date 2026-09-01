<?php

namespace Modules\Appointment\Services;

use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\PatientAllergy;
use Modules\Appointment\Models\PatientCondition;
use Modules\Appointment\Models\PatientFamilyHistory;
use Modules\Appointment\Models\PatientMedication;
use Modules\Appointment\Models\PatientObservation;
use Modules\Appointment\Models\PatientSocialHistory;

class PatientClinicalHistoryService
{
    public function storeBookingData(
        Appointment $appointment,
        array $input
    ): void {
        $common = [
            'patient_id' => $appointment->user_id,
            'appointment_id' => $appointment->id,
            'encounter_id' => null,
            'recorded_by' => $appointment->user_id,
            'source' => 'booking',
            'is_active' => true,
        ];

        foreach ($input['conditions'] ?? [] as $row) {
            if (empty(trim((string) ($row['condition_name'] ?? '')))) {
                continue;
            }
            PatientCondition::create(array_merge($common, [
                'condition_name' => $row['condition_name'],
                'diagnosed_at' => $row['diagnosed_at'] ?? null,
                'status' => $row['status'] ?? 'active',
                'notes' => $row['notes'] ?? null,
            ]));
        }

        foreach ($input['medications'] ?? [] as $row) {
            if (empty(trim((string) ($row['medication_name'] ?? '')))) {
                continue;
            }
            PatientMedication::create(array_merge($common, [
                'medication_name' => $row['medication_name'],
                'dose' => $row['dose'] ?? null,
                'frequency' => $row['frequency'] ?? null,
                'route' => $row['route'] ?? null,
                'started_at' => $row['started_at'] ?? null,
                'ended_at' => $row['ended_at'] ?? null,
                'status' => $row['status'] ?? 'current',
                'notes' => $row['notes'] ?? null,
            ]));
        }

        foreach ($input['allergies'] ?? [] as $row) {
            if (empty(trim((string) ($row['allergen'] ?? '')))) {
                continue;
            }
            PatientAllergy::create(array_merge($common, [
                'allergen' => $row['allergen'],
                'reaction' => $row['reaction'] ?? null,
                'severity' => $row['severity'] ?? 'unknown',
                'identified_at' => $row['identified_at'] ?? null,
                'notes' => $row['notes'] ?? null,
            ]));
        }

        if (!empty(array_filter($input['social_history'] ?? []))) {
            PatientSocialHistory::create(array_merge(
                $common,
                $input['social_history']
            ));
        }

        foreach ($input['family_history'] ?? [] as $row) {
            if (
                empty(trim((string) ($row['relationship'] ?? ''))) ||
                empty(trim((string) ($row['condition_name'] ?? '')))
            ) {
                continue;
            }
            PatientFamilyHistory::create(array_merge($common, [
                'relationship' => $row['relationship'],
                'condition_name' => $row['condition_name'],
                'age_at_diagnosis' =>
                    $row['age_at_diagnosis'] ?? null,
                'notes' => $row['notes'] ?? null,
            ]));
        }

        if (!empty(array_filter($input['observations'] ?? []))) {
            $height = $input['observations']['height_cm'] ?? null;
            $weight = $input['observations']['weight_kg'] ?? null;

            PatientObservation::create(array_merge(
                $common,
                $input['observations'],
                [
                    'bmi' => PatientObservation::calculateBmi(
                        $height,
                        $weight
                    ),
                    'observed_at' => now(),
                ]
            ));
        }
    }
}
