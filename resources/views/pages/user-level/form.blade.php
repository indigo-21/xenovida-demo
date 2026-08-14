<x-app-layout>
    <x-slot name="header">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">User Levels</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('user-level.index') }}">User Levels</a>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ isset($userLevel) ? 'Edit' : 'Create' }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-{{ isset($userLevel) ? 'edit' : 'plus' }} mr-1"></i>
                        {{ isset($userLevel) ? 'Edit' : 'Create' }} User Level
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('user-level.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>

                <form action="{{ isset($userLevel) ? route('user-level.update', $userLevel->id) : route('user-level.store') }}"
                      method="POST"
                      id="userLevelForm">
                    @csrf
                    @if (isset($userLevel))
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

                        <div class="form-group">
                            <label for="description">
                                Description <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="description"
                                   name="description"
                                   class="form-control @error('description') is-invalid @enderror"
                                   placeholder="e.g. Super Administrator"
                                   value="{{ old('description', isset($userLevel) ? $userLevel->description : '') }}"
                                   required
                                   autofocus>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Show assigned users count when editing --}}
                        @isset($userLevel)
                            @php $usersCount = $userLevel->users()->count(); @endphp
                            <div class="alert alert-{{ $usersCount > 0 ? 'info' : 'secondary' }}">
                                <i class="fas fa-users mr-1"></i>
                                <strong>{{ $usersCount }}</strong>
                                {{ Str::plural('user', $usersCount) }} assigned to this level.
                                @if ($usersCount > 0)
                                    <a href="{{ route('user.index') }}" class="ml-2">View users</a>
                                @endif
                            </div>
                        @endisset

                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                {{ isset($userLevel) ? 'Update User Level' : 'Create User Level' }}
                            </button>
                            <a href="{{ route('user-level.index') }}" class="btn btn-secondary ml-2">
                                Cancel
                            </a>
                        </div>

                        @isset($userLevel)
                            <a href="{{ route('user-level.destroy', $userLevel->id) }}"
                               landto="{{ route('user-level.index') }}"
                               class="btn btn-danger delete-data-btn"
                               data-users="{{ $userLevel->users()->count() }}">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </a>
                        @endisset
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            // Override delete for user levels — block if users assigned
            $(document).on('click', '.delete-data-btn', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const destroyUrl = $(this).attr('href');
                const landTo     = $(this).attr('landto') || window.location.href;
                const usersCount = parseInt($(this).data('users') || 0, 10);

                if (usersCount > 0) {
                    Swal.fire({
                        title: 'Cannot Delete',
                        text:  `This level has ${usersCount} assigned user(s). Reassign them before deleting.`,
                        icon:  'error',
                        confirmButtonColor: '#3085d6',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text:  'This action cannot be undone.',
                    icon:  'warning',
                    showCancelButton:  true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor:  '#6c757d',
                    confirmButtonText:  'Yes, delete it!',
                    cancelButtonText:   'Cancel',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        const form = $('<form>', { method: 'POST', action: destroyUrl });
                        form.append($('<input>', { type: 'hidden', name: '_token',  value: $('meta[name="csrf-token"]').attr('content') }));
                        form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        </script>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
    </x-slot>
</x-app-layout>
