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
                            <li class="breadcrumb-item active">User Levels</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group mr-1"></i> List of User Levels
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('user-level.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Add User Level
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="userLevelTbl" class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Description</th>
                                <th style="width: 10%;" class="text-center">Users</th>
                                <th style="width: 12%;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userLevels as $userLevel)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $userLevel->description }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $userLevel->users_count > 0 ? 'primary' : 'secondary' }}">
                                            {{ $userLevel->users_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('user-level.edit', $userLevel->id) }}"
                                           class="btn btn-sm btn-info"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('user-level.destroy', $userLevel->id) }}"
                                           landto="{{ route('user-level.index') }}"
                                           class="btn btn-sm btn-danger delete-data-btn"
                                           title="Delete"
                                           data-users="{{ $userLevel->users_count }}">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            $(function () {
                $('#userLevelTbl').DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                }).buttons().container().appendTo('#userLevelTbl_wrapper .col-md-6:eq(0)');

                // Override delete for user levels — warn if users are assigned
                $(document).on('click', '.delete-data-btn', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation(); // prevent global handler running twice

                    const destroyUrl  = $(this).attr('href');
                    const landTo      = $(this).attr('landto') || window.location.href;
                    const usersCount  = parseInt($(this).data('users') || 0, 10);

                    const warningText = usersCount > 0
                        ? `This level has ${usersCount} assigned user(s). You must reassign them before deleting.`
                        : 'This action cannot be undone.';

                    if (usersCount > 0) {
                        Swal.fire({
                            title: 'Cannot Delete',
                            text:  warningText,
                            icon:  'error',
                            confirmButtonColor: '#3085d6',
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text:  warningText,
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
            });
        </script>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
    </x-slot>
</x-app-layout>
