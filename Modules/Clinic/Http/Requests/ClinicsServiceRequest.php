<?php

namespace Modules\Clinic\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClinicsServiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = request()->id;

        $rules = [
            'duration_min'      => 'required|integer',
            'charges'           => 'required|numeric',
            'status'            => 'sometimes|boolean',

            'tariffs' => [
                        'nullable',
                        'array',
                    ],

                    'tariffs.*.id' => [
                        'nullable',
                        'integer',
                        'exists:consultation_tariffs,id',
                    ],

                    'tariffs.*.name' => [
                        'required_with:tariffs',
                        'string',
                        'max:150',
                    ],

                    'tariffs.*.duration_minutes' => [
                        'required_with:tariffs',
                        'integer',
                        'in:10,20,30,60',
                    ],

                    'tariffs.*.consultation_mode' => [
                        'required_with:tariffs',
                        'in:in_clinic,video,home_visit',
                    ],

                    'tariffs.*.rate_type' => [
                        'required_with:tariffs',
                        'in:standard,out_of_hours,night,bank_holiday',
                    ],

                    'tariffs.*.price' => [
                        'required_with:tariffs',
                        'numeric',
                        'min:0',
                        'max:999999.99',
                    ],

                    'tariffs.*.deposit_type' => [
                        'required_with:tariffs',
                        'in:none,fixed,percentage',
                    ],

                    'tariffs.*.deposit_value' => [
                        'nullable',
                        'numeric',
                        'min:0',
                        'max:999999.99',
                    ],

                    // 'tariffs.*.starts_at' => [
                    //     'nullable',
                    //     'date_format:H:i',
                    // ],

                    // 'tariffs.*.ends_at' => [
                    //     'nullable',
                    //     'date_format:H:i',
                    // ],

                    'tariffs.*.starts_at' => [
                            'nullable',
                            'date_format:H:i:s',
                        ],

                        'tariffs.*.ends_at' => [
                            'nullable',
                            'date_format:H:i:s',
                        ],

                    'tariffs.*.status' => [
                        'nullable',
                        'boolean',
                    ],

        ];

        

        // If multiVendor is enabled, require system_service_id
        if (multiVendor()) {
            $rules['system_service_id'] = 'required|integer|exists:system_service,id';
        }

        // Add advance payment validation: if enabled, value must be > 0
        // The field names may be: advance_payment_enabled (checkbox), advance_payment_value (number/percent)
        // Only validate if advance_payment_enabled is present and truthy
        if (
            ($this->has('advance_payment_enabled') && $this->input('advance_payment_enabled')) ||
            ($this->has('advance_payment_value') && $this->input('advance_payment_value') > 0)
        ) {
            $rules['advance_payment_value'] = [
                'required',
                'numeric',
                'gt:0'
            ];
        }

        switch (strtolower($this->getMethod())) {
            case 'post':
            case 'put':
            case 'patch':
                return $rules;
        }

        return [];
    }
     
    public function authorize(): bool
    {
        return true;
    }
    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => false,
            'message' => $validator->errors()->first(),
            'all_message' => $validator->errors(),
        ];

        if (request()->wantsJson() || request()->is('api/*')) {
            throw new HttpResponseException(response()->json($data, 422));
        }

        throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach (
                $this->input('tariffs', [])
                as $index => $tariff
            ) {
                $depositType =
                    $tariff['deposit_type'] ?? 'none';

                $depositValue = (float) (
                    $tariff['deposit_value'] ?? 0
                );

                $price = (float) (
                    $tariff['price'] ?? 0
                );

                if (
                    $depositType === 'fixed' &&
                    $depositValue > $price
                ) {
                    $validator->errors()->add(
                        "tariffs.{$index}.deposit_value",
                        'The fixed deposit cannot exceed the tariff price.'
                    );
                }

                if (
                    $depositType === 'percentage' &&
                    $depositValue > 100
                ) {
                    $validator->errors()->add(
                        "tariffs.{$index}.deposit_value",
                        'The percentage deposit cannot exceed 100%.'
                    );
                }

                if (
                    $depositType !== 'none' &&
                    $depositValue <= 0
                ) {
                    $validator->errors()->add(
                        "tariffs.{$index}.deposit_value",
                        'Enter a deposit value greater than zero.'
                    );
                }
            }
        });
    }
}
