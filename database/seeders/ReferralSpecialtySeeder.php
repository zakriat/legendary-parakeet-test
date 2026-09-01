<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferralSpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'Medical specialties' => [
                'Cardiology',
                'Dermatology',
                'Endocrinology and diabetes',
                'Gastroenterology',
                'General/internal medicine',
                'Geriatric medicine',
                'Haematology',
                'Hepatology',
                'Infectious diseases',
                'Nephrology',
                'Neurology',
                'Respiratory medicine',
                'Rheumatology',
                'Sleep medicine',
                'Stroke and transient ischaemic attack services',
            ],

            'Surgical specialties' => [
                'Breast surgery',
                'Bariatric surgery',
                'Cardiothoracic surgery',
                'Colorectal surgery',
                'Ear, nose and throat surgery',
                'General surgery',
                'Hepatopancreatobiliary surgery',
                'Neurosurgery',
                'Oral and maxillofacial surgery',
                'Orthopaedic surgery',
                'Plastic and reconstructive surgery',
                'Spinal surgery',
                'Upper gastrointestinal surgery',
                'Urology',
                'Vascular surgery',
            ],

            'Women’s health' => [
                'Gynaecology',
                'Obstetrics and maternity services',
                'Fertility and reproductive medicine',
                'Menopause clinic',
                'Endometriosis clinic',
                'Colposcopy',
                'Breast clinic',
                'Pelvic-floor service',
                'Sexual and reproductive health',
            ],

            'Children and young people' => [
                'General paediatrics',
                'Paediatric allergy',
                'Paediatric cardiology',
                'Paediatric dermatology',
                'Paediatric endocrinology',
                'Paediatric gastroenterology',
                'Paediatric neurology',
                'Paediatric respiratory medicine',
                'Child development services',
                'Child and adolescent mental health services',
            ],

            'Mental health and neurodevelopment services' => [
                'Psychiatry',
                'Clinical psychology',
                'Counselling',
                'Cognitive behavioural therapy',
                'EMDR therapy',
                'Addiction services',
                'Eating-disorder services',
                'ADHD assessment',
                'Autism assessment',
                'Perinatal mental-health services',
                'Crisis mental-health services',
            ],

            'Musculoskeletal and rehabilitation services' => [
                'Physiotherapy',
                'Osteopathy',
                'Chiropractic services',
                'Podiatry',
                'Occupational therapy',
                'Pain-management clinic',
                'Sports and exercise medicine',
                'Musculoskeletal assessment service',
                'Orthotics and prosthetics',
                'Rehabilitation medicine',
            ],

            'Eye, hearing and dental services' => [
                'Ophthalmology',
                'Optometry',
                'Audiology',
                'Hearing-aid services',
                'ENT',
                'Dentistry',
                'Orthodontics',
                'Oral medicine',
                'Oral and maxillofacial surgery',
            ],

            'Cancer and suspected-cancer services' => [
                'Rapid diagnostic centre',
                'Two-week-wait suspected-cancer pathways',
                'Medical oncology',
                'Clinical oncology',
                'Haemato-oncology',
                'Breast clinic',
                'Lung cancer service',
                'Colorectal cancer service',
                'Upper gastrointestinal cancer service',
                'HPB and pancreatic cancer service',
                'Gynaecological oncology',
                'Urological oncology',
                'Dermatological cancer service',
                'Palliative and supportive care',
            ],

            'Diagnostic referrals' => [
                'Blood and laboratory testing',
                'Histopathology and cytology',
                'X-ray',
                'Ultrasound',
                'CT scan',
                'MRI scan',
                'DEXA bone-density scan',
                'Mammography',
                'ECG',
                'Echocardiography',
                'Ambulatory blood-pressure monitoring',
                'Holter or cardiac-event monitoring',
                'Exercise ECG and cardiac stress testing',
                'Lung-function testing',
                'Endoscopy',
                'Colonoscopy',
                'Gastroscopy',
                'Sleep studies',
                'Neurophysiology, including EEG and nerve-conduction studies',
            ],

            'Community and supportive services' => [
                'District nursing',
                'Community nursing',
                'Health visiting',
                'Community pharmacy',
                'Dietetics',
                'Weight-management services',
                'Smoking-cessation services',
                'Alcohol and drug-misuse services',
                'Speech and language therapy',
                'Continence services',
                'Falls-prevention service',
                'Social prescribing',
                'Adult social care',
                'Safeguarding services',
                'Palliative and hospice care',
            ],

            'Other professional referrals' => [
                'Occupational health',
                'Travel medicine',
                'Vaccination services',
                'Private medical examinations',
                'Genetic counselling and clinical genetics',
                'Sexual-health and genitourinary medicine',
                'Gender-identity services',
                'Long-COVID services',
                'Memory clinic',
                'Chronic-fatigue/ME service',
            ],
        ];

        $now = now();
        $rows = [];

        foreach (
            $groups as $category => $specialties
        ) {
            foreach (
                $specialties as $position => $name
            ) {
                $rows[] = [
                    'category' => $category,
                    'name' => $name,
                    'status' => true,
                    'sort_order' =>
                        $position + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('referral_specialties')
            ->upsert(
                $rows,
                [
                    'category',
                    'name',
                ],
                [
                    'status',
                    'sort_order',
                    'updated_at',
                ]
            );
    }
}