<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sawdeera Toor | @yield('title')</title>

    <base href="/" />
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" />

    <link rel="stylesheet" href="{{ asset('assets-2/fonts/inter/inter.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets-2/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets-2/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets-2/plugins/sweetalert2/css/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets-2/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets-2/css/style.bundle.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @yield('css')

    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else if (localStorage.getItem("data-bs-theme") !== null) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #FBF8F2;
        }

        @media (min-width: 992px) {
            .wrapper {
                padding-left: 280px !important;
            }

            .header-fixed[data-kt-sticky-header=on] .header {
                left: 280px !important;
            }
        }

        @media (max-width: 991.98px) {
            .wrapper {
                padding-left: 0 !important;
            }
        }

        .aside {
            width: 280px !important;
            background: linear-gradient(180deg, #2a1a0d 0%, #170f08 100%) !important;
            border-right: 1px solid rgba(255,255,255,0.08) !important;
        }

        .aside .menu .menu-item .menu-link {
            color: #eee3d0 !important;
            border-radius: 9px;
            margin: 3px 14px;
            min-height: 44px;
            padding: 9px 13px;
            transition: .18s ease;
        }

        .aside .menu .menu-item .menu-link:hover,
        .aside .menu .menu-item .menu-link.active {
            background: linear-gradient(135deg, #c48a27, #9a671d) !important;
            color: #fff !important;
            box-shadow: 0 8px 18px rgba(166, 109, 18, 0.28);
        }

        .aside .menu .menu-title {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
        }

        .aside-logo {
            background: #1a1109;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding-top: 18px !important;
            padding-bottom: 18px !important;
        }

        .aside-menu {
            padding-top: 4px;
        }

        .aside-menu .hover-scroll-overlay-y {
            height: calc(100vh - 140px) !important;
            overflow-y: auto !important;

            /* Tambahkan ini untuk menyembunyikan scrollbar */
            -ms-overflow-style: none;  /* Untuk Internet Explorer dan Edge kuno */
            scrollbar-width: none;     /* Untuk Firefox */
        }

        /* Tambahkan ini untuk Chrome, Safari, dan Opera */
        .aside-menu .hover-scroll-overlay-y::-webkit-scrollbar {
            display: none;
        }

        .aside .menu-icon{
            background: transparent !important;
            color: #d5a849 !important;
            width: 24px;
            min-width: 24px;
            margin-right: 10px;
            font-size: 15px;
        }

        .aside .menu-link.active .menu-icon,
        .aside .menu-link:hover .menu-icon {
            color: #fff !important;
        }

        .sidebar-help-card {
            margin: 28px 14px 14px;
            padding: 16px;
            border-radius: 10px;
            background: linear-gradient(160deg, rgba(122,80,22,.45), rgba(38,24,11,.9));
            border: 1px solid rgba(230,194,122,.18);
            color: #f8efd9;
            box-shadow: inset 0 1px rgba(255,255,255,.05);
        }

        .sidebar-help-card b {
            display: block;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .sidebar-help-card p,
        .sidebar-help-card small {
            color: #cdbf9f;
            font-size: 11px;
            line-height: 1.45;
        }

        .sidebar-help-card a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 36px;
            border-radius: 7px;
            color: #f7d58a;
            border: 1px solid rgba(231,185,90,.45);
            background: rgba(70,45,15,.7);
            font-weight: 700;
            font-size: 12px;
            margin: 12px 0 8px;
        }

        .header {
            background: #fff !important;
            border-bottom: 1px solid #e8e8e8;
            height: 96px !important;
            min-height: 96px;
            padding: 0 1rem;
        }

        .header .container-fluid {
            min-height: 96px;
            gap: 16px;
            align-items: center !important;
        }

        .header h2 {
            margin: 2px 0 6px;
            font-size: 22px;
            line-height: 1.2;
            font-weight: 800;
        }

        .header small {
            line-height: 1.35;
        }

        .content {
            margin-top: 0 !important;
            padding: 0 0 24px !important;
        }

        .app-content-container {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .account-menu {
            min-width: 280px;
            border: 0;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .account-menu .account-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px 14px;
        }

        .account-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff0f2;
            color: #ef4767;
            font-weight: 700;
            object-fit: cover;
        }

        .dropdown-item {
            padding: 10px 18px;
        }

        .content-wrapper {
            padding: 0;
            background: transparent;
        }

        .recap-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin: 4px 0 18px;
        }

        .recap-heading h1 {
            margin: 8px 0 4px;
            color: #241d16;
            font-size: 25px;
            line-height: 1.2;
            font-weight: 800;
        }

        .recap-heading p {
            margin: 0;
            color: #7c766f;
            font-size: 13px;
        }

        .recap-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            color: #928a80;
            font-size: 11px;
        }

        .recap-breadcrumb a {
            color: #8b5b19;
            font-weight: 700;
        }

        .recap-breadcrumb i {
            color: #bbb2a8;
            font-size: 7px;
        }

        .recap-heading-actions {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 20px;
        }

        @media (max-width: 700px) {
            .recap-heading {
                display: block;
            }

            .recap-heading-actions {
                width: 100%;
                margin-top: 14px;
            }
        }

        .card {
            border-radius: 14px;
            border: 1px solid #e4e6ef;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 64px;
            padding: 1rem 1.25rem;
        }

        .card-header h2,
        .card-header h4 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .card-header .btn-sawdeera1 {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 0.625rem 1rem !important;
            border-radius: 10px;
            line-height: 1.2;
            white-space: nowrap;
        }

        .card-header .btn-sawdeera1 i {
            margin: 0 !important;
            line-height: 1;
        }

        table .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px !important;
            height: 36px !important;
            padding: 0 !important;
            border-radius: 10px;
            line-height: 1;
            vertical-align: middle;
        }

        table .btn-icon i {
            margin: 0 !important;
            line-height: 1;
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
    </style>
</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-disabled">
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else if (localStorage.getItem("data-bs-theme") !== null) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">
            <div id="kt_aside" class="aside aside-light aside-hoverable">
                <div class="aside-logo flex-column-auto px-8 py-8">
                    <a href="/dashboard" class="d-flex align-items-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Sawdeera Toor" class="h-55px" />
                    </a>
                </div>

                <div class="aside-menu flex-column-fluid">
                    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper">
                        @include('layouts.sidebar')
                    </div>
                </div>
            </div>

            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <div class="header align-items-stretch">
                    <div class="container-fluid d-flex align-items-stretch justify-content-between px-8">
                        <div>
                            <small class="fs-4">Assalamu'alaikum,</small>
                            <h2>Halo, {{ Auth::user()->name }}</h2>
                            <small>Selamat datang di Dashboard Sawdeera Toor</small>
                        </div>

                        <div class="d-flex align-items-center gap-3 py-3">
                            @auth
                                <div class="dropdown">
                                    <a class="btn btn-icon btn-light btn-active-color-primary position-relative" href="#" data-bs-toggle="dropdown" id="notificationsToggle" aria-expanded="false">
                                        <i class="fas fa-bell fs-4"></i>
                                        <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                            style="display: {{ auth()->user()->unreadNotifications->count() ? 'inline-block' : 'none' }};">
                                            {{ auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg" style="min-width:320px;">
                                        <span class="dropdown-header">Notifikasi</span>
                                        <div class="dropdown-divider"></div>
                                        <div id="notifList" style="max-height:320px; overflow:auto;">
                                            @forelse(auth()->user()->notifications->take(10) as $notification)
                                                <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <img src="{{ asset('img/logo-kecil.png') }}" alt="avatar" class="img-fluid rounded-circle" style="width:38px;height:38px;">
                                                        <div>
                                                            <div class="fw-semibold">{{ $notification->data['title'] ?? class_basename($notification->type) }}</div>
                                                            <div class="text-muted small">{{ $notification->data['message'] ?? '' }}</div>
                                                            <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                            @empty
                                                <div class="px-3 py-2 text-muted">Tidak ada notifikasi</div>
                                            @endforelse
                                        </div>
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('notifications.index') ?? '#' }}" class="dropdown-item text-primary">Lihat semua</a>
                                    </div>
                                </div>
                            @endauth

                            @auth
                                @php
                                    $navbarPhoto = auth()->user()->jemaah?->foto_profil;
                                @endphp
                                <div class="dropdown">
                                    <a href="#" class="btn btn-icon btn-light btn-active-color-primary" data-bs-toggle="dropdown" aria-label="Menu akun">
                                        @if($navbarPhoto)
                                            <img src="{{ asset('storage/'.$navbarPhoto) }}" class="account-avatar" alt="Foto profil">
                                        @else
                                            <span class="account-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                        @endif
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end account-menu">
                                        <div class="account-head">
                                            @if($navbarPhoto)
                                                <img src="{{ asset('storage/'.$navbarPhoto) }}" class="account-avatar" alt="">
                                            @else
                                                <span class="account-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                                <div class="text-muted small">{{ auth()->user()->email }}</div>
                                            </div>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('profile') }}" class="dropdown-item"><i class="far fa-user me-2"></i>Profile</a>
                                        <a href="#" class="dropdown-item" onclick="logoutConfirm();return false"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a>
                                    </div>
                                </div>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="container-fluid app-content-container">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var hostUrl = "{{ asset('assets-2') }}/";
    </script>

    <script src="{{ asset('assets-2/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets-2/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets-2/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets-2/plugins/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets-2/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets-2/js/widgets.bundle.js') }}"></script>
    <script src="{{ asset('assets-2/js/custom/widgets.js') }}"></script>
    <script src="{{ asset('assets-2/js/custom/apps/chat/chat.js') }}"></script>
    <script src="{{ asset('assets-2/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
    <script src="{{ asset('assets-2/js/custom/utilities/modals/create-app.js') }}"></script>
    <script src="{{ asset('assets-2/js/custom/utilities/modals/users-search.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.js"></script>


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
            });
        }

        function showAppModal(target) {
            const element = typeof target === 'string'
                ? document.getElementById(target.replace(/^#/, ''))
                : target;

            if (!element) return;
            if (window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(element).show();
                return;
            }
            if (window.jQuery && typeof window.jQuery(element).modal === 'function') {
                window.jQuery(element).modal('show');
            }
        }

        function hideAppModal(target) {
            const element = typeof target === 'string'
                ? document.getElementById(target.replace(/^#/, ''))
                : target;

            if (!element) return;
            if (window.bootstrap && window.bootstrap.Modal) {
                const modal = window.bootstrap.Modal.getInstance(element);
                if (modal) modal.hide();
                return;
            }
            if (window.jQuery && typeof window.jQuery(element).modal === 'function') {
                window.jQuery(element).modal('hide');
            }
        }

        document.addEventListener('click', function(event) {
            const closeButton = event.target.closest('[data-dismiss="modal"], [data-bs-dismiss="modal"], .btnCloseModal');
            if (!closeButton) return;
            const modal = closeButton.closest('.modal');
            if (modal) hideAppModal(modal);
        }, true);

        document.addEventListener('DOMContentLoaded', function() {
            try {
                const userMeta = document.querySelector('meta[name="user-id"]');
                if (!window.Echo || !userMeta) return;

                const userId = userMeta.getAttribute('content');
                const badge = document.getElementById('notifBadge');
                const list = document.getElementById('notifList');

                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        const item = document.createElement('a');
                        item.className = 'dropdown-item';
                        item.href = notification.url ?? (notification.data && notification.data.url) ?? '#';
                        item.innerHTML = `
                            <div class="d-flex align-items-start gap-3">
                                <img src="{{ asset('img/logo-kecil.png') }}" alt="avatar" class="img-fluid rounded-circle" style="width:38px;height:38px;">
                                <div>
                                    <div class="fw-semibold">${notification.title ?? notification.type}</div>
                                    <div class="text-muted small">${notification.message ?? (notification.data && notification.data.message) ?? ''}</div>
                                    <div class="text-muted small">Baru saja</div>
                                </div>
                            </div>
                        `;
                        if (list && list.children.length) {
                            list.insertBefore(item, list.firstChild);
                        } else if (list) {
                            list.appendChild(item);
                        }

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
