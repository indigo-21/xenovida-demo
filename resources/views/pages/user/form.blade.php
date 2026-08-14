<x-app-layout>
    <x-slot name="header">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Users</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.index') }}">Users</a>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ isset($user) ? 'Edit' : 'Create' }}
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
                <i class="fas fa-{{ isset($user) ? 'edit' : 'plus' }} mr-1"></i>
                {{ isset($user) ? 'Edit' : 'Create' }} User
            </h3>
            <div class="card-tools">
                <a href="{{ route('user.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form action="{{ isset($user) ? route('user.update', $user->id) : route('user.store') }}"
              method="POST"
              id="userForm">
            @csrf
            @if (isset($user))
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

                <!-- User Information -->
                <div class="row">
                    <div class="col-12">
                        <h5 class="text-muted mb-3">
                            <i class="fas fa-id-card mr-1"></i> User Information
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <x-input type="text"
                                 name="firstname"
                                 label="First Name"
                                 placeholder="Enter first name"
                                 :isRequired="true"
                                 value="{{ isset($user) ? $user->firstname : old('firstname') }}" />
                    </div>

                    <div class="col-md-6">
                        <x-input type="text"
                                 name="lastname"
                                 label="Last Name"
                                 placeholder="Enter last name"
                                 :isRequired="true"
                                 value="{{ isset($user) ? $user->lastname : old('lastname') }}" />
                    </div>

                    <div class="col-md-4">
                        <x-input type="email"
                                 name="email"
                                 label="Email Address"
                                 placeholder="Enter email"
                                 :isRequired="true"
                                 value="{{ isset($user) ? $user->email : old('email') }}" />
                    </div>

                    <div class="col-md-4">
                        @php $oldAccessLevel = isset($user) ? $user->user_level_id : old('access_level'); @endphp
                        <x-select name="access_level" label="Access Level" :isRequired="true">
                            <option value="">— Select Access Level —</option>
                            @foreach ($userLevels as $userLevel)
                                <option value="{{ $userLevel->id }}"
                                    {{ $oldAccessLevel == $userLevel->id ? 'selected' : '' }}>
                                    {{ $userLevel->description }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <div class="col-md-4">
                        @php $oldStatus = isset($user) ? $user->is_active : old('status', '1'); @endphp
                        <x-select name="status" label="Status" :isRequired="true">
                            <option value="">— Select Status —</option>
                            <option value="1" {{ $oldStatus == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $oldStatus == '0' ? 'selected' : '' }}>Inactive</option>
                        </x-select>
                    </div>
                </div>

                <!-- Credentials -->
                <div class="row mt-3">
                    <div class="col-12">
                        <h5 class="text-muted mb-3">
                            <i class="fas fa-key mr-1"></i> Credentials
                            @if (isset($user))
                                <small class="text-muted font-weight-normal">(leave password blank to keep unchanged)</small>
                            @endif
                        </h5>
                    </div>

                    <div class="col-md-4">
                        <x-input type="text"
                                 name="username"
                                 label="Username"
                                 placeholder="Enter username"
                                 :isRequired="true"
                                 value="{{ isset($user) ? $user->username : old('username') }}" />
                    </div>

                    <div class="col-md-4">
                        <x-input type="password"
                                 name="password"
                                 label="Password"
                                 placeholder="{{ isset($user) ? 'New password (optional)' : 'Enter password' }}"
                                 :isRequired="!isset($user)" />
                    </div>

                    <div class="col-md-4">
                        <x-input type="password"
                                 name="password_confirmation"
                                 label="Confirm Password"
                                 placeholder="Re-enter password"
                                 :isRequired="!isset($user)" />
                    </div>
                </div>

            </div>
            <!-- /.card-body -->

            <div class="card-footer d-flex justify-content-between align-items-center">
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        {{ isset($user) ? 'Update User' : 'Create User' }}
                    </button>
                    <a href="{{ route('user.index') }}" class="btn btn-secondary ml-2">
                        Cancel
                    </a>
                </div>

                @isset($user)
                    <a href="{{ route('user.destroy', $user->id) }}"
                       landto="{{ route('user.index') }}"
                       class="btn btn-danger delete-data-btn">
                        <i class="fas fa-trash mr-1"></i> Delete User
                    </a>
                @endisset
            </div>
        </form>
    </div>

    <x-slot name="script">
        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
    </x-slot>
</x-app-layout>
