{{-- ===================================================================== --}}
{{-- RESULT PANEL: ESSENTIAL PROGRESS SHEET                                  --}}
{{-- Reflects the /api/v1/progress-sheet response. Shows a hint until a      --}}
{{-- live result arrives, then renders the computed clinical dashboard.      --}}
{{-- ===================================================================== --}}
<div x-show="mode === 'progress'" x-transition.opacity>

    {{-- ---------- Status banner ---------- --}}
    <div class="card" :class="pgResultCardCls">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <span class="mr-3" style="font-size:1.6rem;">
                    <i class="fas" :class="pgResultIcon"></i>
                </span>
                <div class="flex-grow-1">
                    <h5 class="mb-0" x-text="pgResultTitle"></h5>
                    <small class="text-muted" x-text="pgResultSubtitle"></small>
                </div>
                <template x-if="pgApi.status">
                    <span class="badge badge-pill ml-2"
                        :class="pgApi.ok ? 'badge-success' : 'badge-danger'"
                        x-text="'HTTP ' + pgApi.status"></span>
                </template>
            </div>
        </div>
    </div>

    {{-- ---------- Validation errors (422) ---------- --}}
    <template x-if="Object.keys(pgApi.errors || {}).length">
        <div class="alert alert-danger">
            <h6 class="mb-2"><i class="fas fa-ban mr-1"></i> Validation failed</h6>
            <ul class="mb-0 pl-3">
                <template x-for="(msgs, field) in pgApi.errors" :key="field">
                    <li><strong x-text="field"></strong>: <span x-text="Array.isArray(msgs) ? msgs.join(', ') : msgs"></span></li>
                </template>
            </ul>
        </div>
    </template>

    {{-- Everything below renders only once a live API result is present. --}}
    <template x-if="pgd">
        <div>
            {{-- ---------- Patient summary ---------- --}}
            <template x-if="pgd.patient">
                <div class="callout callout-info d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0"><i class="fas fa-user mr-1"></i> <span x-text="pgd.patient.full_name"></span></h6>
                        <small class="text-muted">
                            Ref: <span x-text="pgd.patient.patient_reference"></span>
                            <template x-if="pgd.patient.gender || pgd.patient.age != null">
                                <span> · <span x-text="pgd.patient.gender"></span>
                                    <template x-if="pgd.patient.age != null"><span>, <span x-text="pgd.patient.age"></span> yrs</span></template>
                                </span>
                            </template>
                        </small>
                    </div>
                    <template x-if="pgd.measurement_date">
                        <span class="badge badge-secondary" x-text="pgd.measurement_date"></span>
                    </template>
                </div>
            </template>

            {{-- ---------- Cardiac risk assessment banner ---------- --}}
            <template x-if="pgd.cardiac_risk_assessment">
                <div class="callout" :class="pgCardiacRisk.cls">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-heart-pulse fa-2x mr-3" :class="pgCardiacRisk.icon"></i>
                        <div>
                            <h6 class="mb-0 text-uppercase" style="letter-spacing:.4px;">Cardiac Risk Assessment</h6>
                            <span class="h6 mb-0" x-text="pgd.cardiac_risk_assessment"></span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- ---------- Cardiovascular KPI cards (color-coded) ---------- --}}
            <template x-if="pgCardio.length">
                <div class="row">
                    <template x-for="c in pgCardio" :key="c.key">
                        <div class="col-6 col-xl-3">
                            <div class="info-box">
                                <span class="info-box-icon elevation-1"
                                    :style="'background:' + (c.color || '#6c757d') + ';color:#fff;'">
                                    <i class="fas" :class="c.icon"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text" x-text="c.label"></span>
                                    <span class="info-box-number">
                                        <span x-text="c.value ?? '—'"></span>
                                        <small class="text-muted" x-text="c.unit"></small>
                                    </span>
                                    <span class="badge mt-1" :style="'background:' + (c.color || '#6c757d') + ';color:#fff;'"
                                        x-text="c.status"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- ---------- Blood pressure classification slider ---------- --}}
            <template x-if="pgd.systolic_bp != null && pgd.diastolic_bp != null">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-heartbeat mr-1"></i> Blood Pressure Classification</h3>
                        <div class="card-tools">
                            <span class="badge" :style="'background:' + pgBpClass.colour + ';color:#fff;'"
                                x-text="pgBpClass.label"></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-2">
                            <span class="display-4 font-weight-bold" :style="'color:' + pgBpClass.colour"
                                x-text="pgd.systolic_bp + '/' + pgd.diastolic_bp"></span>
                            <span class="text-muted ml-1">mmHg</span>
                        </div>

                        <div class="metric-slider">
                            <div class="metric-slider__bubble-row">
                                <div class="metric-slider__bubble"
                                    :style="'left:' + pgSysSlider.markerPercent + '%; background:' + pgSysSlider.markerColour + '; color:' + pgSysSlider.markerColour + ';'">
                                    <span class="metric-slider__bubble-value" x-text="pgd.systolic_bp"></span>
                                </div>
                            </div>
                            <div class="metric-slider__track">
                                <template x-for="(band, bi) in pgSysSlider.bands" :key="'psys-' + bi">
                                    <div class="metric-slider__band"
                                        :style="'width:' + band.width + '%; background:' + band.colour + ';'"
                                        :title="band.label"></div>
                                </template>
                            </div>
                            <div class="metric-slider__ticks">
                                <template x-for="(t, ti) in pgSysSlider.ticks" :key="'psyst-' + ti">
                                    <span class="metric-slider__tick"
                                        :class="(t.position <= 1 && 'is-start') || (t.position >= 99 && 'is-end')"
                                        :style="'left:' + t.position + '%'" x-text="t.label"></span>
                                </template>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle mr-1"></i> Systolic reading placed on the standard BP classification scale.
                        </small>
                    </div>
                </div>
            </template>

            {{-- ---------- Derived ratios + MAP info boxes ---------- --}}
            <div class="row">
                <template x-if="pgd.mean_arterial_pressure != null">
                    <div class="col-6 col-xl-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-wave-square"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Mean Arterial Pressure</span>
                                <span class="info-box-number" x-text="num(pgd.mean_arterial_pressure)"></span>
                                <span class="progress-description">mmHg</span>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="pgd.waist_to_hip_ratio != null">
                    <div class="col-6 col-xl-3">
                        <div class="info-box">
                            <span class="info-box-icon elevation-1"
                                :style="'background:' + pgWhr.colour + ';color:#fff;'"><i class="fas fa-circle-notch"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Waist-to-Hip Ratio</span>
                                <span class="info-box-number" x-text="pgd.waist_to_hip_ratio"></span>
                                <span class="progress-description" x-text="pgWhr.label"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="pgd.chol_hdl_ratio != null">
                    <div class="col-6 col-xl-3">
                        <div class="info-box">
                            <span class="info-box-icon elevation-1"
                                :style="'background:' + pgCholRatio.colour + ';color:#fff;'"><i class="fas fa-percentage"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Chol / HDL Ratio</span>
                                <span class="info-box-number" x-text="pgd.chol_hdl_ratio"></span>
                                <span class="progress-description" x-text="pgCholRatio.label"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="pgd.ldl_hdl_ratio != null">
                    <div class="col-6 col-xl-3">
                        <div class="info-box">
                            <span class="info-box-icon elevation-1"
                                :style="'background:' + pgLdlRatio.colour + ';color:#fff;'"><i class="fas fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">LDL / HDL Ratio</span>
                                <span class="info-box-number" x-text="pgd.ldl_hdl_ratio"></span>
                                <span class="progress-description" x-text="pgLdlRatio.label"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ---------- Progress / lifestyle notes ---------- --}}
            <template x-if="pgNotes.length">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clipboard-check mr-1"></i> Progress Notes</h3>
                    </div>
                    <div class="card-body">
                        <template x-for="note in pgNotes" :key="note.label">
                            <div class="mb-2">
                                <h6 class="mb-1 text-muted"><i class="fas mr-1" :class="note.icon"></i>
                                    <span x-text="note.label"></span></h6>
                                <p class="mb-0" x-text="note.value"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- ---------- Lipid panel + body metrics tables ---------- --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list-ul mr-1"></i> Clinical Measures &amp; Computed Values</h3>
                    <div class="card-tools"><span class="badge badge-success">From API</span></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">Lipid Panel</h6>
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <template x-for="r in pgLipidRows" :key="r.label">
                                        <template x-if="r.value !== null && r.value !== undefined">
                                            <tr>
                                                <td class="text-muted" x-text="r.label"></td>
                                                <td class="text-right"><strong x-text="r.value + (r.unit ? ' ' + r.unit : '')"></strong></td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">Measures</h6>
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <template x-for="r in pgMeasureRows" :key="r.label">
                                        <template x-if="r.value !== null && r.value !== undefined && r.value !== ''">
                                            <tr>
                                                <td class="text-muted" x-text="r.label"></td>
                                                <td class="text-right"><strong x-text="r.value + (r.unit ? ' ' + r.unit : '')"></strong></td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <template x-if="pgd.bp_alert_triggered">
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Blood pressure alert triggered.
                            <template x-if="pgd.patient_advised"><span> Patient has been advised.</span></template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ---------- Raw API response ---------- --}}
            <template x-if="pgApi.raw">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-code mr-1"></i> Raw API Response</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" @click="pgShowRaw = !pgShowRaw">
                                <i class="fas" :class="pgShowRaw ? 'fa-minus' : 'fa-plus'"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0" x-show="pgShowRaw">
                        <pre class="mb-0 p-3" style="max-height:320px;overflow:auto;font-size:.8rem;"
                            x-text="JSON.stringify(pgApi.raw, null, 2)"></pre>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- ---------- Empty-state hint (no live result yet) ---------- --}}
    <template x-if="!pgd && !Object.keys(pgApi.errors || {}).length">
        <div class="card card-outline card-secondary">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-chart-line fa-3x mb-3 d-block"></i>
                Record the follow-up visit on the left and submit to the Essential Progress API
                to see cardiovascular assessments, blood-pressure classification, derived ratios,
                cardiac risk and the patient's progress notes.
            </div>
        </div>
    </template>
</div>
