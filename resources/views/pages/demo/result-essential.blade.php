{{-- ===================================================================== --}}
{{-- RESULT PANEL: ESSENTIAL SHEET                                           --}}
{{-- Reflects the /api/v1/essential-sheet response. Shows a hint until a     --}}
{{-- live result arrives, then renders the computed clinical dashboard.      --}}
{{-- ===================================================================== --}}
<div x-show="mode === 'essential'" x-transition.opacity>

    {{-- ---------- Status banner ---------- --}}
    <div class="card" :class="esResultCardCls">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <span class="mr-3" style="font-size:1.6rem;">
                    <i class="fas" :class="esResultIcon"></i>
                </span>
                <div class="flex-grow-1">
                    <h5 class="mb-0" x-text="esResultTitle"></h5>
                    <small class="text-muted" x-text="esResultSubtitle"></small>
                </div>
                <template x-if="esApi.status">
                    <span class="badge badge-pill ml-2"
                        :class="esApi.ok ? 'badge-success' : 'badge-danger'"
                        x-text="'HTTP ' + esApi.status"></span>
                </template>
            </div>
        </div>
    </div>

    {{-- ---------- Validation errors (422) ---------- --}}
    <template x-if="Object.keys(esApi.errors || {}).length">
        <div class="alert alert-danger">
            <h6 class="mb-2"><i class="fas fa-ban mr-1"></i> Validation failed</h6>
            <ul class="mb-0 pl-3">
                <template x-for="(msgs, field) in esApi.errors" :key="field">
                    <li><strong x-text="field"></strong>: <span x-text="Array.isArray(msgs) ? msgs.join(', ') : msgs"></span></li>
                </template>
            </ul>
        </div>
    </template>

    {{-- Everything below renders only once a live API result is present. --}}
    <template x-if="esd">
        <div>
            {{-- ---------- Patient summary ---------- --}}
            <template x-if="esd.patient">
                <div class="callout callout-info d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0"><i class="fas fa-user mr-1"></i> <span x-text="esd.patient.full_name"></span></h6>
                        <small class="text-muted">
                            Ref: <span x-text="esd.patient.patient_reference"></span>
                            <template x-if="esd.patient.gender || esd.patient.age != null">
                                <span> · <span x-text="esd.patient.gender"></span>
                                    <template x-if="esd.patient.age != null"><span>, <span x-text="esd.patient.age"></span> yrs</span></template>
                                </span>
                            </template>
                        </small>
                    </div>
                    <template x-if="esd.measurement_date">
                        <span class="badge badge-secondary" x-text="esd.measurement_date"></span>
                    </template>
                </div>
            </template>

            {{-- ---------- Cardiac risk assessment banner ---------- --}}
            <template x-if="esd.cardiac_risk_assessment">
                <div class="callout" :class="esCardiacRisk.cls">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-heart-pulse fa-2x mr-3" :class="esCardiacRisk.icon"></i>
                        <div>
                            <h6 class="mb-0 text-uppercase" style="letter-spacing:.4px;">Cardiac Risk Assessment</h6>
                            <span class="h6 mb-0" x-text="esd.cardiac_risk_assessment"></span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- ---------- Cardiovascular KPI cards (color-coded) ---------- --}}
            <template x-if="esCardio.length">
                <div class="row">
                    <template x-for="c in esCardio" :key="c.key">
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
            <template x-if="esd.systolic_bp != null && esd.diastolic_bp != null">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-heartbeat mr-1"></i> Blood Pressure Classification</h3>
                        <div class="card-tools">
                            <span class="badge" :style="'background:' + esBpClass.colour + ';color:#fff;'"
                                x-text="esBpClass.label"></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-2">
                            <span class="display-4 font-weight-bold" :style="'color:' + esBpClass.colour"
                                x-text="esd.systolic_bp + '/' + esd.diastolic_bp"></span>
                            <span class="text-muted ml-1">mmHg</span>
                        </div>

                        {{-- Systolic band slider --}}
                        <div class="metric-slider">
                            <div class="metric-slider__bubble-row">
                                <div class="metric-slider__bubble"
                                    :style="'left:' + esSysSlider.markerPercent + '%; background:' + esSysSlider.markerColour + '; color:' + esSysSlider.markerColour + ';'">
                                    <span class="metric-slider__bubble-value" x-text="esd.systolic_bp"></span>
                                </div>
                            </div>
                            <div class="metric-slider__track">
                                <template x-for="(band, bi) in esSysSlider.bands" :key="'sys-' + bi">
                                    <div class="metric-slider__band"
                                        :style="'width:' + band.width + '%; background:' + band.colour + ';'"
                                        :title="band.label"></div>
                                </template>
                            </div>
                            <div class="metric-slider__ticks">
                                <template x-for="(t, ti) in esSysSlider.ticks" :key="'syst-' + ti">
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
                <template x-if="esd.mean_arterial_pressure != null">
                    <div class="col-6 col-xl-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-wave-square"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Mean Arterial Pressure</span>
                                <span class="info-box-number" x-text="num(esd.mean_arterial_pressure)"></span>
                                <span class="progress-description">mmHg</span>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="esd.waist_to_hip_ratio != null">
                    <div class="col-6 col-xl-3">
                        <div class="info-box">
                            <span class="info-box-icon elevation-1"
                                :style="'background:' + esWhr.colour + ';color:#fff;'"><i class="fas fa-circle-notch"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Waist-to-Hip Ratio</span>
                                <span class="info-box-number" x-text="esd.waist_to_hip_ratio"></span>
                                <span class="progress-description" x-text="esWhr.label"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="esd.chol_hdl_ratio != null">
                    <div class="col-6 col-xl-3">
                        <div class="info-box">
                            <span class="info-box-icon elevation-1"
                                :style="'background:' + esCholRatio.colour + ';color:#fff;'"><i class="fas fa-percentage"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Chol / HDL Ratio</span>
                                <span class="info-box-number" x-text="esd.chol_hdl_ratio"></span>
                                <span class="progress-description" x-text="esCholRatio.label"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="esd.ldl_hdl_ratio != null">
                    <div class="col-6 col-xl-3">
                        <div class="info-box">
                            <span class="info-box-icon elevation-1"
                                :style="'background:' + esLdlRatio.colour + ';color:#fff;'"><i class="fas fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">LDL / HDL Ratio</span>
                                <span class="info-box-number" x-text="esd.ldl_hdl_ratio"></span>
                                <span class="progress-description" x-text="esLdlRatio.label"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ---------- Lipid panel + body metrics tables ---------- --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list-ul mr-1"></i> Clinical Measures &amp; Computed Values</h3>
                    <div class="card-tools"><span class="badge badge-success">From API</span></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Lipid panel --}}
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">Lipid Panel</h6>
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <template x-for="r in esLipidRows" :key="r.label">
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
                        {{-- Measures / metabolic --}}
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;">Measures</h6>
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <template x-for="r in esMeasureRows" :key="r.label">
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

                    {{-- Alerts / flags --}}
                    <template x-if="esd.bp_alert_triggered">
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Blood pressure alert triggered.
                            <template x-if="esd.patient_advised"><span> Patient has been advised.</span></template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ---------- Raw API response ---------- --}}
            <template x-if="esApi.raw">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-code mr-1"></i> Raw API Response</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" @click="esShowRaw = !esShowRaw">
                                <i class="fas" :class="esShowRaw ? 'fa-minus' : 'fa-plus'"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0" x-show="esShowRaw">
                        <pre class="mb-0 p-3" style="max-height:320px;overflow:auto;font-size:.8rem;"
                            x-text="JSON.stringify(esApi.raw, null, 2)"></pre>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- ---------- Empty-state hint (no live result yet) ---------- --}}
    <template x-if="!esd && !Object.keys(esApi.errors || {}).length">
        <div class="card card-outline card-secondary">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-notes-medical fa-3x mb-3 d-block"></i>
                Fill in the clinical measures on the left and submit to the Essential API
                to see cardiovascular assessments, blood-pressure classification, derived
                ratios and cardiac risk.
            </div>
        </div>
    </template>
</div>
