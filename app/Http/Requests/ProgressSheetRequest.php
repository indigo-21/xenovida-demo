<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for the Essential Progress Sheet demo submission.
 *
 * Rules mirror the external /api/v1/progress-sheet contract: a patient
 * reference and measurement date anchor the record, free-text lifestyle
 * notes capture the patient's changes, and the clinical measures
 * (anthropometry, blood pressure, lipid panel) drive the computed
 * cardiovascular assessments on the API side. Unlike the Essential Sheet,
 * height is not submitted — it is returned by the API from the patient record.
 */
class ProgressSheetRequest extends FormRequest
{
    /** Public demo endpoint — no auth gate. */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Anchor fields.
            'patient_reference' => ['required', 'string', 'max:255'],
            'measurement_date' => ['required', 'date', 'before_or_equal:today'],
            'exercised_this_morning' => ['nullable', 'boolean'],
            'clinical_measures_taken_by' => ['nullable', 'string', 'max:255'],

            // Free-text progress / lifestyle notes.
            'dietary_changes' => ['nullable', 'string', 'max:2000'],
            'exercise_changes' => ['nullable', 'string', 'max:2000'],

            // Anthropometry.
            'waist_circumference' => ['nullable', 'numeric', 'between:30,300'],
            'hip_circumference' => ['nullable', 'numeric', 'between:30,300'],

            // Blood pressure / heart rate.
            'systolic_bp' => ['nullable', 'numeric', 'between:50,300'],
            'diastolic_bp' => ['nullable', 'numeric', 'between:30,200'],
            'heart_rate' => ['nullable', 'numeric', 'between:20,250'],

            // Lipid panel (mmol/L).
            'cholesterol' => ['nullable', 'numeric', 'between:1,20'],
            'hdl' => ['nullable', 'numeric', 'between:0.1,10'],
            'ldl' => ['nullable', 'numeric', 'between:0.1,15'],
            'triglycerides' => ['nullable', 'numeric', 'between:0.1,20'],
        ];
    }

    /**
     * Normalise booleans before validation so an unchecked toggle is false,
     * not a missing field.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('exercised_this_morning')) {
            $this->merge([
                'exercised_this_morning' => filter_var(
                    $this->input('exercised_this_morning'),
                    FILTER_VALIDATE_BOOLEAN
                ),
            ]);
        }
    }

    public function attributes(): array
    {
        return [
            'patient_reference' => 'patient reference',
            'measurement_date' => 'measurement date',
            'dietary_changes' => 'dietary changes',
            'exercise_changes' => 'exercise changes',
            'waist_circumference' => 'waist circumference',
            'hip_circumference' => 'hip circumference',
            'systolic_bp' => 'systolic blood pressure',
            'diastolic_bp' => 'diastolic blood pressure',
            'heart_rate' => 'heart rate',
            'clinical_measures_taken_by' => 'clinical measures taken by',
        ];
    }
}
