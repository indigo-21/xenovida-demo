<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ Auth::check() ? route('dashboard') : route('demo') }}" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        @auth
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user mr-1"></i>
                    {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                    <i class="fas fa-caret-down ml-1"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-id-card mr-2"></i> Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        @else
            <li class="nav-item">
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </a>
            </li>
        @endauth
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ Auth::check() ? route('dashboard') : route('demo') }}" class="brand-link">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                @auth
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endauth

                @php($demoMode = request()->routeIs('demo') ? request()->query('mode', 'body') : null)

                <li class="nav-item">
                    <a href="{{ route('demo') }}"
                        class="nav-link {{ $demoMode === 'body' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-heartbeat"></i>
                        <p>Body Composition Demo</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('demo', ['mode' => 'essential']) }}"
                        class="nav-link {{ $demoMode === 'essential' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-notes-medical"></i>
                        <p>Essential Demo</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('demo', ['mode' => 'progress']) }}"
                        class="nav-link {{ $demoMode === 'progress' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Essential Progress Demo</p>
                    </a>
                </li>

                @auth
                    <li class="nav-header">Administrator</li>

                    <li class="nav-item">
                        <a href="{{ route('user-level.index') }}"
                            class="nav-link {{ request()->routeIs('user-level.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>User Levels</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('user.index') }}"
                            class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>
                @endauth

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
