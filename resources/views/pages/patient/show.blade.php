<x-app-layout>
    <x-slot name="header">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Patients</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('patient.index') }}">Patients</a>
                            </li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user mr-1"></i> Patient Details
            </h3>
            <div class="card-tools">
                <a href="{{ route('patient.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h5 class="text-muted mb-3">
                        <i class="fas fa-id-card mr-1"></i> Patient Information
                    </h5>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-0">First Name</label>
                    <p class="mb-0">{{ $patient->firstname }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-0">Last Name</label>
                    <p class="mb-0">{{ $patient->lastname }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-0">Email Address</label>
                    <p class="mb-0">{{ $patient->email }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-0">Date of Birth</label>
                    <p class="mb-0">{{ optional($patient->dob)->format('Y-m-d') ?? '—' }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted mb-0">Gender</label>
                    <p class="mb-0">{{ $patient->gender ?? '—' }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted mb-0">Postcode</label>
                    <p class="mb-0">{{ $patient->postcode ?? '—' }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted mb-0">Mobile Number</label>
                    <p class="mb-0">{{ $patient->mobile ?? '—' }}</p>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-center align-items-center">
            <div>
                <a href="{{ route('patient.edit', $patient->id) }}" class="btn btn-info">
                    <i class="fas fa-edit mr-1"></i> Edit Patient
                </a>
                <a href="{{ route('patient.index') }}" class="btn btn-secondary ml-2">
                    Back to List
                </a>
                <a href="{{ route('patient.destroy', $patient->id) }}"
                    landto="{{ route('patient.index') }}"
                    class="btn btn-danger delete-data-btn">
                    <i class="fas fa-trash mr-1"></i> Delete Patient
                </a>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- Assessments. Reuses the interactive demo Alpine component               --}}
    {{-- (bodyCompositionDemo) so the three API-backed forms render and switch   --}}
    {{-- inline, exactly as they do on the /demo page. The component's own       --}}
    {{-- `mode` state drives both the active tab and which form is shown, so no  --}}
    {{-- new JavaScript is needed.                                               --}}
    {{-- ===================================================================== --}}
    <div class="row">
        <div class="col-12">
            <h5 class="text-muted mb-3">
                <i class="fas fa-clipboard-list mr-1"></i> Assessments
            </h5>
        </div>
    </div>

    <div class="card card-primary card-outline card-outline-tabs"
         x-data="bodyCompositionDemo(@js([
            'apiConfigured' => $apiConfigured ?? false,
            'storeUrl' => route('demo.body-composition.store'),
            'essentialStoreUrl' => route('demo.essential-sheet.store'),
            'progressStoreUrl' => route('demo.progress-sheet.store'),
         ]))"
         x-init="init()" x-cloak>
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" href="#"
                       :class="mode === 'body' ? 'active' : ''"
                       @click.prevent="setMode('body')">
                        <i class="fas fa-weight mr-1"></i> Body Composition
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"
                       :class="mode === 'essential' ? 'active' : ''"
                       @click.prevent="setMode('essential')">
                        <i class="fas fa-vial mr-1"></i> Essential
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"
                       :class="mode === 'progress' ? 'active' : ''"
                       @click.prevent="setMode('progress')">
                        <i class="fas fa-chart-line mr-1"></i> Essential Progress
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                {{-- Body Composition tab --}}
                <div x-show="mode === 'body'" x-transition.opacity>
                    <div class="row">
                        <div class="col-4">
                            @include('pages.demo.form-body')
                        </div>
                        <div class="col-8">
                            @include('pages.demo.result-body')
                        </div>
                    </div>
                </div>

                {{-- Essential tab --}}
                <div x-show="mode === 'essential'" x-transition.opacity>
                    <div class="row">
                        <div class="col-4">
                            @include('pages.demo.form-essential')
                        </div>
                        <div class="col-8">
                            @include('pages.demo.result-essential')
                        </div>
                    </div>
                </div>

                {{-- Essential Progress tab --}}
                <div x-show="mode === 'progress'" x-transition.opacity>
                    <div class="row">
                        <div class="col-4">
                            @include('pages.demo.form-progress')
                        </div>
                        <div class="col-8">
                            @include('pages.demo.result-progress')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Registers the bodyCompositionDemo() Alpine component + supporting styles. --}}
    @include('pages.demo.script')
</x-app-layout>
