<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            title: '{{ $title }}',
            text: '{{ $text }}',
            icon: '{{ $icon }}',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
        });
    });
</script>
