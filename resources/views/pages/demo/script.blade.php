{{-- ===================================================================== --}}
{{-- DEMO ENGINE: Alpine component + Chart.js wiring                         --}}
{{-- ===================================================================== --}}
@push('scripts')
    {{-- Chart.js (bundled with the AdminLTE plugins) --}}
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

    <style>
        [x-cloak] { display: none !important; }
        .custom-range { width: 100%; }

        /* ---- Slider-style indicator bars (pill + value bubble) ---- */
        .metric-slider { margin-bottom: .35rem; }

        /* Row that reserves space for the floating value bubble */
        .metric-slider__bubble-row {
            position: relative;
            height: 30px;
        }
        .metric-slider__bubble {
            position: absolute;
            bottom: 2px;
            transform: translateX(-50%);
            font-weight: 700;
            font-size: .8rem;
            line-height: 1;
            padding: 5px 10px;
            border-radius: 14px;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0,0,0,.2);
            z-index: 3;
            transition: left .35s ease, background .35s ease, color .35s ease;
        }
        .metric-slider__bubble-value { color: #fff; }
        /* Downward pointer under the bubble */
        .metric-slider__bubble::after {
            content: '';
            position: absolute;
            left: 50%; bottom: -6px;
            transform: translateX(-50%);
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-top: 7px solid currentColor; /* inherits band colour via color */
        }

        .metric-slider__track {
            position: relative;
            height: 16px;
            border-radius: 999px;
            overflow: hidden;
            display: flex;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,.05);
        }
        .metric-slider__band {
            height: 100%;
            min-width: 0;
            transition: filter .15s ease, width .35s ease;
        }
        .metric-slider__band:hover { filter: brightness(1.07); }

        .metric-slider__ticks {
            position: relative;
            height: 16px;
            margin-top: 4px;
            font-size: .72rem;
            color: #6c757d;
        }
        .metric-slider__tick {
            position: absolute;
            transform: translateX(-50%);
            white-space: nowrap;
        }
        .metric-slider__tick.is-start { transform: translateX(0); }
        .metric-slider__tick.is-end { transform: translateX(-100%); }

        /* ---- Physique rating grid ---- */
        .physique-grid { border-collapse: separate; border-spacing: 2px; }
        .physique-grid td, .physique-grid th { text-align: center; font-size: .7rem; }
        .physique-grid .cell {
            width: 34px; height: 30px;
            border-radius: 3px;
            color: #333;
            position: relative;
        }
        .physique-grid .cell.current {
            outline: 3px solid #111;
            outline-offset: -1px;
            font-weight: 700;
            z-index: 2;
        }
    </style>

    {{--
        Alpine.js powers the real-time binding on this page. The project ships
        Alpine via Vite (resources/js/app.js), but the AdminLTE layout does not
        emit @vite, so no page currently loads it. To keep this demo self-contained
        and consistent with how the layout loads its other libraries (direct
        <script> tags), Alpine 3 is loaded here from a CDN, scoped to this page.
        The component factory is defined before Alpine's deferred init.
    --}}
    <script>
        function bodyCompositionDemo(config) {
            config = config || {};

            return {
                mode: 'body',

                /* ===================================================== */
                /* API INTEGRATION (Body Composition mode)               */
                /* ===================================================== */
                api: {
                    configured: !!config.apiConfigured,
                    url: config.storeUrl || '',
                    essentialUrl: config.essentialStoreUrl || '',
                    progressUrl: config.progressStoreUrl || '',
                    ok: false,
                    status: 0,
                    errors: {},
                    raw: null,
                },
                submitting: false,
                fileName: '',
                file: null,
                showMetrics: false,
                showSegmental: false,
                showPatient: false,
                showRaw: true,
                today: new Date().toISOString().slice(0, 10),

                /* Optional metric fields (label + key -> matches API spec) */
                metricFields: [
                    { key: 'body_fat_percentage', label: 'Body Fat %' },
                    { key: 'fat_mass_kg', label: 'Fat Mass (kg)' },
                    { key: 'lean_body_mass_kg', label: 'Lean Body Mass (kg)' },
                    { key: 'muscle_mass_kg', label: 'Muscle Mass (kg)' },
                    { key: 'visceral_fat_score', label: 'Visceral Fat Score' },
                    { key: 'bone_mass_kg', label: 'Bone Mass (kg)' },
                    { key: 'total_body_water_percentage', label: 'Total Body Water %' },
                    { key: 'total_body_water_kg', label: 'Total Body Water (kg)' },
                    { key: 'extracellular_water_kg', label: 'Extracellular Water (kg)' },
                    { key: 'intracellular_water_kg', label: 'Intracellular Water (kg)' },
                    { key: 'basal_metabolic_rate', label: 'BMR (kcal)' },
                    { key: 'metabolic_age', label: 'Metabolic Age' },
                    { key: 'phase_angle', label: 'Phase Angle' },
                    { key: 'physique_rating', label: 'Physique Rating (1-9)' },
                ],

                bcSegments: [
                    { key: 'left_arm', label: 'Left Arm' },
                    { key: 'right_arm', label: 'Right Arm' },
                    { key: 'trunk', label: 'Trunk' },
                    { key: 'left_leg', label: 'Left Leg' },
                    { key: 'right_leg', label: 'Right Leg' },
                ],

                bc: {},
                result: null, // last successful API response `data`

                bcDefaults() {
                    return {
                        patient_reference: 'PTR-000123',
                        entry_method: 'manual_entry',
                        device_model: 'Tanita MC-780',
                        measurement_date: new Date().toISOString().slice(0, 10),
                        gender: 'Female',
                        height_cm: 168,
                        weight_kg: 68,
                        body_type: 'Normal',
                        body_fat_percentage: 28,
                        total_body_water_percentage: 52,
                        visceral_fat_score: 5,
                        metabolic_age: 30,
                        physique_rating: 5,
                        left_arm_muscle_mass_kg: 2.1, left_arm_fat_mass_kg: 0.4,
                        right_arm_muscle_mass_kg: 2.2, right_arm_fat_mass_kg: 0.4,
                        trunk_muscle_mass_kg: 22.0, trunk_fat_mass_kg: 6.0,
                        left_leg_muscle_mass_kg: 8.1, left_leg_fat_mass_kg: 2.0,
                        right_leg_muscle_mass_kg: 8.3, right_leg_fat_mass_kg: 2.0,
                        patient: {
                            firstname: 'Jane', lastname: 'Doe', email: '',
                            postcode: '', telephone: '', mobile: '',
                            dob: '', gender: 'Female', patient_code: '', external_patient_id: '',
                        },
                    };
                },

                resetBc() {
                    this.bc = this.bcDefaults();
                    this.clearResult();
                },
                clearResult() {
                    this.result = null;
                    this.api.ok = false; this.api.status = 0; this.api.errors = {}; this.api.raw = null;
                    this.fileName = ''; this.file = null;
                },
                loadBcSample() {
                    this.bc = Object.assign(this.bcDefaults(), {
                        patient_reference: 'PTR-000987',
                        gender: 'Male', height_cm: 178, weight_kg: 72.4, body_type: 'Normal',
                        body_fat_percentage: 18.2, total_body_water_percentage: 58.3,
                        visceral_fat_score: 5, metabolic_age: 28, physique_rating: 6,
                        patient: Object.assign(this.bcDefaults().patient, { firstname: 'Alex', lastname: 'Rivera', gender: 'Male' }),
                    });
                    this.clearResult();
                },

                onFileChange(e) {
                    const f = e.target.files[0];
                    this.file = f || null;
                    this.fileName = f ? f.name : '';
                },

                err(field) { return !!(this.api.errors && this.api.errors[field]); },
                errText(field) {
                    const e = this.api.errors && this.api.errors[field];
                    return Array.isArray(e) ? e.join(' ') : (e || '');
                },

                /* Build the manual-entry JSON payload (drop empty values) */
                buildManualPayload() {
                    const out = {};
                    const copy = ['patient_reference', 'entry_method', 'device_model', 'measurement_date',
                        'gender', 'height_cm', 'weight_kg', 'body_type'];
                    copy.forEach(k => { if (this.bc[k] !== '' && this.bc[k] != null) out[k] = this.bc[k]; });
                    this.metricFields.forEach(f => {
                        if (this.bc[f.key] !== '' && this.bc[f.key] != null) out[f.key] = this.bc[f.key];
                    });
                    this.bcSegments.forEach(s => {
                        ['muscle_mass_kg', 'fat_mass_kg', 'muscle_score', 'fat_score'].forEach(suf => {
                            const k = s.key + '_' + suf;
                            if (this.bc[k] !== '' && this.bc[k] != null) out[k] = this.bc[k];
                        });
                    });
                    const patient = {};
                    Object.entries(this.bc.patient || {}).forEach(([k, v]) => { if (v !== '' && v != null) patient[k] = v; });
                    if (Object.keys(patient).length) out.patient = patient;
                    out.entry_method = 'manual_entry';
                    return out;
                },

                async submitBc() {
                    if (!this.api.configured || this.submitting) return;
                    this.submitting = true;
                    this.api.errors = {};

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    let opts;

                    if (this.bc.entry_method === 'file_upload') {
                        if (!this.file) {
                            this.api.errors = { tanita_file: ['A Tanita export file is required.'] };
                            this.submitting = false;
                            return;
                        }
                        const fd = new FormData();
                        fd.append('entry_method', 'file_upload');
                        fd.append('patient_reference', this.bc.patient_reference || '');
                        if (this.bc.device_model) fd.append('device_model', this.bc.device_model);
                        Object.entries(this.bc.patient || {}).forEach(([k, v]) => {
                            if (v !== '' && v != null) fd.append('patient[' + k + ']', v);
                        });
                        fd.append('tanita_file', this.file);
                        opts = { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: fd };
                    } else {
                        opts = {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.buildManualPayload()),
                        };
                    }

                    try {
                        const res = await fetch(this.api.url, opts);
                        const json = await res.json().catch(() => ({}));
                        this.api.raw = json;
                        this.api.ok = !!json.ok;
                        this.api.status = json.status || res.status;
                        this.api.errors = json.errors || {};

                        if (json.ok && json.data) {
                            this.result = json.data;
                            this.toast('success', json.message || 'Record created successfully.');
                            this.$nextTick(() => this.renderCharts());
                        } else {
                            this.result = null;
                            this.toast('error', json.message || 'Submission failed.');
                        }
                    } catch (ex) {
                        this.api.ok = false; this.api.status = 0;
                        this.toast('error', 'Network error contacting the server.');
                    } finally {
                        this.submitting = false;
                    }
                },

                toast(icon, title) {
                    if (window.Swal) {
                        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500 })
                            .fire({ icon, title });
                    }
                },

                physiqueLabel(r) {
                    const map = { 1: 'Hidden Obese', 2: 'Obese', 3: 'Solidly-built', 4: 'Under-exercised',
                        5: 'Standard', 6: 'Standard Muscular', 7: 'Thin', 8: 'Thin & Muscular', 9: 'Very Muscular' };
                    return map[r] || '';
                },

                /* -------- Result-vs-preview selector for the dashboard -------- */
                /* When a live API result exists, prefer its measurement values;  */
                /* otherwise compute a local preview from the form inputs.        */
                get view() {
                    const m = this.result?.measurement || {};
                    const p = this.result?.patient || null;

                    const weight = Number(this.bc.weight_kg) || 0;
                    const bodyFat = Number(this.bc.body_fat_percentage) || 0;
                    const waterPct = Number(this.bc.total_body_water_percentage) || 0;

                    const fatMass = weight * bodyFat / 100;
                    const leanMass = weight - fatMass;
                    const waterMass = weight * waterPct / 100;
                    const dryLeanMass = Math.max(0, leanMass - waterMass);

                    const male = this.bc.gender === 'Male';
                    const hasFormSeg = this.bcSegments.some(s =>
                        (Number(this.bc[s.key + '_muscle_mass_kg']) || 0) > 0 ||
                        (Number(this.bc[s.key + '_fat_mass_kg']) || 0) > 0);

                    // Prefer API segmental object; fall back to form values.
                    const apiSeg = m.segmental || null;
                    const hasApiSeg = apiSeg && this.bcSegments.some(s =>
                        apiSeg[s.key] && (apiSeg[s.key].muscle_mass_kg != null || apiSeg[s.key].fat_mass_kg != null));

                    // Composition masses: prefer live values, else local preview.
                    const vFatMass = m.fat_mass_kg != null ? Number(m.fat_mass_kg) : fatMass;
                    const vWaterMass = m.total_body_water_kg != null ? Number(m.total_body_water_kg) : waterMass;
                    const vLean = m.fat_free_mass_kg != null ? Number(m.fat_free_mass_kg)
                        : (m.lean_body_mass_kg != null ? Number(m.lean_body_mass_kg) : leanMass);
                    const vDryLean = Math.max(0, vLean - vWaterMass);

                    return {
                        patient: p,
                        m: m,
                        cr: this.result?.computed_results || null,
                        live: !!this.result,
                        bodyFat: m.body_fat_percentage != null ? Number(m.body_fat_percentage) : bodyFat,
                        waterPct: m.total_body_water_percentage != null ? Number(m.total_body_water_percentage) : waterPct,
                        fatMass: vFatMass, waterMass: vWaterMass, dryLeanMass: vDryLean, leanMass: vLean,
                        apiSeg,
                        hasSegmental: hasApiSeg || hasFormSeg,
                        fatCat: this.bcFatCat(m.body_fat_percentage != null ? Number(m.body_fat_percentage) : bodyFat, male),
                    };
                },
                segValue(segKey, metric) {
                    // Returns the segmental metric from API result if present, else the form input.
                    const apiSeg = this.view.apiSeg;
                    if (apiSeg && apiSeg[segKey] && apiSeg[segKey][metric] != null) {
                        return Number(apiSeg[segKey][metric]);
                    }
                    return Number(this.bc[segKey + '_' + metric]) || 0;
                },
                bcFatCat(f, male) {
                    const lowCut = male ? 14 : 21, highCut = male ? 24 : 31;
                    if (f < (male ? 6 : 14)) return { label: 'Essential', bar: 'bg-info', badge: 'badge-info' };
                    if (f <= lowCut) return { label: 'Athletic', bar: 'bg-success', badge: 'badge-success' };
                    if (f <= highCut) return { label: 'Fitness', bar: 'bg-primary', badge: 'badge-primary' };
                    if (f <= highCut + 8) return { label: 'Average', bar: 'bg-warning', badge: 'badge-warning' };
                    return { label: 'High', bar: 'bg-danger', badge: 'badge-danger' };
                },

                /* ===================================================== */
                /* SLIDER-STYLE INDICATOR BUILDERS                       */
                /* Consume the API `computed_results` structures and      */
                /* normalise them into a uniform slider model the UI      */
                /* can render consistently.                               */
                /* ===================================================== */

                /* Parse a textual range ("≤ 18.49", "18.50 – 24.90", "> 27", "< 57%", "≥ 13") */
                parseRange(text) {
                    if (text == null) return { min: null, max: null };
                    const s = String(text).replace(/%/g, '').replace(/,/g, '').trim();
                    const nums = (s.match(/-?\d+(\.\d+)?/g) || []).map(Number);

                    if (/^(≤|<=|<)/.test(s)) return { min: null, max: nums[0] };
                    if (/^(≥|>=|>)/.test(s)) return { min: nums[0], max: null };
                    if (nums.length >= 2) return { min: nums[0], max: nums[1] };
                    if (nums.length === 1) return { min: nums[0], max: nums[0] };
                    return { min: null, max: null };
                },

                /* Clamp a numeric value to a 0–100% position on [min,max] */
                posPercent(value, min, max) {
                    if (max === min) return 0;
                    return Math.max(0, Math.min(100, ((value - min) / (max - min)) * 100));
                },

                /*
                 * Build a slider model from API band data.
                 * opts: { value, unit, classification, colour, scaleMin, scaleMax,
                 *         bands:[{label,colour,min,max,width?}], marker? }
                 * Bands with null min/max are treated as open-ended against the scale.
                 */
                buildSlider(opts) {
                    const sMin = opts.scaleMin, sMax = opts.scaleMax;
                    const span = (sMax - sMin) || 1;

                    const bands = (opts.bands || []).map(b => {
                        const bMin = b.min == null ? sMin : b.min;
                        const bMax = b.max == null ? sMax : b.max;
                        const left = this.posPercent(bMin, sMin, sMax);
                        const right = this.posPercent(bMax, sMin, sMax);
                        return {
                            label: b.label,
                            colour: b.colour,
                            left,
                            width: (b.width != null ? b.width : Math.max(0, right - left)),
                            min: b.min, max: b.max,
                        };
                    });

                    const value = Number(opts.value);
                    const markerPercent = opts.marker != null
                        ? opts.marker
                        : this.posPercent(value, sMin, sMax);

                    // Boundary ticks: start of scale + each band's upper edge, mapped to %.
                    const ticks = this.boundaryTicks(bands, sMin, sMax);

                    // Which band is the marker sitting in (for the value-bubble colour)?
                    const markerColour = this.markerBandColour(bands, markerPercent) || opts.colour || '#6c757d';

                    return {
                        value,
                        unit: opts.unit || '',
                        classification: opts.classification || '',
                        colour: opts.colour || '#6c757d',
                        min: sMin, max: sMax,
                        bands,
                        markerPercent,
                        markerColour,
                        ticks,
                    };
                },

                /* Derive scale-boundary tick labels from the band edges. */
                boundaryTicks(bands, sMin, sMax) {
                    if (!bands.length) return [];
                    const ticks = [];
                    // Leading edge = scale min.
                    ticks.push({ label: this.tidy(sMin), position: 0 });
                    let acc = 0;
                    bands.forEach((b, i) => {
                        acc += b.width;
                        const pos = Math.max(0, Math.min(100, acc));
                        const last = i === bands.length - 1;
                        // The upper numeric edge of this band; open-ended last band => scale max + '+'.
                        let edge = b.max;
                        if (edge == null) edge = sMax;
                        const label = (last && b.max == null) ? (this.tidy(edge) + '+') : this.tidy(edge);
                        ticks.push({ label, position: pos });
                    });
                    // De-duplicate ticks that land on the same position (rounding).
                    return ticks.filter((t, i, a) =>
                        i === 0 || Math.abs(t.position - a[i - 1].position) > 0.5 || t.label !== a[i - 1].label);
                },
                tidy(n) {
                    const v = Number(n);
                    if (!isFinite(v)) return n;
                    return Math.round(v * 100) / 100;
                },
                markerBandColour(bands, markerPercent) {
                    let acc = 0;
                    for (const b of bands) {
                        acc += b.width;
                        if (markerPercent <= acc + 0.001) return b.colour;
                    }
                    return bands.length ? bands[bands.length - 1].colour : null;
                },

                /* Each entry: a fully-resolved slider model + metadata for the card. */
                get metricSliders() {
                    const cr = this.view.cr;
                    if (!cr) return [];
                    const out = [];

                    /* ---- BMI ---- */
                    if (cr.bmi) {
                        const b = cr.bmi;
                        const scale = b.scale || { min: 15, max: 35 };
                        out.push({
                            key: 'bmi',
                            title: 'BMI (Body Mass Index)',
                            icon: 'fa-ruler-vertical',
                            interpretation: b.formula ? ('Formula: ' + b.formula) : null,
                            slider: this.buildSlider({
                                value: b.value, unit: b.unit, classification: b.classification, colour: b.colour,
                                scaleMin: scale.min, scaleMax: scale.max,
                                bands: (b.bands || []).map(x => {
                                    const r = this.parseRange(x.range);
                                    return { label: x.label, colour: x.colour, min: r.min, max: r.max };
                                }),
                            }),
                        });
                    }

                    /* ---- FFMI ----
                       Prefer the clinical_reference ranges (Too low / Slightly low /
                       Optimal / Slightly high) rendered on a 10–25 scale — this gives
                       the red/orange/green/orange boundary look with edges 10,14,15,19,25+.
                       Falls back to the detailed enhancement slider if no reference. */
                    if (cr.ffmi && cr.ffmi.available !== false) {
                        const f = cr.ffmi;
                        const ref = f.clinical_reference;
                        if (ref && ref.ranges && ref.ranges.length) {
                            out.push({
                                key: 'ffmi',
                                title: 'FFMI (Fat-Free Mass Index)',
                                icon: 'fa-dumbbell',
                                interpretation: 'Clinical reference (' + (ref.gender || '') + '): optimal 15–19',
                                slider: this.buildSlider({
                                    value: f.value, unit: f.unit || 'kg/m²',
                                    classification: f.classification, colour: f.colour,
                                    scaleMin: 10, scaleMax: 25,
                                    bands: ref.ranges.map(x => { const r = this.parseRange(x.range); return { label: x.label, colour: x.colour, min: r.min, max: r.max }; }),
                                }),
                            });
                        } else if (f.slider) {
                            const sl = f.slider;
                            out.push({
                                key: 'ffmi',
                                title: 'FFMI (Fat-Free Mass Index)',
                                icon: 'fa-dumbbell',
                                interpretation: f.formula ? ('Formula: ' + f.formula) : null,
                                slider: {
                                    value: Number(f.value), unit: f.unit || 'kg/m²',
                                    classification: f.classification, colour: f.colour || (sl.marker && sl.marker.colour) || '#6c757d',
                                    min: sl.scale.min, max: sl.scale.max,
                                    bands: (sl.bands || []).map(x => ({ label: x.label, colour: x.colour, width: x.width_percent, min: x.min, max: x.max })),
                                    markerPercent: (sl.marker && sl.marker.position_percent != null) ? sl.marker.position_percent : this.posPercent(Number(f.value), sl.scale.min, sl.scale.max),
                                    markerColour: (sl.marker && sl.marker.colour) || f.colour || '#6c757d',
                                    ticks: this.boundaryTicks((sl.bands || []).map(x => ({ width: x.width_percent, max: x.max })), sl.scale.min, sl.scale.max),
                                },
                            });
                        }
                    }

                    /* ---- Muscle Mass % ---- */
                    if (cr.muscle_mass_percentage && cr.muscle_mass_percentage.available !== false) {
                        const mm = cr.muscle_mass_percentage;
                        const ranges = (mm.reference && mm.reference.ranges) || [];
                        // Derive a sensible scale from the reference ranges.
                        const nums = ranges.flatMap(x => { const r = this.parseRange(x.range); return [r.min, r.max].filter(v => v != null); });
                        const sMin = nums.length ? Math.floor(Math.min(...nums) - 5) : 40;
                        const sMax = nums.length ? Math.ceil(Math.max(...nums) + 10) : 100;
                        out.push({
                            key: 'muscle_mass_percentage',
                            title: 'Muscle Mass Percentage',
                            icon: 'fa-percentage',
                            interpretation: (mm.reference ? ('Reference: ' + (mm.reference.gender || '') + ', age ' + (mm.reference.age ?? '—')) : null),
                            slider: this.buildSlider({
                                value: mm.value, unit: mm.unit, classification: mm.classification, colour: mm.colour,
                                scaleMin: sMin, scaleMax: sMax,
                                bands: ranges.map(x => { const r = this.parseRange(x.range); return { label: x.label, colour: x.colour, min: r.min, max: r.max }; }),
                            }),
                        });
                    }

                    /* ---- Visceral Fat ---- */
                    const vf = cr.body_composition_analysis && cr.body_composition_analysis.visceral_fat;
                    if (vf) {
                        const ranges = vf.bands || [];
                        out.push({
                            key: 'visceral_fat',
                            title: 'Visceral Fat',
                            icon: 'fa-circle-notch',
                            interpretation: 'Lower scores indicate lower visceral fat risk.',
                            slider: this.buildSlider({
                                value: vf.score, unit: '', classification: vf.classification, colour: vf.colour,
                                scaleMin: 1, scaleMax: 20,
                                bands: ranges.map(x => { const r = this.parseRange(x.range); return { label: x.label, colour: x.colour, min: r.min, max: r.max }; }),
                            }),
                        });
                    }

                    return out;
                },

                /* ---- Physique Rating grid (2D chart) ---- */
                get physiqueChart() {
                    const pr = this.view.cr && this.view.cr.physique_rating;
                    if (!pr || !pr.chart) return null;
                    return {
                        value: pr.value,
                        current: pr.current || null,
                        axes: pr.chart.axes || { x: 'MUSCLE MASS', y: 'FAT %' },
                        muscleBands: pr.chart.muscle_bands || [],
                        grid: pr.chart.grid || [],
                    };
                },
                get bmiBarWidth() {
                    const bmi = Number(this.view.m.bmi) ||
                        (Number(this.bc.weight_kg) / Math.pow((Number(this.bc.height_cm) || 1) / 100, 2));
                    return Math.max(4, Math.min(100, (bmi / 40) * 100));
                },
                get classificationRows() {
                    const m = this.view.m || {};
                    return [
                        { label: 'BMI', value: m.bmi_classification, colour: m.bmi_colour },
                        { label: 'Body Fat', value: m.body_fat_classification, colour: m.body_fat_colour },
                        { label: 'FFMI', value: m.ffmi_classification, colour: m.ffmi_colour },
                        { label: 'Muscle Mass', value: m.muscle_mass_classification, colour: m.muscle_mass_colour },
                        { label: 'Visceral Fat', value: m.visceral_fat_classification, colour: m.visceral_fat_colour },
                        { label: 'Physique', value: m.physique_rating_label, colour: null },
                    ];
                },
                get metricRows() {
                    const m = this.view.m || {};
                    const n = v => (v === null || v === undefined) ? null : (Math.round(Number(v) * 100) / 100);
                    return [
                        { label: 'BMI', value: n(m.bmi), unit: '' },
                        { label: 'FFMI', value: n(m.ffmi), unit: '' },
                        { label: 'Fat Mass', value: n(m.fat_mass_kg), unit: 'kg' },
                        { label: 'Fat-Free Mass', value: n(m.fat_free_mass_kg), unit: 'kg' },
                        { label: 'Muscle Mass', value: n(m.muscle_mass_kg), unit: 'kg' },
                        { label: 'Total Body Water', value: n(m.total_body_water_kg), unit: 'kg' },
                        { label: 'ECW/ICW Ratio', value: n(m.ecw_icw_ratio), unit: '' },
                        { label: 'ECW/TBW Ratio', value: n(m.ecw_tbw_ratio), unit: '' },
                        { label: 'BMR (Mifflin)', value: n(m.bmr_kcal), unit: 'kcal' },
                        { label: 'BMR (Katch)', value: n(m.bmr_katch_kcal), unit: 'kcal' },
                        { label: 'Metabolic Age', value: n(m.metabolic_age), unit: 'yrs' },
                        { label: 'Metabolic Age Δ', value: n(m.metabolic_age_difference), unit: 'yrs' },
                        { label: 'Phase Angle', value: n(m.phase_angle), unit: '°' },
                    ];
                },
                get resultCardCls() {
                    if (this.api.status === 0 && !this.result) return 'card-outline card-secondary';
                    if (this.api.ok) return 'card-outline card-success';
                    return 'card-outline card-danger';
                },
                get resultIcon() {
                    if (this.api.ok) return 'fa-check-circle text-success';
                    if (this.api.status && !this.api.ok) return 'fa-times-circle text-danger';
                    return 'fa-eye text-secondary';
                },
                get resultTitle() {
                    if (this.api.ok) return 'Record created';
                    if (this.api.status && !this.api.ok) return 'Submission failed';
                    return 'Live preview';
                },
                get resultSubtitle() {
                    if (this.api.ok) return this.api.raw?.message || 'The API accepted the submission.';
                    if (this.api.status && !this.api.ok) return this.api.raw?.message || 'See messages below.';
                    return 'Enter values and submit to the API to see computed metrics.';
                },

                /* ===================================================== */
                /* ESSENTIAL SHEET (API-backed)                          */
                /* Posts the clinical payload to the server proxy and    */
                /* renders the computed cardiovascular dashboard.        */
                /* ===================================================== */
                es: {},
                esApi: { ok: false, status: 0, errors: {}, raw: null },
                esSubmitting: false,
                esShowRaw: true,
                esResult: null, // last successful API response `data`

                esDefaults() {
                    return {
                        patient_reference: 'REF-001',
                        measurement_date: new Date().toISOString().slice(0, 10),
                        exercised_this_morning: true,
                        clinical_measures_taken_by: 'Nurse A',
                        height_cm: 175,
                        waist_circumference: 92,
                        hip_circumference: 104,
                        systolic_bp: 128,
                        diastolic_bp: 82,
                        heart_rate: 72,
                        cholesterol: 4.8,
                        hdl: 1.3,
                        ldl: 2.9,
                        triglycerides: 1.5,
                    };
                },

                resetEs() {
                    this.es = this.esDefaults();
                    this.clearEsResult();
                },
                clearEsResult() {
                    this.esResult = null;
                    this.esApi = { ok: false, status: 0, errors: {}, raw: null };
                },
                loadEsSample() {
                    this.es = Object.assign(this.esDefaults(), {
                        patient_reference: 'REF-204',
                        clinical_measures_taken_by: 'Dr. Patel',
                        height_cm: 182, waist_circumference: 98, hip_circumference: 103,
                        systolic_bp: 142, diastolic_bp: 91, heart_rate: 78,
                        cholesterol: 5.9, hdl: 1.1, ldl: 3.8, triglycerides: 2.1,
                    });
                    this.clearEsResult();
                },

                esErr(field) { return !!(this.esApi.errors && this.esApi.errors[field]); },
                esErrText(field) {
                    const e = this.esApi.errors && this.esApi.errors[field];
                    return Array.isArray(e) ? e.join(' ') : (e || '');
                },

                /* The numeric fields the API accepts; empty values are dropped. */
                buildEssentialPayload() {
                    const out = {};
                    const keys = ['patient_reference', 'measurement_date', 'clinical_measures_taken_by',
                        'height_cm', 'waist_circumference', 'hip_circumference',
                        'systolic_bp', 'diastolic_bp', 'heart_rate',
                        'cholesterol', 'hdl', 'ldl', 'triglycerides'];
                    keys.forEach(k => { if (this.es[k] !== '' && this.es[k] != null) out[k] = this.es[k]; });
                    out.exercised_this_morning = !!this.es.exercised_this_morning;
                    return out;
                },

                async submitEs() {
                    if (!this.api.configured || this.esSubmitting) return;
                    this.esSubmitting = true;
                    this.esApi.errors = {};

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const opts = {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.buildEssentialPayload()),
                    };

                    try {
                        const res = await fetch(this.api.essentialUrl, opts);
                        const json = await res.json().catch(() => ({}));
                        this.esApi.raw = json;
                        this.esApi.ok = !!json.ok;
                        this.esApi.status = json.status || res.status;
                        this.esApi.errors = json.errors || {};

                        if (json.ok && json.data) {
                            this.esResult = json.data;
                            this.toast('success', json.message || 'Essential sheet created successfully.');
                        } else {
                            this.esResult = null;
                            this.toast('error', json.message || 'Submission failed.');
                        }
                    } catch (ex) {
                        this.esApi.ok = false; this.esApi.status = 0;
                        this.toast('error', 'Network error contacting the server.');
                    } finally {
                        this.esSubmitting = false;
                    }
                },

                /* Convenience accessor for the live response `data` object. */
                get esd() { return this.esResult; },

                get esResultCardCls() {
                    if (this.esApi.status === 0 && !this.esResult) return 'card-outline card-secondary';
                    if (this.esApi.ok) return 'card-outline card-success';
                    return 'card-outline card-danger';
                },
                get esResultIcon() {
                    if (this.esApi.ok) return 'fa-check-circle text-success';
                    if (this.esApi.status && !this.esApi.ok) return 'fa-times-circle text-danger';
                    return 'fa-eye text-secondary';
                },
                get esResultTitle() {
                    if (this.esApi.ok) return 'Essential sheet created';
                    if (this.esApi.status && !this.esApi.ok) return 'Submission failed';
                    return 'Awaiting submission';
                },
                get esResultSubtitle() {
                    if (this.esApi.ok) return this.esApi.raw?.message || 'The API accepted the submission.';
                    if (this.esApi.status && !this.esApi.ok) return this.esApi.raw?.message || 'See messages below.';
                    return 'Submit the clinical measures to the API to see computed results.';
                },

                /* ---- Cardiovascular assessment cards (from API block) ---- */
                get esCardio() {
                    const d = this.esd;
                    if (!d) return [];
                    const ca = d.cardiovascular_assessment || {};
                    const defs = [
                        { key: 'systolic_bp', label: 'Systolic BP', unit: 'mmHg', icon: 'fa-arrow-up', fallback: d.systolic_bp },
                        { key: 'diastolic_bp', label: 'Diastolic BP', unit: 'mmHg', icon: 'fa-arrow-down', fallback: d.diastolic_bp },
                        { key: 'map', label: 'MAP', unit: 'mmHg', icon: 'fa-wave-square', fallback: d.mean_arterial_pressure },
                        { key: 'heart_rate', label: 'Heart Rate', unit: 'bpm', icon: 'fa-heartbeat', fallback: d.heart_rate },
                    ];
                    return defs
                        .filter(def => ca[def.key] || def.fallback != null)
                        .map(def => {
                            const a = ca[def.key] || {};
                            return {
                                key: def.key,
                                label: def.label,
                                unit: def.unit,
                                icon: def.icon,
                                value: a.value != null ? a.value : def.fallback,
                                status: a.status || '—',
                                color: a.color || '#6c757d',
                            };
                        });
                },

                /* ---- Cardiac risk callout styling (text-driven) ---- */
                get esCardiacRisk() {
                    const t = (this.esd && this.esd.cardiac_risk_assessment || '').toLowerCase();
                    if (/high/.test(t)) return { cls: 'callout-danger', icon: 'text-danger' };
                    if (/moderate|medium|border/.test(t)) return { cls: 'callout-warning', icon: 'text-warning' };
                    if (/low/.test(t)) return { cls: 'callout-success', icon: 'text-success' };
                    return { cls: 'callout-info', icon: 'text-info' };
                },

                /* ---- Blood pressure classification (ACC/AHA-style bands) ---- */
                get esBpClass() {
                    const s = Number(this.esd?.systolic_bp), d = Number(this.esd?.diastolic_bp);
                    // Classify by the worse of systolic/diastolic.
                    const rank = (label) => ({ 'Optimal': 0, 'Normal': 1, 'Elevated': 2, 'Stage 1': 3, 'Stage 2': 4, 'Crisis': 5 }[label]);
                    const sysLabel = s < 120 ? 'Optimal' : s < 130 ? 'Elevated' : s < 140 ? 'Stage 1' : s < 180 ? 'Stage 2' : 'Crisis';
                    const diaLabel = d < 80 ? 'Optimal' : d < 90 ? 'Stage 1' : d < 120 ? 'Stage 2' : 'Crisis';
                    const label = rank(sysLabel) >= rank(diaLabel) ? sysLabel : diaLabel;
                    const colours = { 'Optimal': '#28A745', 'Normal': '#28A745', 'Elevated': '#FFC107', 'Stage 1': '#FD7E14', 'Stage 2': '#DC3545', 'Crisis': '#8B0000' };
                    return { label, colour: colours[label] || '#6c757d' };
                },

                /* ---- Systolic BP slider model (shares the metric-slider UI) ---- */
                get esSysSlider() {
                    const value = Number(this.esd?.systolic_bp) || 0;
                    return this.buildSlider({
                        value,
                        unit: 'mmHg',
                        scaleMin: 90, scaleMax: 180,
                        colour: this.esBpClass.colour,
                        bands: [
                            { label: 'Optimal', colour: '#28A745', min: 90, max: 120 },
                            { label: 'Elevated', colour: '#FFC107', min: 120, max: 130 },
                            { label: 'Stage 1', colour: '#FD7E14', min: 130, max: 140 },
                            { label: 'Stage 2', colour: '#DC3545', min: 140, max: 180 },
                        ],
                    });
                },

                /* ---- Waist-to-hip ratio risk (gender-aware) ---- */
                get esWhr() {
                    const r = Number(this.esd?.waist_to_hip_ratio);
                    if (!isFinite(r) || r === 0) return { label: '—', colour: '#6c757d' };
                    const female = (this.esd?.patient?.gender || '').toLowerCase() === 'female';
                    const low = female ? 0.80 : 0.90;
                    const high = female ? 0.85 : 1.00;
                    if (r < low) return { label: 'Low risk', colour: '#28A745' };
                    if (r < high) return { label: 'Moderate risk', colour: '#FFC107' };
                    return { label: 'High risk', colour: '#DC3545' };
                },

                /* ---- Cholesterol / HDL ratio risk ---- */
                get esCholRatio() {
                    const r = Number(this.esd?.chol_hdl_ratio);
                    if (!isFinite(r) || r === 0) return { label: '—', colour: '#6c757d' };
                    if (r < 3.5) return { label: 'Optimal', colour: '#28A745' };
                    if (r < 5) return { label: 'Moderate', colour: '#FFC107' };
                    return { label: 'High', colour: '#DC3545' };
                },

                /* ---- LDL / HDL ratio risk ---- */
                get esLdlRatio() {
                    const r = Number(this.esd?.ldl_hdl_ratio);
                    if (!isFinite(r) || r === 0) return { label: '—', colour: '#6c757d' };
                    if (r < 2) return { label: 'Optimal', colour: '#28A745' };
                    if (r < 3.5) return { label: 'Moderate', colour: '#FFC107' };
                    return { label: 'High', colour: '#DC3545' };
                },

                get esLipidRows() {
                    const d = this.esd || {};
                    const n = v => (v === null || v === undefined) ? null : (Math.round(Number(v) * 100) / 100);
                    return [
                        { label: 'Total Cholesterol', value: n(d.cholesterol), unit: 'mmol/L' },
                        { label: 'HDL', value: n(d.hdl), unit: 'mmol/L' },
                        { label: 'LDL', value: n(d.ldl), unit: 'mmol/L' },
                        { label: 'Triglycerides', value: n(d.triglycerides), unit: 'mmol/L' },
                        { label: 'Chol / HDL Ratio', value: n(d.chol_hdl_ratio), unit: '' },
                        { label: 'LDL / HDL Ratio', value: n(d.ldl_hdl_ratio), unit: '' },
                    ];
                },
                get esMeasureRows() {
                    const d = this.esd || {};
                    const n = v => (v === null || v === undefined) ? null : (Math.round(Number(v) * 100) / 100);
                    return [
                        { label: 'Height', value: d.height_ft_in || (d.height_cm != null ? n(d.height_cm) + ' cm' : null), unit: '' },
                        { label: 'Waist', value: n(d.waist_circumference), unit: 'cm' },
                        { label: 'Hip', value: n(d.hip_circumference), unit: 'cm' },
                        { label: 'Waist-to-Hip Ratio', value: n(d.waist_to_hip_ratio), unit: '' },
                        { label: 'Heart Rate', value: n(d.heart_rate), unit: 'bpm' },
                        { label: 'Mean Arterial Pressure', value: n(d.mean_arterial_pressure), unit: 'mmHg' },
                        { label: 'HbA1c', value: d.hba1c_percent != null ? n(d.hba1c_percent) + ' %' : null, unit: '' },
                        { label: 'HbA1c (IFCC)', value: d.hba1c_mmol != null ? n(d.hba1c_mmol) + ' mmol/mol' : null, unit: '' },
                        { label: 'Measured by', value: d.clinical_measures_taken_by || null, unit: '' },
                    ];
                },

                /* ===================================================== */
                /* ESSENTIAL PROGRESS (client-side demo)                 */
                /* ===================================================== */
                modes: {
                    body: { title: 'Body Composition', subtitle: 'External API integration — manual entry or Tanita file upload.', badge: 'API' },
                    essential: { title: 'Essential', subtitle: 'External API integration — clinical measures, blood pressure & lipid panel.', badge: 'API' },
                    progress: { title: 'Essential Progress', subtitle: 'External API integration — follow-up visit with lifestyle notes & clinical measures.', badge: 'API' },
                },

                charts: {},

                init() {
                    this.bc = this.bcDefaults();
                    this.es = this.esDefaults();
                    this.pg = this.pgDefaults();
                    // Open the tab requested via ?mode= (deep link from the sidebar).
                    const requested = new URLSearchParams(window.location.search).get('mode');
                    if (this.modes[requested]) this.mode = requested;
                    this.$nextTick(() => this.renderCharts());
                    this.$watch('bc', () => { if (this.mode === 'body') this.updateCharts(); }, { deep: true });
                    this.$watch('mode', () => this.$nextTick(() => this.renderCharts()));
                },

                setMode(m) {
                    if (!this.modes[m]) return;
                    this.mode = m;
                    // Keep the URL in sync so the tab is bookmarkable / survives refresh.
                    const url = new URL(window.location.href);
                    if (m === 'body') url.searchParams.delete('mode');
                    else url.searchParams.set('mode', m);
                    window.history.replaceState({}, '', url);
                },
                num(v) { v = Number(v); return isFinite(v) ? Math.round(v * 10) / 10 : 0; },
                pct(v) { v = Number(v); return (isFinite(v) ? Math.round(v * 1000) / 10 : 0) + '%'; },

                /* ===================================================== */
                /* ESSENTIAL PROGRESS SHEET (API-backed)                 */
                /* Posts the follow-up visit payload to the server proxy */
                /* and renders the computed cardiovascular dashboard plus */
                /* the patient's lifestyle/progress notes.               */
                /* ===================================================== */
                pg: {},
                pgApi: { ok: false, status: 0, errors: {}, raw: null },
                pgSubmitting: false,
                pgShowRaw: true,
                pgResult: null, // last successful API response `data`

                pgDefaults() {
                    return {
                        patient_reference: 'REF-001',
                        measurement_date: new Date().toISOString().slice(0, 10),
                        exercised_this_morning: true,
                        clinical_measures_taken_by: 'Jane Smith',
                        dietary_changes: 'Reduced refined sugar.',
                        exercise_changes: 'Two 30-minute brisk walks per week.',
                        waist_circumference: 88.5,
                        hip_circumference: 104.0,
                        systolic_bp: 128,
                        diastolic_bp: 82,
                        heart_rate: 72,
                        cholesterol: 5.0,
                        hdl: 1.25,
                        ldl: 3.0,
                        triglycerides: 1.8,
                    };
                },

                resetPg() {
                    this.pg = this.pgDefaults();
                    this.clearPgResult();
                },
                clearPgResult() {
                    this.pgResult = null;
                    this.pgApi = { ok: false, status: 0, errors: {}, raw: null };
                },
                loadPgSample() {
                    this.pg = Object.assign(this.pgDefaults(), {
                        patient_reference: 'REF-204',
                        clinical_measures_taken_by: 'Dr. Patel',
                        dietary_changes: 'Switched to wholegrain carbs; cut sugary drinks.',
                        exercise_changes: 'Added two gym sessions and daily 20-minute walks.',
                        waist_circumference: 94.0, hip_circumference: 106.0,
                        systolic_bp: 138, diastolic_bp: 88, heart_rate: 76,
                        cholesterol: 5.6, hdl: 1.15, ldl: 3.5, triglycerides: 2.0,
                    });
                    this.clearPgResult();
                },

                pgErr(field) { return !!(this.pgApi.errors && this.pgApi.errors[field]); },
                pgErrText(field) {
                    const e = this.pgApi.errors && this.pgApi.errors[field];
                    return Array.isArray(e) ? e.join(' ') : (e || '');
                },

                /* The fields the API accepts; empty values are dropped. */
                buildProgressPayload() {
                    const out = {};
                    const keys = ['patient_reference', 'measurement_date', 'clinical_measures_taken_by',
                        'dietary_changes', 'exercise_changes',
                        'waist_circumference', 'hip_circumference',
                        'systolic_bp', 'diastolic_bp', 'heart_rate',
                        'cholesterol', 'hdl', 'ldl', 'triglycerides'];
                    keys.forEach(k => { if (this.pg[k] !== '' && this.pg[k] != null) out[k] = this.pg[k]; });
                    out.exercised_this_morning = !!this.pg.exercised_this_morning;
                    return out;
                },

                async submitPg() {
                    if (!this.api.configured || this.pgSubmitting) return;
                    this.pgSubmitting = true;
                    this.pgApi.errors = {};

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const opts = {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.buildProgressPayload()),
                    };

                    try {
                        const res = await fetch(this.api.progressUrl, opts);
                        const json = await res.json().catch(() => ({}));
                        this.pgApi.raw = json;
                        this.pgApi.ok = !!json.ok;
                        this.pgApi.status = json.status || res.status;
                        this.pgApi.errors = json.errors || {};

                        if (json.ok && json.data) {
                            this.pgResult = json.data;
                            this.toast('success', json.message || 'Essential progress sheet created successfully.');
                        } else {
                            this.pgResult = null;
                            this.toast('error', json.message || 'Submission failed.');
                        }
                    } catch (ex) {
                        this.pgApi.ok = false; this.pgApi.status = 0;
                        this.toast('error', 'Network error contacting the server.');
                    } finally {
                        this.pgSubmitting = false;
                    }
                },

                /* Convenience accessor for the live response `data` object. */
                get pgd() { return this.pgResult; },

                get pgResultCardCls() {
                    if (this.pgApi.status === 0 && !this.pgResult) return 'card-outline card-secondary';
                    if (this.pgApi.ok) return 'card-outline card-success';
                    return 'card-outline card-danger';
                },
                get pgResultIcon() {
                    if (this.pgApi.ok) return 'fa-check-circle text-success';
                    if (this.pgApi.status && !this.pgApi.ok) return 'fa-times-circle text-danger';
                    return 'fa-eye text-secondary';
                },
                get pgResultTitle() {
                    if (this.pgApi.ok) return 'Progress sheet created';
                    if (this.pgApi.status && !this.pgApi.ok) return 'Submission failed';
                    return 'Awaiting submission';
                },
                get pgResultSubtitle() {
                    if (this.pgApi.ok) return this.pgApi.raw?.message || 'The API accepted the submission.';
                    if (this.pgApi.status && !this.pgApi.ok) return this.pgApi.raw?.message || 'See messages below.';
                    return 'Submit the follow-up visit to the API to see computed results.';
                },

                /* ---- Cardiovascular assessment cards (from API block) ---- */
                get pgCardio() {
                    const d = this.pgd;
                    if (!d) return [];
                    const ca = d.cardiovascular_assessment || {};
                    const defs = [
                        { key: 'systolic_bp', label: 'Systolic BP', unit: 'mmHg', icon: 'fa-arrow-up', fallback: d.systolic_bp },
                        { key: 'diastolic_bp', label: 'Diastolic BP', unit: 'mmHg', icon: 'fa-arrow-down', fallback: d.diastolic_bp },
                        { key: 'map', label: 'MAP', unit: 'mmHg', icon: 'fa-wave-square', fallback: d.mean_arterial_pressure },
                        { key: 'heart_rate', label: 'Heart Rate', unit: 'bpm', icon: 'fa-heartbeat', fallback: d.heart_rate },
                    ];
                    return defs
                        .filter(def => ca[def.key] || def.fallback != null)
                        .map(def => {
                            const a = ca[def.key] || {};
                            return {
                                key: def.key,
                                label: def.label,
                                unit: def.unit,
                                icon: def.icon,
                                value: a.value != null ? a.value : def.fallback,
                                status: a.status || '—',
                                color: a.color || '#6c757d',
                            };
                        });
                },

                /* ---- Cardiac risk callout styling (text-driven) ---- */
                get pgCardiacRisk() {
                    const t = (this.pgd && this.pgd.cardiac_risk_assessment || '').toLowerCase();
                    if (/high/.test(t)) return { cls: 'callout-danger', icon: 'text-danger' };
                    if (/moderate|medium|border/.test(t)) return { cls: 'callout-warning', icon: 'text-warning' };
                    if (/low/.test(t)) return { cls: 'callout-success', icon: 'text-success' };
                    return { cls: 'callout-info', icon: 'text-info' };
                },

                /* ---- Blood pressure classification (ACC/AHA-style bands) ---- */
                get pgBpClass() {
                    const s = Number(this.pgd?.systolic_bp), d = Number(this.pgd?.diastolic_bp);
                    const rank = (label) => ({ 'Optimal': 0, 'Normal': 1, 'Elevated': 2, 'Stage 1': 3, 'Stage 2': 4, 'Crisis': 5 }[label]);
                    const sysLabel = s < 120 ? 'Optimal' : s < 130 ? 'Elevated' : s < 140 ? 'Stage 1' : s < 180 ? 'Stage 2' : 'Crisis';
                    const diaLabel = d < 80 ? 'Optimal' : d < 90 ? 'Stage 1' : d < 120 ? 'Stage 2' : 'Crisis';
                    const label = rank(sysLabel) >= rank(diaLabel) ? sysLabel : diaLabel;
                    const colours = { 'Optimal': '#28A745', 'Normal': '#28A745', 'Elevated': '#FFC107', 'Stage 1': '#FD7E14', 'Stage 2': '#DC3545', 'Crisis': '#8B0000' };
                    return { label, colour: colours[label] || '#6c757d' };
                },

                /* ---- Systolic BP slider model (shares the metric-slider UI) ---- */
                get pgSysSlider() {
                    const value = Number(this.pgd?.systolic_bp) || 0;
                    return this.buildSlider({
                        value,
                        unit: 'mmHg',
                        scaleMin: 90, scaleMax: 180,
                        colour: this.pgBpClass.colour,
                        bands: [
                            { label: 'Optimal', colour: '#28A745', min: 90, max: 120 },
                            { label: 'Elevated', colour: '#FFC107', min: 120, max: 130 },
                            { label: 'Stage 1', colour: '#FD7E14', min: 130, max: 140 },
                            { label: 'Stage 2', colour: '#DC3545', min: 140, max: 180 },
                        ],
                    });
                },

                /* ---- Waist-to-hip ratio risk (gender-aware) ---- */
                get pgWhr() {
                    const r = Number(this.pgd?.waist_to_hip_ratio);
                    if (!isFinite(r) || r === 0) return { label: '—', colour: '#6c757d' };
                    const female = (this.pgd?.patient?.gender || '').toLowerCase() === 'female';
                    const low = female ? 0.80 : 0.90;
                    const high = female ? 0.85 : 1.00;
                    if (r < low) return { label: 'Low risk', colour: '#28A745' };
                    if (r < high) return { label: 'Moderate risk', colour: '#FFC107' };
                    return { label: 'High risk', colour: '#DC3545' };
                },

                /* ---- Cholesterol / HDL ratio risk ---- */
                get pgCholRatio() {
                    const r = Number(this.pgd?.chol_hdl_ratio);
                    if (!isFinite(r) || r === 0) return { label: '—', colour: '#6c757d' };
                    if (r < 3.5) return { label: 'Optimal', colour: '#28A745' };
                    if (r < 5) return { label: 'Moderate', colour: '#FFC107' };
                    return { label: 'High', colour: '#DC3545' };
                },

                /* ---- LDL / HDL ratio risk ---- */
                get pgLdlRatio() {
                    const r = Number(this.pgd?.ldl_hdl_ratio);
                    if (!isFinite(r) || r === 0) return { label: '—', colour: '#6c757d' };
                    if (r < 2) return { label: 'Optimal', colour: '#28A745' };
                    if (r < 3.5) return { label: 'Moderate', colour: '#FFC107' };
                    return { label: 'High', colour: '#DC3545' };
                },

                /* ---- Patient progress / lifestyle notes (free text) ---- */
                get pgNotes() {
                    const d = this.pgd || {};
                    const defs = [
                        { key: 'dietary_changes', label: 'Dietary changes', icon: 'fa-utensils' },
                        { key: 'exercise_changes', label: 'Exercise changes', icon: 'fa-running' },
                        { key: 'lifestyle_changes', label: 'Lifestyle changes', icon: 'fa-bed' },
                        { key: 'improvements_achieved', label: 'Improvements achieved', icon: 'fa-check-circle' },
                        { key: 'hoping_results_show', label: 'Hoping results show', icon: 'fa-bullseye' },
                    ];
                    return defs
                        .filter(def => d[def.key])
                        .map(def => ({ label: def.label, icon: def.icon, value: d[def.key] }));
                },

                get pgLipidRows() {
                    const d = this.pgd || {};
                    const n = v => (v === null || v === undefined) ? null : (Math.round(Number(v) * 100) / 100);
                    return [
                        { label: 'Total Cholesterol', value: n(d.cholesterol), unit: 'mmol/L' },
                        { label: 'HDL', value: n(d.hdl), unit: 'mmol/L' },
                        { label: 'LDL', value: n(d.ldl), unit: 'mmol/L' },
                        { label: 'Triglycerides', value: n(d.triglycerides), unit: 'mmol/L' },
                        { label: 'Chol / HDL Ratio', value: n(d.chol_hdl_ratio), unit: '' },
                        { label: 'LDL / HDL Ratio', value: n(d.ldl_hdl_ratio), unit: '' },
                    ];
                },
                get pgMeasureRows() {
                    const d = this.pgd || {};
                    const n = v => (v === null || v === undefined) ? null : (Math.round(Number(v) * 100) / 100);
                    return [
                        { label: 'Height', value: d.height_ft_in || (d.height_cm != null ? n(d.height_cm) + ' cm' : null), unit: '' },
                        { label: 'Waist', value: n(d.waist_circumference), unit: 'cm' },
                        { label: 'Hip', value: n(d.hip_circumference), unit: 'cm' },
                        { label: 'Waist-to-Hip Ratio', value: n(d.waist_to_hip_ratio), unit: '' },
                        { label: 'Heart Rate', value: n(d.heart_rate), unit: 'bpm' },
                        { label: 'Mean Arterial Pressure', value: n(d.mean_arterial_pressure), unit: 'mmHg' },
                        { label: 'HbA1c', value: d.hba1c_percent != null ? n(d.hba1c_percent) + ' %' : null, unit: '' },
                        { label: 'HbA1c (IFCC)', value: d.hba1c_mmol != null ? n(d.hba1c_mmol) + ' mmol/mol' : null, unit: '' },
                        { label: 'Measured by', value: d.clinical_measures_taken_by || null, unit: '' },
                    ];
                },

                /* ===================================================== */
                /* CHARTS                                                */
                /* ===================================================== */
                destroyCharts() {
                    Object.values(this.charts).forEach(c => c && c.destroy());
                    this.charts = {};
                },
                renderCharts() {
                    if (typeof Chart === 'undefined') return;
                    this.destroyCharts();
                    if (this.mode === 'body') { this.renderDoughnut(); if (this.view.hasSegmental) this.renderSegmental(); }
                    // Essential and Essential Progress modes are card/slider-based — no Chart.js canvases.
                },
                ctx(id) { const el = document.getElementById(id); return el ? el.getContext('2d') : null; },

                renderDoughnut() {
                    const ctx = this.ctx('bodyDoughnut'); if (!ctx) return;
                    this.charts.doughnut = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Fat Mass', 'Body Water', 'Dry Lean Mass'],
                            datasets: [{
                                data: [this.num(this.view.fatMass), this.num(this.view.waterMass), this.num(this.view.dryLeanMass)],
                                backgroundColor: ['#ffc107', '#17a2b8', '#28a745'], borderWidth: 2,
                            }],
                        },
                        options: { maintainAspectRatio: false, legend: { display: false }, cutoutPercentage: 62 },
                    });
                },
                renderSegmental() {
                    const ctx = this.ctx('segmentalChart'); if (!ctx) return;
                    this.charts.segmental = new Chart(ctx, {
                        type: 'horizontalBar',
                        data: {
                            labels: this.bcSegments.map(s => s.label),
                            datasets: [
                                { label: 'Muscle (kg)', data: this.bcSegments.map(s => this.num(this.segValue(s.key, 'muscle_mass_kg'))), backgroundColor: '#007bff' },
                                { label: 'Fat (kg)', data: this.bcSegments.map(s => this.num(this.segValue(s.key, 'fat_mass_kg'))), backgroundColor: '#ffc107' },
                            ],
                        },
                        options: { maintainAspectRatio: false, scales: { xAxes: [{ stacked: false, ticks: { beginAtZero: true } }] } },
                    });
                },
                updateCharts() { this.$nextTick(() => this.renderCharts()); },
            };
        }
    </script>

    {{-- Alpine 3 (defer so the factory above is registered before init) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
