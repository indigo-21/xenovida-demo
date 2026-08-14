<x-guest-layout>
    <x-slot name="title">Verify Email</x-slot>

    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="/" class="h1">
                    <b>{{ config('app.name') }}</b>
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">
                    Thanks for signing up! Before getting started, please verify your email address by clicking
                    the link we sent you. If you didn't receive the email, we'll send another.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                Resend Verification Email
                            </button>
                        </div>
                    </div>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-sign-out-alt mr-1"></i> Log Out
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
