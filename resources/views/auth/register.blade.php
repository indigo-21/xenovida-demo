<x-guest-layout>
    <x-slot name="title">Register</x-slot>

    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="/" class="h1">
                    <b>{{ config('app.name') }}</b>
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Register a new account</p>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="text"
                               class="form-control @error('firstname') is-invalid @enderror"
                               placeholder="First Name"
                               name="firstname"
                               value="{{ old('firstname') }}"
                               required
                               autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-user"></span></div>
                        </div>
                        @error('firstname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input type="text"
                               class="form-control @error('lastname') is-invalid @enderror"
                               placeholder="Last Name"
                               name="lastname"
                               value="{{ old('lastname') }}"
                               required>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-user"></span></div>
                        </div>
                        @error('lastname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="Email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="username">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Password"
                               name="password"
                               required
                               autocomplete="new-password">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input type="password"
                               class="form-control"
                               placeholder="Confirm Password"
                               name="password_confirmation"
                               required
                               autocomplete="new-password">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                    </div>
                </form>

                <p class="mt-3 mb-1 text-center">
                    <a href="{{ route('login') }}">Already have an account?</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
