<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PatientServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function formRule($id = false)
    {
        return [
            'firstname' => ['required', 'string', 'min:2', 'max:900'],
            'lastname' => ['required', 'string', 'min:2', 'max:900'],
            'email' => ['required', 'string', 'email', 'min:2', 'max:900', Rule::unique('patients')->ignore($id ? $id : '')],
            'dob' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'postcode' => ['required', 'string', 'min:2', 'max:900'],
            'mobile' => ['required', 'string', 'min:2', 'max:900'],
        ];
    }

    public function upsert(Request $request, $id = false)
    {
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = $request->email;
        $dob = $request->dob;
        $gender = $request->gender;
        $postcode = $request->postcode;
        $mobile = $request->mobile;

        $request->validate($this->formRule($id));

        $patient = ! $id ? new Patient : Patient::findOrFail($id);

        $patient->firstname = $firstname;
        $patient->lastname = $lastname;
        $patient->email = $email;
        $patient->dob = $dob;
        $patient->gender = $gender;
        $patient->postcode = $postcode;
        $patient->mobile = $mobile;

        $patient->{! $id ? 'created_by' : 'updated_by'} = Auth::id();

        $result = $patient->save();

        return ['success' => $result];
    }
}
