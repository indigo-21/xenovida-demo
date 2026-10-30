<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\BodyCompositionService;
use App\Services\PatientServices;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(PatientServices $patientServices) {}

    public function getTableData()
    {
        $data = [
            'patients' => Patient::all(),
        ];

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->getTableData();

        return view('pages.patient.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.patient.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PatientServices $patientServices)
    {
        $patientServices->upsert($request);

        return redirect()
            ->route('patient.index')
            ->with('success', 'New Patient created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, BodyCompositionService $bodyCompositionService)
    {
        $patient = Patient::findOrFail($id);

        $data['patient'] = $patient;
        $data['apiConfigured'] = $bodyCompositionService->isConfigured();

        return view('pages.patient.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $patient = Patient::findOrFail($id);

        $data['patient'] = $patient;

        return view('pages.patient.form', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, PatientServices $patientServices)
    {
        $fullname = $request->firstname.' '.$request->lastname;

        $patientServices->upsert($request, $id);

        return redirect()
            ->route('patient.index')
            ->with('success', "$fullname updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::findOrFail($id);

        $fullname = $patient->firstname.' '.$patient->lastname;

        $patient->delete();

        return [
            'error' => false,
            'message' => "$fullname deleted succesfully",
        ];
    }
}
