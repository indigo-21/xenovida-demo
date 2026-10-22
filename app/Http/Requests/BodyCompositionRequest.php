<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for the Body Composition demo submission.
 *
 * Rules mirror the external API specification: the manual-entry method
 * requires the full measurement payload, while the file-upload method
 * ignores metric fields and instead requires a Tanita export file.
 */
class BodyCompositionRequest extends FormRequest
{
    /** Public demo endpoint — no auth gate. */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isFile = $this->input('entry_method') === 'file_upload';

        $rules = [
            // Shared, always required.
            'patient_reference' => ['required', 'string', 'max:255'],
            'entry_method' => ['required', Rule::in(['manual_entry', 'file_upload'])],
            'device_model' => ['nullable', 'string', 'max:100'],

            // New-patient object (all optional; defaults applied server-side by the API).
            'patient' => ['nullable', 'array'],
            'patient.firstname' => ['nullable', 'string', 'max:255'],
            'patient.lastname' => ['nullable', 'string', 'max:255'],
            'patient.email' => ['nullable', 'email', 'max:255'],
            'patient.postcode' => ['nullable', 'string', 'max:255'],
            'patient.telephone' => ['nullable', 'string', 'max:255'],
            'patient.mobile' => ['nullable', 'string', 'max:255'],
            'patient.dob' => ['nullable', 'date', 'before_or_equal:today'],
            'patient.gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'patient.patient_code' => ['nullable', 'string', 'max:255'],
            'patient.external_patient_id' => ['nullable', 'string', 'max:255'],
        ];

        if ($isFile) {
            $rules['tanita_file'] = ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:10240'];

            return $rules;
        }

        // Manual entry: required measurement fields.
        $rules += [
            'measurement_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'height_cm' => ['required', 'numeric', 'between:50,250'],
            'weight_kg' => ['required', 'numeric', 'between:20,350'],
            'body_type' => ['required', Rule::in(['Normal', 'Athletic'])],

            // Optional metric fields.
            'body_fat_percentage' => ['nullable', 'numeric', 'between:3,75'],
            'fat_mass_kg' => ['nullable', 'numeric', 'between:0.5,200'],
            'lean_body_mass_kg' => ['nullable', 'numeric', 'between:15,150'],
            'muscle_mass_kg' => ['nullable', 'numeric', 'between:10,100'],
            'visceral_fat_score' => ['nullable', 'integer', 'between:1,59'],
            'bone_mass_kg' => ['nullable', 'numeric', 'between:0.5,5'],
            'total_body_water_percentage' => ['nullable', 'numeric', 'between:35,75'],
            'total_body_water_kg' => ['nullable', 'numeric', 'between:15,80'],
            'extracellular_water_kg' => ['nullable', 'numeric', 'between:5,30'],
            'intracellular_water_kg' => ['nullable', 'numeric', 'between:10,50'],
            'tanita_bmr_kcal' => ['nullable', 'integer'],
            'tanita_bmr_kj' => ['nullable', 'integer'],
            'basal_metabolic_rate' => ['nullable', 'integer'],
            'metabolic_age' => ['nullable', 'integer', 'between:1,120'],
            'phase_angle' => ['nullable', 'numeric', 'between:1,15'],
            'physique_rating' => ['nullable', 'integer', 'between:1,9'],
        ];

        // Optional segmental fields (all nullable numeric).
        foreach (['left_arm', 'right_arm', 'trunk', 'left_leg', 'right_leg'] as $seg) {
            foreach ([
                'muscle_score', 'muscle_mass_kg', 'fat_score', 'fat_mass_kg',
                'fat_percent', 'fat_amount', 'free_fat_score',
            ] as $suffix) {
                $rules["{$seg}_{$suffix}"] = ['nullable', 'numeric'];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'tanita_file.required' => 'A Tanita export file is required for file uploads.',
            'tanita_file.mimes' => 'The file must be a .csv, .xls or .xlsx export.',
            'tanita_file.max' => 'The file may not be larger than 10 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_reference' => 'patient reference',
            'measurement_date' => 'measurement date',
            'height_cm' => 'height',
            'weight_kg' => 'weight',
            'body_type' => 'body type',
            'tanita_file' => 'Tanita file',
        ];
    }
}
