<!-- Sidebar Menu -->
<div
    class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6"
    data-kt-menu="true"
>
    @php
        $role = auth()->user()->role;

        $active = fn (...$patterns) => request()->is(...$patterns)
            ? 'active'
            : '';

        /*
        |--------------------------------------------------------------------------
        | Status submenu operator
        |--------------------------------------------------------------------------
        */

        $isVerificationMenuActive = request()->is(
            'jemaah/registrasi',
            'jemaah/registrasi/*',
            'jemaah/data-verifikasi',
            'jemaah/data-verifikasi/*',
            'admin/dokumen',
            'admin/dokumen/*',
            'admin/pemabayan-admin',
            'admin/pemabayan-admin/*'
        );

        $isOperationalMenuActive = request()->is(
            'hotel',
            'hotel/*',
            'maskapai',
            'maskapai/*',
            'tour-leader',
            'tour-leader/*'
        );

        /*
        |--------------------------------------------------------------------------
        | Informasi bantuan
        |--------------------------------------------------------------------------
        */

        $helpUrl = $role === 'jemaah'
            ? 'https://wa.me/62895600791616?text=Saya%20ingin%20konsultasi%20terkait%20umrah'
            : 'https://wa.me/62895428500360?text=Saya%20ingin%20konsultasi%20terkait%20umrah';

        $helpLabel = $role === 'jemaah'
            ? 'Hubungi Admin'
            : 'Hubungi Tim IT';
    @endphp

    {{-- ============================================================
        DASHBOARD
    ============================================================= --}}
    <div class="menu-item">
        <a
            href="/dashboard"
            class="menu-link {{ $active('dashboard') }}"
        >
            <span class="menu-icon">
                <i class="fas fa-home"></i>
            </span>

            <span class="menu-title">
                Dashboard
            </span>
        </a>
    </div>

    {{-- ============================================================
        MENU JEMAAH
    ============================================================= --}}
    @if ($role === 'jemaah')

        <div class="menu-item">
            <a
                href="/paket-umrah-jemaah"
                class="menu-link {{ $active(
                    'paket-umrah-jemaah',
                    'paket-umrah-jemaah/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="fas fa-suitcase"></i>
                </span>

                <span class="menu-title">
                    Paket Umrah
                </span>
            </a>
        </div>

        <div class="menu-item">
            <a
                href="/pendaftaran-saya"
                class="menu-link {{ $active(
                    'pendaftaran-saya',
                    'pendaftaran-saya/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="far fa-user"></i>
                </span>

                <span class="menu-title">
                    Lengkapi Data Diri
                </span>
            </a>
        </div>

        <div class="menu-item">
            <a
                href="/dokumen"
                class="menu-link {{ $active(
                    'dokumen',
                    'dokumen/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="far fa-folder-open"></i>
                </span>

                <span class="menu-title">
                    Upload Dokumen Pendukung
                </span>
            </a>
        </div>

        <div class="menu-item">
            <a
                href="/pemabayan"
                class="menu-link {{ $active(
                    'pemabayan',
                    'pemabayan/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="far fa-credit-card"></i>
                </span>

                <span class="menu-title">
                    Upload Bukti Pembayaran
                </span>
            </a>
        </div>

        <div class="menu-item">
            <a
                href="/status-verifikasi"
                class="menu-link {{ $active('status-verifikasi') }}"
            >
                <span class="menu-icon">
                    <i class="fas fa-shield-alt"></i>
                </span>

                <span class="menu-title">
                    Status Verifikasi
                </span>
            </a>
        </div>

        <div class="menu-item">
            <a
                href="/keberangkatan-jemaah"
                class="menu-link {{ $active(
                    'keberangkatan-jemaah',
                    'keberangkatan-jemaah/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="far fa-calendar-alt"></i>
                </span>

                <span class="menu-title">
                   Jadwal Keberangkatan
                </span>
            </a>
        </div>

        <div class="menu-item">
            <a
                href="{{ route('profile') }}"
                class="menu-link {{ $active('profile') }}"
            >
                <span class="menu-icon">
                    <i class="far fa-address-card"></i>
                </span>

                <span class="menu-title">
                    Kelola Profil
                </span>
            </a>
        </div>

        <input
            type="hidden"
            id="hasActiveJadwal"
            value="{{ $hasActiveJadwal ?? 0 }}"
        >

    {{-- ============================================================
        MENU ADMIN
    ============================================================= --}}
    @elseif ($role === 'admin')

        {{-- Data Admin --}}
        <div class="menu-item">
            <a
                href="/user"
                class="menu-link {{ $active(
                    'user',
                    'user/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="fas fa-user-shield"></i>
                </span>

                <span class="menu-title">
                    Data Admin
                </span>
            </a>
        </div>

        {{-- Monitoring Data Jemaah --}}
        <div class="menu-item">
            <a
                href="/laporan/jemaah"
                class="menu-link {{ $active(
                    'laporan/jemaah',
                    'laporan/jemaah/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="far fa-chart-bar"></i>
                </span>

                <span class="menu-title">
                    Monitoring Data Jemaah
                </span>
            </a>
        </div>

        {{-- Rekapitulasi Data Jemaah --}}
        <div class="menu-item">
            <a
                href="{{ route('admin.jemaah-recap.index') }}"
                class="menu-link {{ $active(
                    'admin/rekapitulasi-jemaah',
                    'admin/rekapitulasi-jemaah/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="fas fa-chart-column"></i>
                </span>

                <span class="menu-title">
                    Laporan Rekapitulasi Data Jemaah
                </span>
            </a>
        </div>

        {{-- Jadwal Keberangkatan --}}
        <div class="menu-item">
            <a
                href="/keberangkatan"
                class="menu-link {{ $active(
                    'keberangkatan',
                    'keberangkatan/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="far fa-calendar-check"></i>
                </span>

                <span class="menu-title">
                    Approval Jadwal Keberangkatan
                </span>
            </a>
        </div>

    {{-- ============================================================
        MENU OPERATOR
    ============================================================= --}}
    @elseif ($role === 'operator')

        {{-- ========================================================
            GROUP VERIFIKASI JEMAAH
        ========================================================= --}}
        <div
            data-kt-menu-trigger="click"
            class="menu-item menu-accordion
                {{ $isVerificationMenuActive ? 'here show' : '' }}"
        >
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="fas fa-user-check"></i>
                </span>

                <span class="menu-title">
                    Verifikasi Jemaah
                </span>

                <span class="menu-arrow"></span>
            </span>

            <div
                class="menu-sub menu-sub-accordion
                    {{ $isVerificationMenuActive ? 'show' : '' }}"
            >
                {{-- Verifikasi Registrasi Akun --}}
                <div class="menu-item">
                    <a
                        href="/jemaah/registrasi"
                        class="menu-link {{ $active(
                            'jemaah/registrasi',
                            'jemaah/registrasi/*'
                        ) }}"
                    >
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>

                        <span class="menu-title">
                            Verifikasi Registrasi Akun
                        </span>
                    </a>
                </div>

                {{-- Verifikasi Data Jemaah --}}
                <div class="menu-item">
                    <a
                        href="/jemaah/data-verifikasi"
                        class="menu-link {{ $active(
                            'jemaah/data-verifikasi',
                            'jemaah/data-verifikasi/*'
                        ) }}"
                    >
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>

                        <span class="menu-title">
                            Verifikasi Data Jemaah
                        </span>
                    </a>
                </div>

                {{-- Verifikasi Dokumen --}}
                <div class="menu-item">
                    <a
                        href="/admin/dokumen"
                        class="menu-link {{ $active(
                            'admin/dokumen',
                            'admin/dokumen/*'
                        ) }}"
                    >
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>

                        <span class="menu-title">
                            Verifikasi Dokumen
                        </span>
                    </a>
                </div>

                {{-- Verifikasi Pembayaran --}}
                <div class="menu-item">
                    <a
                        href="/admin/pemabayan-admin"
                        class="menu-link {{ $active(
                            'admin/pemabayan-admin',
                            'admin/pemabayan-admin/*'
                        ) }}"
                    >
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>

                        <span class="menu-title">
                            Verifikasi Pembayaran
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="menu-item">
            <a
                href="/laporan/jemaah"
                class="menu-link {{ $active(
                    'laporan/jemaah',
                    'laporan/jemaah/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="far fa-chart-bar"></i>
                </span>

                <span class="menu-title">
                    Monitoring Data Jemaah
                </span>
            </a>
        </div>

        {{-- Jadwal Keberangkatan --}}
        <div class="menu-item">
            <a
                href="/keberangkatan"
                class="menu-link {{ $active(
                    'keberangkatan',
                    'keberangkatan/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="far fa-calendar-check"></i>
                </span>

                <span class="menu-title">
                    Approval Jadwal Keberangkatan
                </span>
            </a>
        </div>

        {{-- Paket Umrah --}}
        <div class="menu-item">
            <a
                href="/paket-umrah"
                class="menu-link {{ $active(
                    'paket-umrah',
                    'paket-umrah/*'
                ) }}"
            >
                <span class="menu-icon">
                    <i class="fas fa-kaaba"></i>
                </span>

                <span class="menu-title">
                    Paket Umrah
                </span>
            </a>
        </div>

        {{-- ========================================================
            GROUP DATA OPERASIONAL
        ========================================================= --}}
        <div
            data-kt-menu-trigger="click"
            class="menu-item menu-accordion
                {{ $isOperationalMenuActive ? 'here show' : '' }}"
        >
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="fas fa-database"></i>
                </span>

                <span class="menu-title">
                    Data Operasional
                </span>

                <span class="menu-arrow"></span>
            </span>

            <div
                class="menu-sub menu-sub-accordion
                    {{ $isOperationalMenuActive ? 'show' : '' }}"
            >
                {{-- Data Hotel --}}
                <div class="menu-item">
                    <a
                        href="/hotel"
                        class="menu-link {{ $active(
                            'hotel',
                            'hotel/*'
                        ) }}"
                    >
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>

                        <span class="menu-title">
                            Data Hotel
                        </span>
                    </a>
                </div>

                {{-- Data Maskapai --}}
                <div class="menu-item">
                    <a
                        href="/maskapai"
                        class="menu-link {{ $active(
                            'maskapai',
                            'maskapai/*'
                        ) }}"
                    >
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>

                        <span class="menu-title">
                            Data Maskapai
                        </span>
                    </a>
                </div>

                {{-- Tour Leader --}}
                <div class="menu-item">
                    <a
                        href="/tour-leader"
                        class="menu-link {{ $active(
                            'tour-leader',
                            'tour-leader/*'
                        ) }}"
                    >
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>

                        <span class="menu-title">
                            Tour Leader
                        </span>
                    </a>
                </div>
            </div>
        </div>

    @endif

    {{-- ============================================================
        BANTUAN
    ============================================================= --}}
    <div class="sidebar-help-card">
        <b>Butuh Bantuan?</b>

        <p class="mb-0">
            {{ $role === 'jemaah'
                ? 'Hubungi kami jika Anda memiliki pertanyaan seputar pendaftaran Umrah Anda.'
                : 'Hubungi tim teknis jika ada kendala pada sistem operasional.'
            }}
        </p>

        <a
            href="{{ $helpUrl }}"
            target="_blank"
            rel="noopener"
        >
            <i class="fab fa-whatsapp"></i>
            {{ $helpLabel }}
        </a>

        <small>
            Jam Operasional
            <br>
            08.00 - 17.00 WIB
        </small>
    </div>
</div>
