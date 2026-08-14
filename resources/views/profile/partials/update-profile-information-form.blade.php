<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-id-card mr-1"></i> Profile Information
        </h3>
    </div>
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label for="firstname">
                    First Name <span class="text-danger">*</span>
                </label>
                <input type="text"
                       id="firstname"
                       name="firstname"
                       class="form-control @error('firstname') is-invalid @enderror"
                       value="{{ old('firstname', $user->firstname) }}"
                       required
                       autofocus>
                @error('firstname')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="lastname">
                    Last Name <span class="text-danger">*</span>
                </label>
                <input type="text"
                       id="lastname"
                       name="lastname"
                       class="form-control @error('lastname') is-invalid @enderror"
                       value="{{ old('lastname', $user->lastname) }}"
                       required>
                @error('lastname')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">
                    Email Address <span class="text-danger">*</span>
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}"
                       required
                       autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-warning mb-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Your email address is unverified.
                        </p>
                        <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                Resend verification email
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Save Changes
            </button>
        </div>
    </form>
</div>
