<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="">

    <title>{{ $title }} </title>

	<!-- Vendors Style-->
	<link rel="stylesheet" href="{{ asset('assets/src/css/vendors_css.css') }}">

	<!-- Style-->
	<link rel="stylesheet" href="{{ asset('assets/src/css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/src/css/skin_color.css') }}">
    <!-- Toast notification -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="https://kit.fontawesome.com/111032cd6f.js" crossorigin="anonymous"></script>
</head>

<body class="hold-transition theme-primary bg-img" style="background-image: url(home/assets/img/admin_bg.png)">

	<div class="container h-p100">
		<div class="row align-items-center justify-content-md-center h-p100">
			@yield('content')
		</div>
	</div>


	<!-- Vendor JS -->
	<script src="{{ asset('assets/src/js/vendors.min.js') }}"></script>
	<script src="{{ asset('assets/src/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
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

    @if ($errors->any())
        <script>
            Swal.fire({
                text: "{{ $errors->first() }}",
                icon: "error",
                button: "ok",
            });
        </script>
    @endif

    @stack('js')
</body>
</html>
