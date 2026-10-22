<x-app-layout>
    <x-slot name="header">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-heartbeat text-danger mr-2"></i>
                            <span x-text="modes[mode].title">Body Composition</span>
                        </h1>
                        <small class="text-muted" x-text="modes[mode].subtitle"></small>
                    </div>
                    <div class="col-sm-6">
                        {{-- Demo mode selector (upper-right) --}}
                        <div class="d-flex justify-content-sm-end mt-2 mt-sm-0">
                            <div class="btn-group btn-group-toggle" role="group" aria-label="Demo mode">
                                <button type="button" class="btn btn-sm"
                                    :class="mode === 'body' ? 'btn-primary' : 'btn-outline-primary'"
                                    @click="setMode('body')">
                                    <i class="fas fa-weight mr-1"></i> Body Composition
                                </button>
                                <button type="button" class="btn btn-sm"
                                    :class="mode === 'essential' ? 'btn-primary' : 'btn-outline-primary'"
                                    @click="setMode('essential')">
                                    <i class="fas fa-vial mr-1"></i> Essential
                                </button>
                                <button type="button" class="btn btn-sm"
                                    :class="mode === 'progress' ? 'btn-primary' : 'btn-outline-primary'"
                                    @click="setMode('progress')">
                                    <i class="fas fa-chart-line mr-1"></i> Essential Progress
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- ===================================================================== --}}
    {{-- Interactive demo root. Alpine provides real-time binding; the Body     --}}
    {{-- Composition mode submits to the external API via the server proxy.     --}}
    {{-- ===================================================================== --}}
    <div x-data="bodyCompositionDemo(@js([
            'apiConfigured' => $apiConfigured ?? false,
            'storeUrl' => route('demo.body-composition.store'),
            'essentialStoreUrl' => route('demo.essential-sheet.store'),
            'progressStoreUrl' => route('demo.progress-sheet.store'),
         ]))"
         x-init="init()" x-cloak>

        {{-- API not configured warning (all modes are API-backed) --}}
        <template x-if="!api.configured">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                The external API is not configured. Set <code>XV_API_URL</code> and
                <code>XV_API_KEY</code> in your <code>.env</code> file to enable live submissions.
                The local preview below still works.
            </div>
        </template>

        <div class="row">
            {{-- =============================================================== --}}
            {{-- LEFT COLUMN: DATA ENTRY FORM                                     --}}
            {{-- =============================================================== --}}
            <div class="col-lg-5 col-xl-4">
                {{-- Body Composition mode: API-backed dual-method form --}}
                <div x-show="mode === 'body'" x-transition.opacity>
                    @include('pages.demo.form-body')
                </div>

                {{-- Essential mode: API-backed clinical entry form --}}
                <div x-show="mode === 'essential'" x-transition.opacity>
                    @include('pages.demo.form-essential')
                </div>

                {{-- Essential Progress: client-side demo form --}}
                <div x-show="mode === 'progress'" x-transition.opacity>
                    @include('pages.demo.form-progress')
                </div>
            </div>

            {{-- =============================================================== --}}
            {{-- RIGHT COLUMN: RESULT / PREVIEW DASHBOARD                         --}}
            {{-- =============================================================== --}}
            <div class="col-lg-7 col-xl-8">
                @include('pages.demo.result-body')
                @include('pages.demo.result-essential')
                @include('pages.demo.result-progress')
            </div>
        </div>
    </div>

    @include('pages.demo.script')
</x-app-layout>
