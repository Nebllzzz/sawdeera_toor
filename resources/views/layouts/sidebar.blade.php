<!-- Sidebar Menu -->
<div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" data-kt-menu="true">
    @php
        $role = auth()->user()->role;

        $active = function (...$patterns) {
            return request()->is(...$patterns) ? 'active' : '';
        };
    @endphp

    <div class="menu-item">
        <div class="menu-content pb-2">
            <span class="menu-section text-muted text-uppercase fs-8 ls-1">Main Menu</span>
        </div>
    </div>

    <div class="menu-item">
        <a href="/dashboard" class="menu-link {{ $active('dashboard') }}">
            <span class="menu-icon"><i class="fas fa-home"></i></span>
            <span class="menu-title">Dashboard</span>
        </a>
    </div>

    @if ($role === 'jemaah')
        <div class="menu-item">
            <div class="menu-content pt-8 pb-2">
                <span class="menu-section text-muted text-uppercase fs-8 ls-1">Akun Saya</span>
            </div>
        </div>

        <div class="menu-item">
            <a href="/pendaftaran-saya" class="menu-link {{ $active('pendaftaran-saya', 'pendaftaran-saya/*') }}">
                <span class="menu-icon"><i class="fas fa-user"></i></span>
                <span class="menu-title">Pendaftaran Saya</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="/status-verifikasi" class="menu-link {{ $active('status-verifikasi') }}">
                <span class="menu-icon"><i class="fas fa-tasks"></i></span>
                <span class="menu-title">Status Verifikasi</span>
            </a>
        </div>
    @endif

    @if ($role === 'admin')
        <div class="menu-item">
            <div class="menu-content pt-8 pb-2">
                <span class="menu-section text-muted text-uppercase fs-8 ls-1">Management Pimpinan</span>
            </div>
        </div>

        <div class="menu-item">
            <a href="/user" class="menu-link {{ $active('user', 'user/*') }}">
                <span class="menu-icon"><i class="fas fa-user-shield"></i></span>
                <span class="menu-title">Data Admin</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="/laporan/jemaah" class="menu-link {{ $active('laporan/jemaah', 'laporan/jemaah/*') }}">
                <span class="menu-icon"><i class="fas fa-file-export"></i></span>
                <span class="menu-title">Laporan Jemaah</span>
            </a>
        </div>
    @endif

    @if (in_array($role, ['admin', 'operator']))
        <div class="menu-item">
            <div class="menu-content pt-8 pb-2">
                <span class="menu-section text-muted text-uppercase fs-8 ls-1">Master Data</span>
            </div>
        </div>

        <div class="menu-item">
            <a href="/jemaah" class="menu-link {{ $active('jemaah', 'jemaah/*') }}">
                <span class="menu-icon"><i class="fas fa-users"></i></span>
                <span class="menu-title">Data Jemaah</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="/paket-umrah" class="menu-link {{ $active('paket-umrah', 'paket-umrah/*') }}">
                <span class="menu-icon"><i class="fas fa-kaaba"></i></span>
                <span class="menu-title">Paket Umrah</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="/hotel" class="menu-link {{ $active('hotel', 'hotel/*') }}">
                <span class="menu-icon"><i class="fas fa-hotel"></i></span>
                <span class="menu-title">Data Hotel</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="/maskapai" class="menu-link {{ $active('maskapai', 'maskapai/*') }}">
                <span class="menu-icon"><i class="fas fa-plane"></i></span>
                <span class="menu-title">Data Maskapai</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="/tour-leader" class="menu-link {{ $active('tour-leader', 'tour-leader/*') }}">
                <span class="menu-icon"><i class="fas fa-user-tie"></i></span>
                <span class="menu-title">Tour Leader</span>
            </a>
        </div>

        <div class="menu-item">
            <div class="menu-content pt-8 pb-2">
                <span class="menu-section text-muted text-uppercase fs-8 ls-1">Operasional</span>
            </div>
        </div>

        <div class="menu-item">
            <a href="/keberangkatan" class="menu-link {{ $active('keberangkatan', 'keberangkatan/*') }}">
                <span class="menu-icon"><i class="fas fa-plane-departure"></i></span>
                <span class="menu-title">Jadwal Keberangkatan</span>
            </a>
        </div>

        <div class="menu-item">
            <div class="menu-content pt-8 pb-2">
                <span class="menu-section text-muted text-uppercase fs-8 ls-1">Verifikasi</span>
            </div>
        </div>

        <div class="menu-item">
            <a href="/admin/dokumen" class="menu-link {{ $active('admin/dokumen', 'admin/dokumen/*') }}">
                <span class="menu-icon"><i class="fas fa-file-alt"></i></span>
                <span class="menu-title">Verifikasi Dokumen</span>
            </a>
        </div>

        <div class="menu-item mb-3">
            <a href="/admin/pemabayan-admin" class="menu-link {{ $active('admin/pemabayan-admin', 'admin/pemabayan-admin/*') }}">
                <span class="menu-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                <span class="menu-title">Verifikasi Pembayaran</span>
            </a>
        </div>
    @endif

    @if ($role === 'jemaah')
        <div class="menu-item">
            <div class="menu-content pt-8 pb-2">
                <span class="menu-section text-muted text-uppercase fs-8 ls-1">Layanan Jemaah</span>
            </div>
        </div>

        <div class="menu-item">
            <a href="/dokumen" class="menu-link {{ $active('dokumen', 'dokumen/*') }}">
                <span class="menu-icon"><i class="fas fa-file-upload"></i></span>
                <span class="menu-title">Dokumen Pendukung</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="/pemabayan" class="menu-link {{ $active('pemabayan', 'pemabayan/*') }}">
                <span class="menu-icon"><i class="fas fa-receipt"></i></span>
                <span class="menu-title">Pembayaran</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="/paket-umrah-jemaah" class="menu-link {{ $active('paket-umrah-jemaah', 'paket-umrah-jemaah/*') }}">
                <span class="menu-icon"><i class="fas fa-box-open"></i></span>
                <span class="menu-title">Paket Umrah</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="/keberangkatan-jemaah" class="menu-link {{ $active('keberangkatan-jemaah', 'keberangkatan-jemaah/*') }}">
                <span class="menu-icon"><i class="fas fa-plane"></i></span>
                <span class="menu-title">Keberangkatan Saya</span>
            </a>
        </div>

        <div class="menu-item">
            <input type="hidden" id="hasActiveJadwal" value="{{ $hasActiveJadwal ?? 0 }}">
        </div>
    @endif
</div>
