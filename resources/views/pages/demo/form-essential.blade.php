{{-- ===================================================================== --}}
{{-- ESSENTIAL SHEET — API-backed clinical data entry form                   --}}
{{-- Mirrors the /api/v1/essential-sheet request payload. Posts to the       --}}
{{-- server proxy (demo.essential-sheet.store) which forwards to the API.    --}}
{{-- ===================================================================== --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-notes-medical mr-1"></i> Essential Sheet</h3>
        <div class="card-tools">
            <span class="badge" :class="api.configured ? 'badge-success' : 'badge-secondary'"
                x-text="api.configured ? 'API connected' : 'API offline'"></span>
        </div>
    </div>

    <div class="card-body">
        {{-- ---------- Request meta ---------- --}}
        <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">
            <i class="fas fa-fingerprint mr-1"></i> Request
        </h6>
        <div class="row">
            <div class="col-7">
                <div class="form-group">
                    <label class="mb-1">Patient Reference <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm"
                        :class="esErr('patient_reference') && 'is-invalid'"
                        x-model="es.patient_reference" placeholder="REF-001">
                    <div class="invalid-feedback" x-text="esErrText('patient_reference')"></div>
                </div>
            </div>
            <div class="col-5">
                <div class="form-group">
                    <label class="mb-1">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm"
                        :class="esErr('measurement_date') && 'is-invalid'"
                        x-model="es.measurement_date" :max="today">
                    <div class="invalid-feedback" x-text="esErrText('measurement_date')"></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="mb-1 small">Clinical measures taken by</label>
            <input type="text" class="form-control form-control-sm"
                :class="esErr('clinical_measures_taken_by') && 'is-invalid'"
                x-model="es.clinical_measures_taken_by" placeholder="e.g. Nurse A">
            <div class="invalid-feedback" x-text="esErrText('clinical_measures_taken_by')"></div>
        </div>
        <div class="custom-control custom-switch mb-2">
            <input type="checkbox" class="custom-control-input" id="esExercised"
                x-model="es.exercised_this_morning">
            <label class="custom-control-label small" for="esExercised">Exercised this morning</label>
        </div>

        {{-- ---------- Anthropometry ---------- --}}
        <hr class="my-2">
        <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">
            <i class="fas fa-ruler-combined mr-1"></i> Anthropometry
        </h6>
        <div class="row">
            <div class="col-4">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Height (cm)</label>
                    <input type="number" step="0.1" min="50" max="250" class="form-control form-control-sm"
                        :class="esErr('height_cm') && 'is-invalid'" x-model.number="es.height_cm">
                    <div class="invalid-feedback" x-text="esErrText('height_cm')"></div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Waist (cm)</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="esErr('waist_circumference') && 'is-invalid'" x-model.number="es.waist_circumference">
                    <div class="invalid-feedback" x-text="esErrText('waist_circumference')"></div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Hip (cm)</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="esErr('hip_circumference') && 'is-invalid'" x-model.number="es.hip_circumference">
                    <div class="invalid-feedback" x-text="esErrText('hip_circumference')"></div>
                </div>
            </div>
        </div>

        {{-- ---------- Blood pressure ---------- --}}
        <hr class="my-2">
        <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">
            <i class="fas fa-heartbeat mr-1"></i> Blood Pressure &amp; Heart Rate
        </h6>
        <div class="row">
            <div class="col-4">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Systolic</label>
                    <input type="number" step="1" class="form-control form-control-sm"
                        :class="esErr('systolic_bp') && 'is-invalid'" x-model.number="es.systolic_bp">
                    <div class="invalid-feedback" x-text="esErrText('systolic_bp')"></div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Diastolic</label>
                    <input type="number" step="1" class="form-control form-control-sm"
                        :class="esErr('diastolic_bp') && 'is-invalid'" x-model.number="es.diastolic_bp">
                    <div class="invalid-feedback" x-text="esErrText('diastolic_bp')"></div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Heart Rate</label>
                    <input type="number" step="1" class="form-control form-control-sm"
                        :class="esErr('heart_rate') && 'is-invalid'" x-model.number="es.heart_rate">
                    <div class="invalid-feedback" x-text="esErrText('heart_rate')"></div>
                </div>
            </div>
        </div>

        {{-- ---------- Lipid panel ---------- --}}
        <hr class="my-2">
        <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">
            <i class="fas fa-vial mr-1"></i> Lipid Panel <small class="text-muted">(mmol/L)</small>
        </h6>
        <div class="row">
            <div class="col-6">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Total Cholesterol</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="esErr('cholesterol') && 'is-invalid'" x-model.number="es.cholesterol">
                    <div class="invalid-feedback" x-text="esErrText('cholesterol')"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-2">
                    <label class="mb-1 small">HDL</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="esErr('hdl') && 'is-invalid'" x-model.number="es.hdl">
                    <div class="invalid-feedback" x-text="esErrText('hdl')"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-2">
                    <label class="mb-1 small">LDL</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="esErr('ldl') && 'is-invalid'" x-model.number="es.ldl">
                    <div class="invalid-feedback" x-text="esErrText('ldl')"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Triglycerides</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="esErr('triglycerides') && 'is-invalid'" x-model.number="es.triglycerides">
                    <div class="invalid-feedback" x-text="esErrText('triglycerides')"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        <button type="button" class="btn btn-sm btn-outline-secondary" @click="resetEs()">
            <i class="fas fa-undo mr-1"></i> Reset
        </button>
        <div>
            <button type="button" class="btn btn-sm btn-light" @click="loadEsSample()" :disabled="esSubmitting">
                <i class="fas fa-magic mr-1"></i> Sample
            </button>
            <button type="button" class="btn btn-sm btn-primary" @click="submitEs()"
                :disabled="esSubmitting || !api.configured">
                <span x-show="!esSubmitting"><i class="fas fa-paper-plane mr-1"></i> Submit to API</span>
                <span x-show="esSubmitting"><i class="fas fa-spinner fa-spin mr-1"></i> Submitting…</span>
            </button>
        </div>
    </div>
</div>
