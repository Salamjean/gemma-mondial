<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Accueil | GEMMA</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset(iconsLoad()['favicon']) }}">

	<!-- CSS here -->
	<link rel="stylesheet" href="{{asset('home/assets/css/bootstrap.min.css')}}">
	<link rel="stylesheet" href="{{asset('home/assets/css/owl.carousel.min.css')}}">
	<link rel="stylesheet" href="{{asset('home/assets/css/slicknav.css')}}">
    <link rel="stylesheet" href="{{asset('home/assets/css/flaticon.css')}}">
    <link rel="stylesheet" href="{{asset('home/assets/css/gijgo.css')}}">
    <link rel="stylesheet" href="{{asset('home/assets/css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('home/assets/css/animated-headline.css')}}">
	<link rel="stylesheet" href="{{asset('home/assets/css/magnific-popup.css')}}">
	<link rel="stylesheet" href="{{asset('home/assets/css/fontawesome-all.min.css')}}">
	<link rel="stylesheet" href="{{asset('home/assets/css/themify-icons.css')}}">
	<link rel="stylesheet" href="{{asset('home/assets/css/slick.css')}}">
	<link rel="stylesheet" href="{{asset('home/assets/css/nice-select.css')}}">
	<link rel="stylesheet" href="{{asset('home/assets/css/style.css')}}">
</head>
<body>
    <!-- ? Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="{{ asset(iconsLoad()['loading']) }}" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader Start -->
<header>
    <!--? Header Start -->
    <div class="header-area">
        <div class="main-header header-sticky">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <!-- Logo -->
                    <div class="col-xl-2 col-lg-2 col-md-1">
                        <div class="logo">
                            <a href="{{ url('/') }}"><img src="{{ asset(iconsLoad()['logo']) }}" alt=""></a>
                        </div>
                    </div>
                    <div class="col-xl-10 col-lg-10 col-md-10">
                        <div class="menu-main d-flex align-items-center justify-content-end">
                            <!-- Main-menu -->
                            <div class="main-menu f-right d-none d-lg-block">
                                <nav>
                                    <ul id="navigation">
                                        <li><a href="/">Accueil</a></li>
                                        <li><a href="{{route('apropos')}}">A propos</a></li>
                                        <li><a href="{{route('contact')}}">Contact</a></li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="header-right-btn f-right d-none d-block ml-30">
                                <a href="{{route('dashboard')}}" class="btn header-btn">Connexion</a>
                            </div>
                        </div>
                    </div>
                    <!-- Mobile Menu -->
                    <div class="col-12">
                        <div class="mobile_menu d-block d-lg-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
</header>
<main>