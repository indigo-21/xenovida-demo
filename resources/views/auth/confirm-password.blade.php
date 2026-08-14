<x-guest-layout>
    <x-slot name="title">Confirm Password</x-slot>

    <div class="login-box">
        <div class="card card-outline card-warning">
            <div class="card-header text-center">
                <a href="/" class="h1">
                    <b>{{ config('app.name') }}</b>
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">
                    This is a secure area. Please confirm your password before continuing.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Password"
                               name="password"
                               required
                               autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning btn-block">Confirm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
