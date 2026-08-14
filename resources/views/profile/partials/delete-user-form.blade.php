<div class="card card-danger card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-times mr-1"></i> Delete Account
        </h3>
    </div>
    <div class="card-body">
        <p class="text-muted">
            Once your account is deleted, all of its data will be permanently removed.
            Please enter your password to confirm.
        </p>

        @if ($errors->userDeletion->has('password'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ $errors->userDeletion->first('password') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
            @csrf
            @method('DELETE')

            <div class="form-group">
                <label for="delete_password">
                    Confirm Password <span class="text-danger">*</span>
                </label>
                <input type="password"
                       id="delete_password"
                       name="password"
                       class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                       placeholder="Enter your password to confirm"
                       autocomplete="current-password">
                @if ($errors->userDeletion->has('password'))
                    <div class="invalid-feedback">
                        {{ $errors->userDeletion->first('password') }}
                    </div>
                @endif
            </div>
        </form>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-danger" id="deleteAccountBtn">
            <i class="fas fa-trash mr-1"></i> Delete Account
        </button>
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        $('#deleteAccountBtn').on('click', function () {
            Swal.fire({
                title: 'Delete your account?',
                text:  'This action is permanent and cannot be undone.',
                icon:  'warning',
                showCancelButton:  true,
                confirmButtonColor: '#d33',
                cancelButtonColor:  '#6c757d',
                confirmButtonText:  'Yes, delete my account',
                cancelButtonText:   'Cancel',
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#deleteAccountForm').submit();
                }
            });
        });
    });
</script>
@endpush
