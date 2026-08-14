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
                            <li class="breadcrumb-item active">Users</li>
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
                        <i class="fas fa-users mr-1"></i> List of Users
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Add User
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="userTbl" class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Access Level</th>
                                <th>Status</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->userLevel->description ?? '—' }}</td>
                                    <td>
                                        @if ($user->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('user.edit', $user->id) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('user.destroy', $user->id) }}"
                                           landto="{{ route('user.index') }}"
                                           class="btn btn-sm btn-danger delete-data-btn">
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
                $('#userTbl').DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                }).buttons().container().appendTo('#userTbl_wrapper .col-md-6:eq(0)');
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
