<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    >

    <title>Register - Sawdeera Tour</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    >

    <style>
        :root {
            --primary-brown: #5c3a16;
            --primary-brown-dark: #3f260d;
            --background-brown: #211408;
            --background-brown-light: #322012;
            --gold: #c89b2c;
            --gold-dark: #a67c1b;
            --card-background: #fffdf9;
            --text-dark: #342318;
            --text-muted: #8b7668;
            --border-color: #e8ddd4;
            --danger: #dc3545;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            background: var(--background-brown);
            font-family: Arial, Helvetica, sans-serif;
        }

        .register-page {
            position: relative;
            isolation: isolate;

            display: flex;
            align-items: center;
            justify-content: center;

            width: 100%;
            height: 100vh;
            height: 100dvh;

            padding: 24px 20px;
            overflow: hidden;

            background:
                radial-gradient(
                    circle at top,
                    rgba(133, 84, 34, 0.28) 0%,
                    rgba(33, 20, 8, 0) 42%
                ),
                linear-gradient(
                    135deg,
                    var(--background-brown-light) 0%,
                    var(--background-brown) 55%,
                    #160d05 100%
                );
        }

        .register-decoration {
            position: absolute;
            z-index: -1;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(2px);
        }

        .register-decoration-one {
            top: -150px;
            right: -110px;
            width: 380px;
            height: 380px;
            background: rgba(200, 155, 44, 0.07);
        }

        .register-decoration-two {
            bottom: -190px;
            left: -140px;
            width: 440px;
            height: 440px;
            background: rgba(133, 84, 34, 0.12);
        }

        .register-card {
            position: relative;
            z-index: 2;

            width: 100%;
            max-width: 640px;
            max-height: calc(100vh - 48px);
            max-height: calc(100dvh - 48px);

            padding: 36px 42px 34px;
            overflow-x: hidden;
            overflow-y: auto;

            background: var(--card-background);
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 24px;

            box-shadow:
                0 30px 70px rgba(0, 0, 0, 0.35),
                0 8px 25px rgba(0, 0, 0, 0.16);

            scrollbar-width: thin;
            scrollbar-color: rgba(92, 58, 22, 0.25) transparent;
        }

        .register-card::-webkit-scrollbar {
            width: 5px;
        }

        .register-card::-webkit-scrollbar-track {
            background: transparent;
        }

        .register-card::-webkit-scrollbar-thumb {
            background: rgba(92, 58, 22, 0.25);
            border-radius: 10px;
        }

        .register-card::before {
            position: absolute;
            top: 0;
            left: 0;

            width: 100%;
            height: 6px;

            content: "";

            background: linear-gradient(
                90deg,
                var(--primary-brown),
                var(--gold),
                var(--primary-brown)
            );
        }

        .register-header {
            text-align: center;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;

            min-height: 76px;
            margin-bottom: 12px;
        }

        .brand-logo img {
            display: block;
            width: 100%;
            max-width: 210px;
            max-height: 82px;
            object-fit: contain;
        }

        .register-title {
            margin: 0 0 8px;

            color: var(--text-dark);
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .register-subtitle {
            max-width: 500px;
            margin: 0 auto 25px;

            color: var(--text-muted);
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: 12px;

            margin-bottom: 18px;

            color: var(--text-dark);
            font-size: 0.92rem;
            font-weight: 700;
        }

        .form-section-title::after {
            flex: 1;
            height: 1px;
            content: "";
            background: var(--border-color);
        }

        .form-group {
            margin-bottom: 17px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;

            color: var(--text-dark);
            font-size: 0.88rem;
            font-weight: 600;
        }

        .required-mark {
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            height: 50px;
            padding: 12px 15px;

            color: var(--text-dark);
            font-size: 0.94rem;

            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .form-control::placeholder {
            color: #b0a39b;
        }

        .form-control:hover {
            border-color: #cfb9a7;
        }

        .form-control:focus {
            color: var(--text-dark);
            background: #ffffff;
            border-color: var(--gold);
            outline: none;
            box-shadow: 0 0 0 4px rgba(200, 155, 44, 0.14);
        }

        .form-control.is-invalid {
            padding-right: 15px;
            background-image: none;
            border-color: var(--danger);
        }

        .form-control.is-invalid:focus {
            border-color: var(--danger);
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.12);
        }

        .invalid-feedback {
            margin-top: 6px;
            font-size: 0.8rem;
        }

        .form-helper {
            display: block;
            margin-top: 6px;

            color: var(--text-muted);
            font-size: 0.78rem;
            line-height: 1.4;
        }

        .btn-custom-register {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 100%;
            min-height: 52px;
            margin-top: 5px;
            padding: 12px 18px;

            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;

            background: linear-gradient(
                135deg,
                var(--primary-brown) 0%,
                #744b20 100%
            );

            border: none;
            border-radius: 12px;

            box-shadow: 0 10px 22px rgba(92, 58, 22, 0.24);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
        }

        .btn-custom-register:hover {
            color: #ffffff;

            background: linear-gradient(
                135deg,
                var(--primary-brown-dark) 0%,
                var(--primary-brown) 100%
            );

            box-shadow: 0 13px 26px rgba(92, 58, 22, 0.32);
            transform: translateY(-1px);
        }

        .btn-custom-register:focus,
        .btn-custom-register:active {
            color: #ffffff !important;
            outline: none !important;
            box-shadow: 0 0 0 4px rgba(92, 58, 22, 0.16) !important;
        }

        .login-wrapper {
            padding-top: 20px;
            margin-top: 20px;

            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.6;
            text-align: center;

            border-top: 1px solid var(--border-color);
        }

        .login-link {
            color: var(--gold-dark);
            font-weight: 700;
            text-decoration: none;

            transition: color 0.2s ease;
        }

        .login-link:hover {
            color: var(--primary-brown);
            text-decoration: none;
        }

        .alert-validation {
            padding: 12px 14px;
            margin-bottom: 20px;

            color: #842029;
            font-size: 0.86rem;

            background: #f8d7da;
            border: 1px solid #f5c2c7;
            border-radius: 10px;
        }

        .alert-validation ul {
            padding-left: 18px;
            margin: 0;
        }

        .alert-validation li + li {
            margin-top: 4px;
        }

        @media (max-width: 767.98px) {
            .register-page {
                padding: 18px 14px;
            }

            .register-card {
                max-height: calc(100vh - 36px);
                max-height: calc(100dvh - 36px);

                padding: 32px 26px 28px;
                border-radius: 20px;
            }

            .brand-logo {
                min-height: 68px;
                margin-bottom: 10px;
            }

            .brand-logo img {
                max-width: 190px;
                max-height: 72px;
            }

            .register-title {
                font-size: 1.65rem;
            }

            .register-subtitle {
                margin-bottom: 22px;
                font-size: 0.88rem;
            }
        }

        @media (max-width: 575.98px) {
            .register-card {
                padding-right: 22px;
                padding-left: 22px;
            }

            .form-control {
                height: 49px;
            }

            .btn-custom-register {
                min-height: 50px;
            }
        }

        @media (max-height: 750px) {
            .register-page {
                padding-top: 14px;
                padding-bottom: 14px;
            }

            .register-card {
                max-height: calc(100vh - 28px);
                max-height: calc(100dvh - 28px);

                padding-top: 28px;
                padding-bottom: 26px;
            }

            .brand-logo {
                min-height: 56px;
                margin-bottom: 6px;
            }

            .brand-logo img {
                max-width: 170px;
                max-height: 58px;
            }

            .register-title {
                font-size: 1.55rem;
            }

            .register-subtitle {
                margin-bottom: 18px;
            }

            .form-group {
                margin-bottom: 13px;
            }

            .login-wrapper {
                padding-top: 16px;
                margin-top: 16px;
            }
        }
    </style>
</head>

<body>
    <main class="register-page">
        <div class="register-decoration register-decoration-one"></div>
        <div class="register-decoration register-decoration-two"></div>

        <section class="register-card">
            <header class="register-header">
                <div class="brand-logo">
                    <img
                        src="{{ asset('img/logo.png') }}"
                        alt="Logo Sawdeera Tour"
                    >
                </div>

                <h1 class="register-title">
                    Formulir Pendaftaran
                </h1>

                <p class="register-subtitle">
                    Buat akun terlebih dahulu. Data pendaftaran dapat dilengkapi
                    setelah akun Anda diaktifkan oleh admin.
                </p>
            </header>

            @if ($errors->any())
                <div class="alert-validation">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ url('/actionregister') }}"
                method="POST"
                autocomplete="off"
            >
                @csrf

                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="name" class="form-label">
                            Nama Lengkap
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap"
                            autocomplete="name"
                            required
                            autofocus
                        >

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group col-12">
                        <label for="email" class="form-label">
                            Alamat Email
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="contoh@email.com"
                            autocomplete="email"
                            required
                        >

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group col-12">
                        <label for="no_telepon" class="form-label">
                            Nomor HP
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="tel"
                            id="no_telepon"
                            name="no_telepon"
                            class="form-control @error('no_telepon') is-invalid @enderror"
                            value="{{ old('no_telepon') }}"
                            placeholder="Contoh: 081234567890"
                            autocomplete="tel"
                            inputmode="tel"
                            required
                        >

                        @error('no_telepon')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="form-helper">
                            Gunakan nomor HP aktif yang dapat dihubungi.
                        </small>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="password" class="form-label">
                            Kata Sandi
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan kata sandi"
                            autocomplete="new-password"
                            required
                        >

                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="password_confirmation" class="form-label">
                            Konfirmasi Kata Sandi
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi kata sandi"
                            autocomplete="new-password"
                            required
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn btn-custom-register"
                >
                    Ajukan Aktivasi Akun
                </button>
            </form>

            <div class="login-wrapper">
                Sudah memiliki akun?

                <a
                    href="{{ url('/login') }}"
                    class="login-link"
                >
                    Masuk sekarang
                </a>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('berhasil'))
        <script>
            Swal.fire({
                title: 'Berhasil',
                text: @json(session('berhasil')),
                icon: 'success',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#5c3a16'
            });
        </script>
    @elseif (session('gagal'))
        <script>
            Swal.fire({
                title: 'Pendaftaran Gagal',
                text: @json(session('gagal')),
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#5c3a16'
            });
        </script>
    @endif
</body>
</html>
