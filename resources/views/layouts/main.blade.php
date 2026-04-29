<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sawdeera Toor | @yield('title')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/plugins/fontawesome-free/css/all.min.css">
    {{-- Font Albert Sans --}}
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    {{-- Bootstap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="/assets/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/assets/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="/assets/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="/assets/plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- datatable -->
    <link rel="stylesheet" href="{{ asset('assets/datatable/datatables.bundle.css') }}">
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- csrf token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* GLOBAL BODY BACKGROUND */

        body {
            background-color: #FFF8EE !important;
            font-family: 'Albert Sans', sans-serif;
        }

        /* content wrapper adminlte */
        .content-wrapper {
            background-color: #FFF8EE !important;
        }

        /* card biar kontras */
        .card {
            background: #ffffff;
        }

        /* table tetap putih */
        .table {
            background: #ffffff;
        }

        div.dataTables_wrapper div.dataTables_processing {
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #444 !important;
            /* abu-abu gelap */
            color: white !important;
            font-weight: 500;
            padding: 1rem 2rem !important;
            text-align: center;
            /* position */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;

            margin: 0;
            /* hilangkan margin agar tidak ganggu posisi */
            width: auto;
            /* biar ukurannya fleksibel sesuai konten */
        }

        .table-responsive {
            overflow-x: hidden !important;
        }

        table {
            width: 100% !important;
            table-layout: fixed;
        }

        table th,
        table td {
            white-space: normal !important;
            word-break: break-word;
        }

        .main-sidebar {
            background: #FAF1DE;
        }

        /* sidebar text */
        .main-sidebar .nav-link {
            color: #333 !important;
        }

        /* sidebar text hover */
        .main-sidebar .nav-link:hover {
            color: #000 !important;
        }

        /* user panel text */
        .user-panel .info a {
            color: #333 !important;
        }

        .main-sidebar .nav-link.active {
            background: #e9d8a6;
            color: #000;
        }

        .brand-link {
            background: #FAF1DE;
            border-bottom: 1px solid #e6d8b8;
        }

        /* BRAND SIDEBAR */
        .brand-center {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px 0;
        }

        .brand-logo {
            width: 180px;
            height: auto;
            opacity: 1;
        }

        /* hilangkan border bawaan adminlte */
        .brand-link {
            border-bottom: none !important;
        }

        /* NAVBAR COLOR */
        .main-header.navbar {
            background-color: #5C3A1A !important;
        }

        /* text navbar */
        .main-header .nav-link {
            color: #ffffff !important;
        }

        /* icon navbar */
        .main-header .nav-link i {
            color: #ffffff !important;
        }

        /* hover */
        .main-header .nav-link:hover {
            color: #f5e6d3 !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('img/logo.png') }}" alt="Sawdeera Toor Logo">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand">

            <!-- Left -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <!-- Right -->
            <ul class="navbar-nav ml-auto">

                <li class="nav-item">
                    <a href="#" class="nav-link text-danger" onclick="logoutConfirm()">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                        @csrf
                    </form>
                </li>

            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-light elevation-4">
            <!-- Brand Logo -->
            <a href="/dashboard" class="brand-link brand-center">
                <img src="{{ asset('img/logo.png') }}"
                    alt="Sawdeera Toor Logo"
                    class="brand-logo">
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="/assets/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                    </div>
                </div>

                @include('layouts.sidebar')

            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        @yield('content')
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="/assets/plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="/assets/plugins/sparklines/sparkline.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="/assets/plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="/assets/plugins/moment/moment.min.js"></script>
    <script src="/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="/assets/plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/assets/js/adminlte.js"></script>
    <!-- AdminLTE for demo purposes -->
    <!-- <script src="/assets/js/demo.js"></script> -->
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="/assets/js/pages/dashboard.js"></script>
    <!-- datatable js -->
    <script src="{{ asset('assets/datatable/datatables.bundle.js') }}"></script>
    {{-- format price input --}}
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>
    {{-- format timestamp --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.js"></script>
    {{-- logout --}}
    <script>
    function logoutConfirm(){
        Swal.fire({
            title: 'Yakin logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, logout'
        }).then((result) => {
            if(result.isConfirmed){
                document.getElementById('logout-form').submit();
            }
        })
    }
    </script>

    @stack('scripts')
</body>

</html>
