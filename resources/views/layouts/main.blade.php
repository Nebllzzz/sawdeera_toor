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
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
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
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
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

        .sidebar .user-panel .info {
            min-width: 0;
            white-space: normal !important;
            overflow: visible;
        }

        .sidebar .user-panel .info a {
            white-space: normal !important;
            word-break: break-word;
            overflow-wrap: anywhere;
            line-height: 1.2;
        }

        .sidebar .user-panel .info small {
            display: block;
            white-space: normal;
        }

        .sidebar .nav-sidebar > .nav-item > .nav-link.active {
            background: linear-gradient(135deg, #6B3E20, #8B5A2B) !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(107, 62, 32, 0.35);
        }

        .sidebar .nav-sidebar > .nav-item > .nav-link.active .nav-icon,
        .sidebar .nav-sidebar > .nav-item > .nav-link.active p {
            color: #ffffff !important;
        }

        .sidebar .nav-sidebar > .nav-item > .nav-link {
            border-radius: 8px;
            margin-bottom: 4px;
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
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            {{-- LEFT --}}
            <ul class="navbar-nav">

                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button" id="toggleSidebar">

                        <i class="fas fa-bars"></i>

                    </a>
                </li>

            </ul>

            {{-- RIGHT --}}
            <ul class="navbar-nav ml-auto">

                {{-- Notifications dropdown --}}
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#" id="notificationsToggle"
                            aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span id="notifBadge" class="badge badge-danger navbar-badge"
                                style="display: {{ auth()->user()->unreadNotifications->count() ? 'inline-block' : 'none' }};">{{ auth()->user()->unreadNotifications->count() }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="min-width:320px;">
                            <span class="dropdown-header">Notifikasi</span>
                            <div class="dropdown-divider"></div>
                            <div id="notifList" style="max-height:320px; overflow:auto;">
                                @forelse(auth()->user()->notifications->take(10) as $notification)
                                    <a href="#" class="dropdown-item">
                                        <div class="media">
                                            <img src="{{ asset('img/logo-kecil.png') }}" alt="avatar"
                                                class="img-size-50 mr-3 img-circle">
                                            <div class="media-body">
                                                <h3 class="dropdown-item-title" style="font-size:14px; font-weight:600;">
                                                    {{ $notification->data['title'] ?? class_basename($notification->type) }}
                                                </h3>
                                                <p class="text-sm" style="margin:0">
                                                    {{ $notification->data['message'] ?? '' }}</p>
                                                <p class="text-muted text-sm" style="margin:0">
                                                    {{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @empty
                                    <div class="px-3 py-2 text-muted">Tidak ada notifikasi</div>
                                @endforelse
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('notifications.index') ?? '#' }}" class="dropdown-item dropdown-footer">Lihat
                                semua</a>
                        </div>
                    </li>
                @endauth

                <li class="nav-item">

                    <a href="#" class="nav-link text-danger" onclick="logoutConfirm()">

                        <i class="fas fa-sign-out-alt mr-1"></i>

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

            {{-- BRAND --}}
            <a href="/dashboard" class="brand-link d-flex align-items-center justify-content-center">

                <img id="sidebarLogo" src="{{ asset('img/logo.png') }}" alt="Sawdeera Toor Logo" class="brand-image"
                    style="max-height:55px; width:auto; opacity:.95;">

            </a>

            <!-- Sidebar -->
            <div class="sidebar">

                {{-- USER PANEL --}}
                <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">

                    <div class="image flex-shrink-0">

                        <div class="d-flex align-items-center justify-content-center rounded-circle elevation-1"
                            style="
                                width:40px;
                                height:40px;
                                background:linear-gradient(135deg,#6B3E20,#8B5A2B);
                                color:white;
                                font-size:18px;
                            ">

                            <i class="fas fa-user"></i>

                        </div>

                    </div>

                    <div class="info" style="min-width:0;">

                        <a href="#" class="d-block font-weight-semibold"
                            style="
                                white-space:normal;
                                word-break:break-word;
                                overflow-wrap:anywhere;
                                line-height:1.2;
                            ">

                            {{ auth()->user()->name }}

                        </a>

                        <small class="text-muted text-capitalize d-block">

                            @if (auth()->user()->role === 'admin')
                                Pimpinan
                            @elseif(auth()->user()->role === 'operator')
                                Admin
                            @else
                                {{ auth()->user()->role }}
                            @endif

                        </small>

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
        function logoutConfirm() {
            Swal.fire({
                title: 'Yakin logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            })
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const body = document.body;
            const logo = document.getElementById('sidebarLogo');

            function updateSidebarLogo() {

                if (body.classList.contains('sidebar-collapse')) {

                    logo.src = "{{ asset('img/logo-kecil.png') }}";

                    logo.style.maxHeight = "40px";

                } else {

                    logo.src = "{{ asset('img/logo.png') }}";

                    logo.style.maxHeight = "55px";

                }

            }

            // initial
            updateSidebarLogo();

            // detect sidebar toggle
            document.getElementById('toggleSidebar')
                .addEventListener('click', function() {

                    setTimeout(() => {
                        updateSidebarLogo();
                    }, 300);

                });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const userMeta = document.querySelector('meta[name="user-id"]');
                if (!window.Echo || !userMeta) return;

                const userId = userMeta.getAttribute('content');
                const badge = document.getElementById('notifBadge');
                const list = document.getElementById('notifList');

                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        // prepend notification item
                        const item = document.createElement('a');
                        item.className = 'dropdown-item';
                        item.href = '#';
                        const html = `
                            <div class="media">
                                <img src="/img/logo-kecil.png" alt="avatar" class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title" style="font-size:14px; font-weight:600;">${notification.title ?? notification.type}</h3>
                                    <p class="text-sm" style="margin:0">${notification.message ?? (notification.data && notification.data.message) ?? ''}</p>
                                    <p class="text-muted text-sm" style="margin:0">Baru saja</p>
                                </div>
                            </div>
                        `;
                        item.innerHTML = html;
                        if (list.children.length) list.insertBefore(item, list.firstChild);
                        else list.appendChild(item);

                        // update badge
                        if (badge) {
                            const current = parseInt(badge.textContent || '0') || 0;
                            badge.textContent = current + 1;
                            badge.style.display = 'inline-block';
                        }
                    });

            } catch (e) {
                // silent
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
