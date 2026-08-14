<x-guest-layout>
    <x-slot name="title">Forgot Password</x-slot>

    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="/" class="h1">
                    <b>{{ config('app.name') }}</b>
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">
                    Forgot your password? Enter your email address and we will send you a reset link.
                </p>

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="Email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                Send Password Reset Link
                            </button>
                        </div>
                    </div>
                </form>

                <p class="mt-3 mb-0 text-center">
                    <a href="{{ route('login') }}"><i class="fas fa-arrow-left mr-1"></i> Back to login</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
