{{-- ===================================================================== --}}
{{-- RESULT PANEL: BODY COMPOSITION                                          --}}
{{-- Reflects the external API response structure. Shows a local preview     --}}
{{-- until a live result arrives, then renders the real API response.        --}}
{{-- ===================================================================== --}}
<div x-show="mode === 'body'" x-transition.opacity>

    {{-- ---------- Status banner ---------- --}}
    <div class="card" :class="resultCardCls">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <span class="mr-3" style="font-size:1.6rem;">
                    <i class="fas" :class="resultIcon"></i>
                </span>
                <div class="flex-grow-1">
                    <h5 class="mb-0" x-text="resultTitle"></h5>
                    <small class="text-muted" x-text="resultSubtitle"></small>
                </div>
                <template x-if="api.status">
                    <span class="badge badge-pill ml-2"
                        :class="api.ok ? 'badge-success' : 'badge-danger'"
                        x-text="'HTTP ' + api.status"></span>
                </template>
            </div>
        </div>
    </div>

    {{-- ---------- Validation errors (422) ---------- --}}
    <template x-if="Object.keys(api.errors || {}).length">
        <div class="alert alert-danger">
            <h6 class="mb-2"><i class="fas fa-ban mr-1"></i> Validation failed</h6>
            <ul class="mb-0 pl-3">
                <template x-for="(msgs, field) in api.errors" :key="field">
                    <li><strong x-text="field"></strong>: <span x-text="Array.isArray(msgs) ? msgs.join(', ') : msgs"></span></li>
                </template>
            </ul>
        </div>
    </template>

    {{-- ---------- Which dataset drives the dashboard: live API result or local preview ---------- --}}
    {{-- m = measurement object; p = patient object --}}

    {{-- Patient summary --}}
    <template x-if="view.patient">
        <div class="callout callout-info d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0"><i class="fas fa-user mr-1"></i> <span x-text="view.patient.full_name"></span></h6>
                <small class="text-muted">Ref: <span x-text="view.patient.patient_reference"></span></small>
            </div>
            <span class="badge" :class="view.patient.created ? 'badge-success' : 'badge-secondary'"
                x-text="view.patient.created ? 'New patient created' : 'Existing patient'"></span>
        </div>
    </template>

    {{-- KPI info boxes from measurement --}}
    <div class="row">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon elevation-1" :style="'background:' + (view.m.bmi_colour || '#007bff') + ';color:#fff;'">
                    <i class="fas fa-ruler-vertical"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">BMI</span>
                    <span class="info-box-number" x-text="view.m.bmi ?? '—'"></span>
                    <span class="progress-description" x-text="view.m.bmi_classification || ''"></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-dumbbell"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">FFMI</span>
                    <span class="info-box-number" x-text="view.m.ffmi ?? '—'"></span>
                    <span class="progress-description">Fat-free mass index</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-fire"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">BMR</span>
                    <span class="info-box-number" x-text="(view.m.bmr_kcal ?? '—')"></span>
                    <span class="progress-description">kcal/day</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-percentage"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Muscle Mass %</span>
                    <span class="info-box-number" x-text="(view.m.muscle_mass_percentage ?? '—')"></span>
                    <span class="progress-description">of body weight</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- HEALTH INDICATOR SLIDERS (from API computed_results)                    --}}
    {{-- ===================================================================== --}}
    <template x-if="metricSliders.length">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h mr-1"></i> Health Indicators</h3>
                <div class="card-tools"><span class="badge badge-primary">Range analysis</span></div>
            </div>
            <div class="card-body">
                <template x-for="item in metricSliders" :key="item.key">
                    <div class="mb-4">
                        {{-- Heading row: title + current value + status --}}
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                            <h6 class="mb-0 text-uppercase" style="letter-spacing:.4px;">
                                <i class="fas mr-1" :class="item.icon"></i>
                                <span x-text="item.title"></span>
                            </h6>
                            <div class="text-right">
                                <span class="h5 mb-0 mr-2"
                                    x-text="item.slider.value + (item.slider.unit ? ' ' + item.slider.unit : '')"></span>
                                <span class="badge" :style="'background:' + item.slider.colour + ';color:#fff;'"
                                    x-text="item.slider.classification"></span>
                            </div>
                        </div>

                        {{-- Pill slider: value bubble above, segmented bands, boundary ticks below --}}
                        <div class="metric-slider">
                            {{-- Value bubble (speech balloon) positioned over the marker --}}
                            <div class="metric-slider__bubble-row">
                                <div class="metric-slider__bubble"
                                    :style="'left:' + item.slider.markerPercent + '%; background:' + item.slider.markerColour + '; color:' + item.slider.markerColour + ';'">
                                    <span class="metric-slider__bubble-value" x-text="item.slider.value"></span>
                                </div>
                            </div>

                            {{-- Rounded band track --}}
                            <div class="metric-slider__track">
                                <template x-for="(band, bi) in item.slider.bands" :key="item.key + '-' + bi">
                                    <div class="metric-slider__band"
                                        :style="'width:' + band.width + '%; background:' + band.colour + ';'"
                                        :title="band.label + (band.min != null || band.max != null ? ' (' + (band.min ?? '') + (band.min != null && band.max != null ? '–' : '') + (band.max ?? '') + ')' : '')"></div>
                                </template>
                            </div>

                            {{-- Boundary scale ticks --}}
                            <div class="metric-slider__ticks">
                                <template x-for="(t, ti) in item.slider.ticks" :key="item.key + '-tick-' + ti">
                                    <span class="metric-slider__tick"
                                        :class="(t.position <= 1 && 'is-start') || (t.position >= 99 && 'is-end')"
                                        :style="'left:' + t.position + '%'" x-text="t.label"></span>
                                </template>
                            </div>
                        </div>

                        {{-- Interpretation --}}
                        <template x-if="item.interpretation">
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle mr-1"></i><span x-text="item.interpretation"></span>
                            </small>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- ===================================================================== --}}
    {{-- PHYSIQUE RATING CHART (2D grid)                                         --}}
    {{-- ===================================================================== --}}
    <template x-if="physiqueChart">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-th mr-1"></i> Physique Rating</h3>
                <div class="card-tools">
                    <span class="badge" :style="physiqueChart.current ? ('background:' + (physiqueChart.current.badge_colour || '#17a2b8') + ';color:#fff;') : ''"
                        x-text="physiqueChart.current ? (physiqueChart.current.body_type + ' · ' + physiqueChart.current.code) : ('Rating ' + physiqueChart.value)"></span>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex">
                    {{-- Y axis label --}}
                    <div class="d-flex align-items-center mr-2">
                        <span class="text-muted small font-weight-bold"
                            style="writing-mode:vertical-rl;transform:rotate(180deg);"
                            x-text="physiqueChart.axes.y"></span>
                    </div>
                    <div class="flex-grow-1" style="overflow-x:auto;">
                        <table class="physique-grid mx-auto">
                            <tbody>
                                <template x-for="(row, ri) in physiqueChart.grid" :key="'prow-' + ri">
                                    <tr>
                                        <th class="text-muted pr-2 text-right" style="white-space:nowrap;"
                                            x-text="row.fat_category"></th>
                                        <template x-for="(cell, ci) in row.cells" :key="'pcell-' + ri + '-' + ci">
                                            <td>
                                                <div class="cell d-flex align-items-center justify-content-center"
                                                    :class="cell.is_current && 'current'"
                                                    :style="'background:' + (cell.colour || row.row_colour || '#eee') + ';'"
                                                    :title="(cell.body_type || '') + ' (code ' + cell.code + ')'"
                                                    x-text="cell.code"></div>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <td class="text-muted pt-1" style="text-align:center;"
                                        :colspan="(physiqueChart.grid[0] && physiqueChart.grid[0].cells.length) || 9"
                                        x-text="physiqueChart.axes.x"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    The highlighted cell marks the patient's physique rating within the fat % / muscle mass matrix.
                </small>
            </div>
        </div>
    </template>

    {{-- Classifications & full computed metric set (rich when live result present) --}}
    <template x-if="view.live">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list-ul mr-1"></i> Computed Metrics &amp; Classifications</h3>
                <div class="card-tools"><span class="badge badge-success">From API</span></div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Classification chips --}}
                    <div class="col-md-6">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <template x-for="c in classificationRows" :key="c.label">
                                    <template x-if="c.value">
                                        <tr>
                                            <td class="text-muted" x-text="c.label"></td>
                                            <td class="text-right">
                                                <span class="badge" :style="c.colour ? ('background:' + c.colour + ';color:#fff;') : ''"
                                                    :class="!c.colour && 'badge-secondary'" x-text="c.value"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    {{-- Numeric metrics --}}
                    <div class="col-md-6">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <template x-for="r in metricRows" :key="r.label">
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
                </div>
            </div>
        </div>
    </template>

    {{-- Segmental analysis (only when present) --}}
    <template x-if="view.hasSegmental">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-child mr-1"></i> Segmental Lean Analysis</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-7"><canvas id="segmentalChart" height="180"></canvas></div>
                    <div class="col-md-5">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr><th>Segment</th><th class="text-right">Muscle (kg)</th><th class="text-right">Fat (kg)</th></tr>
                            </thead>
                            <tbody>
                                <template x-for="seg in bcSegments" :key="seg.key">
                                    <tr>
                                        <td x-text="seg.label"></td>
                                        <td class="text-right" x-text="num(segValue(seg.key, 'muscle_mass_kg'))"></td>
                                        <td class="text-right" x-text="num(segValue(seg.key, 'fat_mass_kg'))"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Raw API response (for transparency / dev reference) --}}
    <template x-if="api.raw">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-code mr-1"></i> Raw API Response</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" @click="showRaw = !showRaw">
                        <i class="fas" :class="showRaw ? 'fa-minus' : 'fa-plus'"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0" x-show="showRaw">
                <pre class="mb-0 p-3" style="max-height:320px;overflow:auto;font-size:.8rem;"
                    x-text="JSON.stringify(api.raw, null, 2)"></pre>
            </div>
        </div>
    </template>
</div>
