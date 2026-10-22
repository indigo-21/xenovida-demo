{{-- ===================================================================== --}}
{{-- BODY COMPOSITION — API-backed data entry form (manual + file upload)    --}}
{{-- ===================================================================== --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-clipboard-list mr-1"></i> Body Composition</h3>
        <div class="card-tools">
            <span class="badge" :class="api.configured ? 'badge-success' : 'badge-secondary'"
                x-text="api.configured ? 'API connected' : 'API offline'"></span>
        </div>
    </div>

    {{-- Submission method toggle --}}
    <div class="card-body pb-0">
        <div class="btn-group btn-group-sm btn-block mb-3" role="group" aria-label="Submission method">
            <button type="button" class="btn w-50"
                :class="bc.entry_method === 'manual_entry' ? 'btn-primary' : 'btn-outline-primary'"
                @click="bc.entry_method = 'manual_entry'">
                <i class="fas fa-keyboard mr-1"></i> Manual Entry
            </button>
            <button type="button" class="btn w-50"
                :class="bc.entry_method === 'file_upload' ? 'btn-primary' : 'btn-outline-primary'"
                @click="bc.entry_method = 'file_upload'">
                <i class="fas fa-file-upload mr-1"></i> File Upload
            </button>
        </div>
    </div>

    <div class="card-body pt-0">
        {{-- ---------- Request meta (shared) ---------- --}}
        <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">
            <i class="fas fa-fingerprint mr-1"></i> Request
        </h6>
        <div class="form-group">
            <label class="mb-1">Patient Reference <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm"
                :class="err('patient_reference') && 'is-invalid'"
                x-model="bc.patient_reference" placeholder="PTR-000123">
            <div class="invalid-feedback" x-text="errText('patient_reference')"></div>
        </div>
        <div class="form-group">
            <label class="mb-1">Device Model</label>
            <input type="text" class="form-control form-control-sm"
                :class="err('device_model') && 'is-invalid'"
                x-model="bc.device_model" placeholder="Tanita MC-780">
            <div class="invalid-feedback" x-text="errText('device_model')"></div>
        </div>

        {{-- ================= MANUAL ENTRY ================= --}}
        <template x-if="bc.entry_method === 'manual_entry'">
            <div>
                <hr class="my-2">
                <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">
                    <i class="fas fa-ruler-combined mr-1"></i> Measurement <span class="text-danger">*</span>
                </h6>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Measurement Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm"
                                :class="err('measurement_date') && 'is-invalid'"
                                x-model="bc.measurement_date" :max="today">
                            <div class="invalid-feedback" x-text="errText('measurement_date')"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Gender <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm"
                                :class="err('gender') && 'is-invalid'" x-model="bc.gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <div class="invalid-feedback" x-text="errText('gender')"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Height (cm) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="50" max="250" class="form-control form-control-sm"
                                :class="err('height_cm') && 'is-invalid'" x-model.number="bc.height_cm">
                            <div class="invalid-feedback" x-text="errText('height_cm')"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Weight (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="20" max="350" class="form-control form-control-sm"
                                :class="err('weight_kg') && 'is-invalid'" x-model.number="bc.weight_kg">
                            <div class="invalid-feedback" x-text="errText('weight_kg')"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Body Type <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm"
                                :class="err('body_type') && 'is-invalid'" x-model="bc.body_type">
                                <option value="Normal">Normal</option>
                                <option value="Athletic">Athletic</option>
                            </select>
                            <div class="invalid-feedback" x-text="errText('body_type')"></div>
                        </div>
                    </div>
                </div>

                {{-- Optional metrics (collapsible) --}}
                <div class="mt-1">
                    <button type="button" class="btn btn-link btn-sm px-0" @click="showMetrics = !showMetrics">
                        <i class="fas" :class="showMetrics ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        Optional composition metrics
                    </button>
                </div>
                <div x-show="showMetrics" x-collapse>
                    <div class="row">
                        <template x-for="f in metricFields" :key="f.key">
                            <div class="col-6">
                                <div class="form-group mb-2">
                                    <label class="mb-1 small" x-text="f.label"></label>
                                    <input type="number" step="0.1" class="form-control form-control-sm"
                                        :class="err(f.key) && 'is-invalid'" x-model.number="bc[f.key]">
                                    <div class="invalid-feedback" x-text="errText(f.key)"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Optional segmental (collapsible) --}}
                <div class="mt-1">
                    <button type="button" class="btn btn-link btn-sm px-0" @click="showSegmental = !showSegmental">
                        <i class="fas" :class="showSegmental ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        Optional segmental metrics
                    </button>
                </div>
                <div x-show="showSegmental" x-collapse>
                    <template x-for="seg in bcSegments" :key="seg.key">
                        <div class="mb-2">
                            <label class="small font-weight-bold mb-1" x-text="seg.label"></label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" step="0.1" class="form-control form-control-sm mb-1"
                                        :placeholder="'Muscle mass kg'" x-model.number="bc[seg.key + '_muscle_mass_kg']">
                                </div>
                                <div class="col-6">
                                    <input type="number" step="0.1" class="form-control form-control-sm mb-1"
                                        :placeholder="'Fat mass kg'" x-model.number="bc[seg.key + '_fat_mass_kg']">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- ================= FILE UPLOAD ================= --}}
        <template x-if="bc.entry_method === 'file_upload'">
            <div>
                <hr class="my-2">
                <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">
                    <i class="fas fa-file-csv mr-1"></i> Tanita Export File <span class="text-danger">*</span>
                </h6>
                <div class="form-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="tanitaFile"
                            accept=".csv,.xls,.xlsx"
                            :class="err('tanita_file') && 'is-invalid'"
                            @change="onFileChange($event)">
                        <label class="custom-file-label text-truncate" for="tanitaFile"
                            x-text="fileName || 'Choose .csv / .xls / .xlsx (≤ 10 MB)'"></label>
                        <div class="invalid-feedback" x-text="errText('tanita_file')"></div>
                    </div>
                    <small class="form-text text-muted">
                        Metric fields are ignored in this mode; values are extracted from the file.
                    </small>
                </div>
            </div>
        </template>

        {{-- ---------- New-patient object (collapsible, both methods) ---------- --}}
        <div class="mt-2">
            <button type="button" class="btn btn-link btn-sm px-0" @click="showPatient = !showPatient">
                <i class="fas" :class="showPatient ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                New-patient details (used only if reference is new)
            </button>
        </div>
        <div x-show="showPatient" x-collapse>
            <div class="row">
                <div class="col-6">
                    <div class="form-group mb-2">
                        <label class="mb-1 small">First name</label>
                        <input type="text" class="form-control form-control-sm" x-model="bc.patient.firstname">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mb-2">
                        <label class="mb-1 small">Last name</label>
                        <input type="text" class="form-control form-control-sm" x-model="bc.patient.lastname">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group mb-2">
                        <label class="mb-1 small">Email</label>
                        <input type="email" class="form-control form-control-sm"
                            :class="err('patient.email') && 'is-invalid'" x-model="bc.patient.email">
                        <div class="invalid-feedback" x-text="errText('patient.email')"></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mb-2">
                        <label class="mb-1 small">Date of birth</label>
                        <input type="date" class="form-control form-control-sm" :max="today"
                            x-model="bc.patient.dob">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mb-2">
                        <label class="mb-1 small">Gender</label>
                        <select class="form-control form-control-sm" x-model="bc.patient.gender">
                            <option value="">—</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mb-2">
                        <label class="mb-1 small">Mobile</label>
                        <input type="text" class="form-control form-control-sm" x-model="bc.patient.mobile">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mb-2">
                        <label class="mb-1 small">Postcode</label>
                        <input type="text" class="form-control form-control-sm" x-model="bc.patient.postcode">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        <button type="button" class="btn btn-sm btn-outline-secondary" @click="resetBc()">
            <i class="fas fa-undo mr-1"></i> Reset
        </button>
        <div>
            <button type="button" class="btn btn-sm btn-light" @click="loadBcSample()" :disabled="submitting">
                <i class="fas fa-magic mr-1"></i> Sample
            </button>
            <button type="button" class="btn btn-sm btn-primary" @click="submitBc()"
                :disabled="submitting || !api.configured">
                <span x-show="!submitting"><i class="fas fa-paper-plane mr-1"></i> Submit to API</span>
                <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-1"></i> Submitting…</span>
            </button>
        </div>
    </div>
</div>
