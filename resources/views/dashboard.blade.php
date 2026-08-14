<x-app-layout>
    <x-slot name="header">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1">
                    <i class="fas fa-layer-group"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">User Levels</span>
                    <span class="info-box-number">
                        {{ \App\Models\UserLevel::count() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1">
                    <i class="fas fa-users"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Users</span>
                    <span class="info-box-number">
                        {{ \App\Models\User::count() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1">
                    <i class="fas fa-user-check"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Users</span>
                    <span class="info-box-number">
                        {{ \App\Models\User::where('is_active', true)->count() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1">
                    <i class="fas fa-user-times"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Inactive Users</span>
                    <span class="info-box-number">
                        {{ \App\Models\User::where('is_active', false)->count() }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <div class="row">
        <!-- Recent Users -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-1"></i> Recent Users
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('user.index') }}" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Level</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse (\App\Models\User::with('userLevel')->latest()->limit(5)->get() as $user)
                                <tr>
                                    <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->userLevel->description ?? '—' }}</td>
                                    <td>
                                        @if ($user->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Levels -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group mr-1"></i> User Levels
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('user-level.index') }}" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse (\App\Models\UserLevel::withCount('users')->get() as $level)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $level->description }}
                                <span class="badge badge-primary badge-pill">
                                    {{ $level->users_count }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center">No levels found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
