<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset(iconsLoad()['favicon']) }}?v={{ time() }}">

    <title>{{ $title }}</title>

    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ asset('assets/src/css/vendors_css.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ asset('assets/src/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/src/css/skin_color.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <script src="https://kit.fontawesome.com/111032cd6f.js" crossorigin="anonymous"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/livekit-client/dist/livekit-client.umd.min.js"></script>

    <!-- Toast notification -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <link href="{{ asset('css/mobiscroll.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/mobiscroll.js') }}"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Personnal style -->
    @stack('css')

</head>

<body class="hold-transition light-skin theme-info bg-s {{ request()->has('embed') ? 'is-embedded-form' : 'sidebar-mini fixed' }}">

    <div class="wrapper">
        @if(!request()->has('embed'))
            <!-- Header menu & header navbar -->
            @include('partials._header')
            <!-- Dashbord Menu -->
            @include('partials._menu')
        @endif

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper bg-s" style="{{ request()->has('embed') ? 'margin-left:0 !important; margin-top:0 !important; padding:15px !important;' : '' }}">
            <div class="container-full">
                @if(!request()->has('embed'))
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="d-flex align-items-center">
                        <div class="me-auto">
                            <div class="d-inline-block align-items-left ">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><b href="#"><i
                                                    class="fa-solid fa-home"></i></b></li>
                                        <li class="breadcrumb-item active fs-5" aria-current="page">{{ $title }}
                                            <span class="text-primary"> </span>
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>

                    </div>
                </div>
                @endif
                <!-- Main content -->
                <section class="content">
                    @yield('content')
                </section>
            </div>
        </div>
        <!-- And Content Wrapper. Contains page content -->

        @if(!request()->has('embed'))
            <!-- Start Page-Footer -->
            @include('partials._footer')
            <!-- End Page-Footer -->
        @endif

    </div>
    <style>
        .bg-s {
            background-image: url("{{ asset('assets/images/bg.jpg') }}");
        }

        .simplemenu {
            float: right;
        }

        .danger {
            color: red;
        }

        /* ===== MODE EMBED PROPRE POUR L'IFRAME DU FORMULAIRE ===== */
        body.is-embedded-form {
            background: #f4f6f9 !important;
            background-image: none !important;
            min-height: 100% !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        body.is-embedded-form .main-header,
        body.is-embedded-form .main-sidebar,
        body.is-embedded-form .main-footer,
        body.is-embedded-form .content-header,
        body.is-embedded-form footer {
            display: none !important;
        }

        body.is-embedded-form .wrapper {
            background: #f4f6f9 !important;
            background-image: none !important;
            min-height: 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            float: none !important;
            overflow: visible !important;
        }

        body.is-embedded-form .content-wrapper {
            margin-left: 0 !important;
            margin-top: 0 !important;
            margin-right: 0 !important;
            padding: 10px 15px 50px 15px !important;
            background: #f4f6f9 !important;
            background-image: none !important;
            min-height: 100vh !important;
            border-radius: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            overflow: visible !important;
        }

        body.is-embedded-form .container,
        body.is-embedded-form .container-full,
        body.is-embedded-form .container-fluid,
        body.is-embedded-form section.content {
            width: 100% !important;
            max-width: 100% !important;
            padding-left: 5px !important;
            padding-right: 5px !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            box-sizing: border-box !important;
        }

        /* Alléger les gros paddings fixes du template pour que tout tienne bien sur l'écran partagé */
        body.is-embedded-form .pe-105, body.is-embedded-form .ps-105 {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
        body.is-embedded-form .pe-60, body.is-embedded-form .ps-60,
        body.is-embedded-form .px-60, body.is-embedded-form .px-100,
        body.is-embedded-form .pe-50, body.is-embedded-form .ps-50,
        body.is-embedded-form .pe-40, body.is-embedded-form .ps-40 {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
    </style>


    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendor_components/jquery-ui/jquery-ui.min.js ') }}"></script>
    <script src="{{ asset('assets/src/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/src/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-select/dist/js/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.js') }}"></script>

    <script src="{{ asset('assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/OwlCarousel2/dist/owl.carousel.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/dropzone/dropzone.js') }}"></script>
    <!-- App -->
    <script src="{{ asset('assets/src/js/template.js') }}"></script>
    {{-- <script src="{{ asset('assets/src/js/pages/dashboard.js') }}"></script> --}}
    <script src="{{ asset('assets/src/js/pages/data-table.js') }}"></script>
    {{-- Form script  --}}
    <script src="{{ asset('assets/vendor_plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('assets/vendor_plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('assets/vendor_plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/moment/min/moment.min.js') }}"></script>

    <script src="{{ asset('assets/src/js/pages/advanced-form-element.js') }}"></script>

    <script src="{{ asset('assets/vendor_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}">
    </script>
    <script src="{{ asset('assets/vendor_components/fullcalendar/fullcalendar.js') }}"></script>

    <script src="{{ asset('assets/vendor_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}">
    </script>
    <script src="{{ asset('assets/vendor_plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- Personnal script -->
    <script src="{{ asset('assets/src/js/pages/calendar.js') }}"></script>

    @if(!request()->has('embed') && auth()->check() && auth()->user()->role_as === 'doctor')
        @include('partials.doctor_video_modal')
        @include('partials.doctor_incoming_call_modal')
    @endif

    @if((auth()->check() && auth()->user()->role_as === 'doctor') || request()->has('embed'))
        <script src="{{ asset('js/offline-consultations.js') }}?v={{ time() }}"></script>
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register("{{ asset('sw-doctor.js') }}?v={{ time() }}")
                    .catch(function(err) { console.warn('ServiceWorker registration failed:', err); });
            }
        </script>
    @endif

    @stack('js')


    <script>
        if (window.self !== window.top) {
            document.body.classList.add('is-embedded-form');
            var els = document.querySelectorAll('.main-header, .main-sidebar, .content-header, footer');
            els.forEach(function(el) { el.style.setProperty('display', 'none', 'important'); });
            var cw = document.querySelector('.content-wrapper');
            if (cw) {
                cw.style.setProperty('margin-left', '0', 'important');
                cw.style.setProperty('padding-top', '10px', 'important');
            }
        }
    </script>
</body>
