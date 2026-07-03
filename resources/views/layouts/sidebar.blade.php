<!-- Sidebar Menu -->
<nav class="mt-2">

    @php
        $role = auth()->user()->role;

        $active = function (...$patterns) {
            return request()->is(...$patterns) ? 'active' : '';
        };
    @endphp

    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        {{-- =========================================
            DASHBOARD
        ========================================== --}}
        <li class="nav-header text-uppercase">
            Main Menu
        </li>

        <li class="nav-item">
            <a href="/dashboard" class="nav-link {{ $active('dashboard') }}">
                <i class="nav-icon fas fa-home"></i>
                <p>Dashboard</p>
            </a>
        </li>

        {{-- =========================================
            PROFILE JEMAAH
        ========================================== --}}
        @if ($role === 'jemaah')
            <li class="nav-header text-uppercase">
                Akun Saya
            </li>

            <li class="nav-item">
                <a href="/profile" class="nav-link {{ $active('profile', 'profile/*') }}">
                    <i class="nav-icon fas fa-user"></i>
                    <p>Profile</p>
                </a>
            </li>
        @endif


        {{-- =========================================
            MASTER DATA ADMIN
        ========================================== --}}
        @if ($role === 'admin')
            <li class="nav-header text-uppercase">
                Management Pimpinan
            </li>

            <li class="nav-item">
                <a href="/user" class="nav-link {{ $active('user', 'user/*') }}">
                    <i class="nav-icon fas fa-user-shield"></i>
                    <p>Data Admin</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="/laporan/jemaah" class="nav-link {{ $active('laporan/jemaah', 'laporan/jemaah/*') }}">
                    <i class="nav-icon fas fa-file-export"></i>
                    <p>Laporan Jemaah</p>
                </a>
            </li>
        @endif


        {{-- =========================================
            MASTER DATA
        ========================================== --}}
        @if (in_array($role, ['admin', 'operator']))
            <li class="nav-header text-uppercase">
                Master Data
            </li>

            <li class="nav-item">
                <a href="/jemaah" class="nav-link {{ $active('jemaah', 'jemaah/*') }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Data Jemaah</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="/paket-umrah" class="nav-link {{ $active('paket-umrah', 'paket-umrah/*') }}">
                    <i class="nav-icon fas fa-kaaba"></i>
                    <p>Paket Umrah</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="/hotel" class="nav-link {{ $active('hotel', 'hotel/*') }}">
                    <i class="nav-icon fas fa-hotel"></i>
                    <p>Data Hotel</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="/maskapai" class="nav-link {{ $active('maskapai', 'maskapai/*') }}">
                    <i class="nav-icon fas fa-plane"></i>
                    <p>Data Maskapai</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="/tour-leader" class="nav-link {{ $active('tour-leader', 'tour-leader/*') }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>Tour Leader</p>
                </a>
            </li>


            {{-- =========================================
                OPERASIONAL
            ========================================== --}}
            <li class="nav-header text-uppercase">
                Operasional
            </li>

            <li class="nav-item">
                <a href="/keberangkatan" class="nav-link {{ $active('keberangkatan', 'keberangkatan/*') }}">
                    <i class="nav-icon fas fa-plane-departure"></i>
                    <p>Jadwal Keberangkatan</p>
                </a>
            </li>


            {{-- =========================================
                VERIFIKASI
            ========================================== --}}
            <li class="nav-header text-uppercase">
                Verifikasi
            </li>

            <li class="nav-item">
                <a href="/admin/dokumen" class="nav-link {{ $active('admin/dokumen', 'admin/dokumen/*') }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>Verifikasi Dokumen</p>
                </a>
            </li>

            <li class="nav-item mb-3">
                <a href="/admin/pemabayan-admin" class="nav-link {{ $active('admin/pemabayan-admin', 'admin/pemabayan-admin/*') }}">
                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                    <p>Verifikasi Pembayaran</p>
                </a>
            </li>
        @endif


        {{-- =========================================
            MENU JEMAAH
        ========================================== --}}
        @if ($role === 'jemaah')
            <li class="nav-header text-uppercase">
                Layanan Jemaah
            </li>

            <li class="nav-item">
                <a href="/dokumen" class="nav-link {{ $active('dokumen', 'dokumen/*') }}">
                    <i class="nav-icon fas fa-file-upload"></i>
                    <p>Upload Dokumen</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="/pemabayan" class="nav-link {{ $active('pemabayan', 'pemabayan/*') }}">
                    <i class="nav-icon fas fa-receipt"></i>
                    <p>Pembayaran</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="/keberangkatan-jemaah" class="nav-link {{ $active('keberangkatan-jemaah', 'keberangkatan-jemaah/*') }}">
                    <i class="nav-icon fas fa-plane"></i>
                    <p>Keberangkatan Saya</p>
                </a>
            </li>

            <li class="nav-item">
                <input type="hidden" id="hasActiveJadwal" value="{{ $hasActiveJadwal ?? 0 }}">
            </li>
        @endif

    </ul>

</nav>
<!-- /.sidebar-menu -->
