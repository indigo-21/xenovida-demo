<x-app-layout>
    <x-slot name="header">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Profile</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Update Password -->
        <div class="col-md-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="row">
        <!-- Delete Account -->
        <div class="col-md-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

    <x-slot name="script">
        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if (session('status') === 'password-updated')
            <x-alert type="success" message="Password updated successfully." />
        @endif
    </x-slot>
</x-app-layout>
