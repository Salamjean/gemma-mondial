<footer class="main-footer">
    &copy;
    <script>
        document.write(new Date().getFullYear())
    </script> <a href="https://www.kks-technologies.com/">KKS-TECHNOLOGIES.COM</a>. Tous droits réservés.
</footer>

<script src="{{ asset('js/sweetalert.min.js') }}"></script>

@if (session('success'))
    <script>
        Swal.fire({
            text: "{{ session('success') }}",
            icon: "success",
            button: "ok",
        });
    </script>
@endif
@if (session('error'))
    <script>
        Swal.fire({
            text: "{{ session('error') }}",
            icon: "error",
            button: "ok",
        });
    </script>
@endif
@if (isset($errors) && $errors->any())
    <script>
        Swal.fire({
            text: "{{ $errors->first() }}",
            icon: "error",
            button: "ok",
        });
    </script>
@endif
@if (session('info'))
    <script>
        Swal.fire({
            text: "{{ session('info') }}",
            icon: "info",
            button: "ok",
        });
    </script>
@endif
@if (session('warning'))
    <script>
        Swal.fire({
            text: "{{ session('warning') }}",
            icon: "warning",
            button: "ok",
        });
    </script>
@endif
