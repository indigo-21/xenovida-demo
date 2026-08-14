@props(['type' => 'success', 'message' => 'Success messages'])

<script>
    $(function() {
        let Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        Toast.fire({
            icon: "{{ $type }}",
            title: "{{ $message }}"
        })
    });
</script>
