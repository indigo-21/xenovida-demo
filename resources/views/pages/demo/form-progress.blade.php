{{-- ===================================================================== --}}
{{-- ESSENTIAL PROGRESS SHEET — API-backed clinical data entry form          --}}
{{-- Mirrors the /api/v1/progress-sheet request payload. Posts to the server --}}
{{-- proxy (demo.progress-sheet.store) which forwards to the API.            --}}
{{-- ===================================================================== --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Essential Progress</h3>
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
                        :class="pgErr('patient_reference') && 'is-invalid'"
                        x-model="pg.patient_reference" placeholder="REF-001">
                    <div class="invalid-feedback" x-text="pgErrText('patient_reference')"></div>
                </div>
            </div>
            <div class="col-5">
                <div class="form-group">
                    <label class="mb-1">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm"
                        :class="pgErr('measurement_date') && 'is-invalid'"
                        x-model="pg.measurement_date" :max="today">
                    <div class="invalid-feedback" x-text="pgErrText('measurement_date')"></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="mb-1 small">Clinical measures taken by</label>
            <input type="text" class="form-control form-control-sm"
                :class="pgErr('clinical_measures_taken_by') && 'is-invalid'"
                x-model="pg.clinical_measures_taken_by" placeholder="e.g. Jane Smith">
            <div class="invalid-feedback" x-text="pgErrText('clinical_measures_taken_by')"></div>
        </div>
        <div class="custom-control custom-switch mb-2">
            <input type="checkbox" class="custom-control-input" id="pgExercised"
                x-model="pg.exercised_this_morning">
            <label class="custom-control-label small" for="pgExercised">Exercised this morning</label>
        </div>

        {{-- ---------- Lifestyle / progress notes ---------- --}}
        <hr class="my-2">
        <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">
            <i class="fas fa-clipboard-check mr-1"></i> Progress Notes
        </h6>
        <div class="form-group mb-2">
            <label class="mb-1 small">Dietary changes</label>
            <textarea rows="2" class="form-control form-control-sm"
                :class="pgErr('dietary_changes') && 'is-invalid'"
                x-model="pg.dietary_changes" placeholder="e.g. Reduced refined sugar."></textarea>
            <div class="invalid-feedback" x-text="pgErrText('dietary_changes')"></div>
        </div>
        <div class="form-group mb-2">
            <label class="mb-1 small">Exercise changes</label>
            <textarea rows="2" class="form-control form-control-sm"
                :class="pgErr('exercise_changes') && 'is-invalid'"
                x-model="pg.exercise_changes" placeholder="e.g. Two 30-minute brisk walks per week."></textarea>
            <div class="invalid-feedback" x-text="pgErrText('exercise_changes')"></div>
        </div>

        {{-- ---------- Anthropometry ---------- --}}
        <hr class="my-2">
        <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">
            <i class="fas fa-ruler-combined mr-1"></i> Anthropometry
        </h6>
        <div class="row">
            <div class="col-6">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Waist (cm)</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="pgErr('waist_circumference') && 'is-invalid'" x-model.number="pg.waist_circumference">
                    <div class="invalid-feedback" x-text="pgErrText('waist_circumference')"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Hip (cm)</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="pgErr('hip_circumference') && 'is-invalid'" x-model.number="pg.hip_circumference">
                    <div class="invalid-feedback" x-text="pgErrText('hip_circumference')"></div>
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
                        :class="pgErr('systolic_bp') && 'is-invalid'" x-model.number="pg.systolic_bp">
                    <div class="invalid-feedback" x-text="pgErrText('systolic_bp')"></div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Diastolic</label>
                    <input type="number" step="1" class="form-control form-control-sm"
                        :class="pgErr('diastolic_bp') && 'is-invalid'" x-model.number="pg.diastolic_bp">
                    <div class="invalid-feedback" x-text="pgErrText('diastolic_bp')"></div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Heart Rate</label>
                    <input type="number" step="1" class="form-control form-control-sm"
                        :class="pgErr('heart_rate') && 'is-invalid'" x-model.number="pg.heart_rate">
                    <div class="invalid-feedback" x-text="pgErrText('heart_rate')"></div>
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
                        :class="pgErr('cholesterol') && 'is-invalid'" x-model.number="pg.cholesterol">
                    <div class="invalid-feedback" x-text="pgErrText('cholesterol')"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-2">
                    <label class="mb-1 small">HDL</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="pgErr('hdl') && 'is-invalid'" x-model.number="pg.hdl">
                    <div class="invalid-feedback" x-text="pgErrText('hdl')"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-2">
                    <label class="mb-1 small">LDL</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="pgErr('ldl') && 'is-invalid'" x-model.number="pg.ldl">
                    <div class="invalid-feedback" x-text="pgErrText('ldl')"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-2">
                    <label class="mb-1 small">Triglycerides</label>
                    <input type="number" step="0.1" class="form-control form-control-sm"
                        :class="pgErr('triglycerides') && 'is-invalid'" x-model.number="pg.triglycerides">
                    <div class="invalid-feedback" x-text="pgErrText('triglycerides')"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        <button type="button" class="btn btn-sm btn-outline-secondary" @click="resetPg()">
            <i class="fas fa-undo mr-1"></i> Reset
        </button>
        <div>
            <button type="button" class="btn btn-sm btn-light" @click="loadPgSample()" :disabled="pgSubmitting">
                <i class="fas fa-magic mr-1"></i> Sample
            </button>
            <button type="button" class="btn btn-sm btn-primary" @click="submitPg()"
                :disabled="pgSubmitting || !api.configured">
                <span x-show="!pgSubmitting"><i class="fas fa-paper-plane mr-1"></i> Submit to API</span>
                <span x-show="pgSubmitting"><i class="fas fa-spinner fa-spin mr-1"></i> Submitting…</span>
            </button>
        </div>
    </div>
</div>
