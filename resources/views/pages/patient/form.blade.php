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
                            <li class="breadcrumb-item active">
                                {{ isset($patient) ? 'Edit' : 'Create' }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-{{ isset($patient) ? 'edit' : 'plus' }} mr-1"></i>
                {{ isset($patient) ? 'Edit' : 'Create' }} Patient
            </h3>
            <div class="card-tools">
                <a href="{{ route('patient.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form action="{{ isset($patient) ? route('patient.update', $patient->id) : route('patient.store') }}"
              method="POST"
              id="patientForm">
            @csrf
            @if (isset($patient))
                @method('PUT')
            @endif

            <div class="card-body">

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i> Validation Error</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Patient Information -->
                <div class="row">
                    <div class="col-12">
                        <h5 class="text-muted mb-3">
                            <i class="fas fa-id-card mr-1"></i> Patient Information
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <x-input type="text"
                                 name="firstname"
                                 label="First Name"
                                 placeholder="Enter first name"
                                 :isRequired="true"
                                 value="{{ isset($patient) ? $patient->firstname : old('firstname') }}" />
                    </div>

                    <div class="col-md-6">
                        <x-input type="text"
                                 name="lastname"
                                 label="Last Name"
                                 placeholder="Enter last name"
                                 :isRequired="true"
                                 value="{{ isset($patient) ? $patient->lastname : old('lastname') }}" />
                    </div>

                    <div class="col-md-6">
                        <x-input type="email"
                                 name="email"
                                 label="Email Address"
                                 placeholder="Enter email"
                                 :isRequired="true"
                                 value="{{ isset($patient) ? $patient->email : old('email') }}" />
                    </div>

                    <div class="col-md-6">
                        <x-input type="date"
                                 name="dob"
                                 label="Date of Birth"
                                 :isRequired="true"
                                 value="{{ isset($patient) ? optional($patient->dob)->format('Y-m-d') : old('dob') }}" />
                    </div>

                    <div class="col-md-4">
                        @php $oldGender = isset($patient) ? $patient->gender : old('gender'); @endphp
                        <x-select name="gender" label="Gender" :isRequired="true">
                            <option value="">— Select Gender —</option>
                            <option value="Male" {{ $oldGender === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $oldGender === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ $oldGender === 'Other' ? 'selected' : '' }}>Other</option>
                        </x-select>
                    </div>

                    <div class="col-md-4">
                        <x-input type="text"
                                 name="postcode"
                                 label="Postcode"
                                 placeholder="Enter postcode"
                                 :isRequired="true"
                                 value="{{ isset($patient) ? $patient->postcode : old('postcode') }}" />
                    </div>

                    <div class="col-md-4">
                        <x-input type="text"
                                 name="mobile"
                                 label="Mobile Number"
                                 placeholder="Enter mobile number"
                                 :isRequired="true"
                                 value="{{ isset($patient) ? $patient->mobile : old('mobile') }}" />
                    </div>
                </div>

            </div>
            <!-- /.card-body -->

            <div class="card-footer d-flex justify-content-center align-items-center">
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        {{ isset($patient) ? 'Update Patient' : 'Create Patient' }}
                    </button>
                    <a href="{{ route('patient.index') }}" class="btn btn-secondary ml-2">
                        Cancel
                    </a>
                    @isset($patient)
                        <a href="{{ route('patient.destroy', $patient->id) }}"
                        landto="{{ route('patient.index') }}"
                        class="btn btn-danger delete-data-btn">
                            <i class="fas fa-trash mr-1"></i> Delete Patient
                        </a>
                    @endisset
                </div>
            </div>
        </form>
    </div>

    <x-slot name="script">
        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
    </x-slot>
</x-app-layout>
