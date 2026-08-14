<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-lock mr-1"></i> Update Password
        </h3>
    </div>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        <div class="card-body">
            @if ($errors->updatePassword->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <ul class="mb-0">
                        @foreach ($errors->updatePassword->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label for="update_password_current_password">
                    Current Password <span class="text-danger">*</span>
                </label>
                <input type="password"
                       id="update_password_current_password"
                       name="current_password"
                       class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                       autocomplete="current-password">
                @if ($errors->updatePassword->has('current_password'))
                    <div class="invalid-feedback">
                        {{ $errors->updatePassword->first('current_password') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="update_password_password">
                    New Password <span class="text-danger">*</span>
                </label>
                <input type="password"
                       id="update_password_password"
                       name="password"
                       class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                       autocomplete="new-password">
                @if ($errors->updatePassword->has('password'))
                    <div class="invalid-feedback">
                        {{ $errors->updatePassword->first('password') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="update_password_password_confirmation">
                    Confirm Password <span class="text-danger">*</span>
                </label>
                <input type="password"
                       id="update_password_password_confirmation"
                       name="password_confirmation"
                       class="form-control"
                       autocomplete="new-password">
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key mr-1"></i> Update Password
            </button>
        </div>
    </form>
</div>
