<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    >

    <title>Sawdeera Toor - Login</title>

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

        .login-page {
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

        .login-decoration {
            position: absolute;
            z-index: -1;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(2px);
        }

        .login-decoration-one {
            top: -140px;
            right: -100px;
            width: 360px;
            height: 360px;
            background: rgba(200, 155, 44, 0.07);
        }

        .login-decoration-two {
            bottom: -180px;
            left: -130px;
            width: 420px;
            height: 420px;
            background: rgba(133, 84, 34, 0.12);
        }

        .login-card {
            position: relative;
            z-index: 2;

            width: 100%;
            max-width: 460px;
            max-height: calc(100vh - 48px);
            max-height: calc(100dvh - 48px);

            padding: 38px 42px 34px;
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

        .login-card::-webkit-scrollbar {
            width: 5px;
        }

        .login-card::-webkit-scrollbar-track {
            background: transparent;
        }

        .login-card::-webkit-scrollbar-thumb {
            background: rgba(92, 58, 22, 0.25);
            border-radius: 10px;
        }

        .login-card::before {
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

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;

            min-height: 80px;
            margin-bottom: 14px;
        }

        .brand-logo img {
            display: block;

            width: 100%;
            max-width: 220px;
            max-height: 90px;

            object-fit: contain;
        }

        .login-title {
            margin: 0 0 8px;

            color: var(--text-dark);
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
            text-align: center;
        }

        .login-subtitle {
            margin: 0 0 26px;

            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            text-align: center;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;

            color: var(--text-dark);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            height: 52px;
            padding: 13px 16px;

            color: var(--text-dark);
            font-size: 0.95rem;

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

        .auth-link {
            color: var(--primary-brown);
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;

            transition: color 0.2s ease;
        }

        .auth-link:hover {
            color: var(--gold-dark);
            text-decoration: none;
        }

        .btn-custom-login {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 100%;
            min-height: 52px;
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

        .btn-custom-login:hover {
            color: #ffffff;

            background: linear-gradient(
                135deg,
                var(--primary-brown-dark) 0%,
                var(--primary-brown) 100%
            );

            box-shadow: 0 13px 26px rgba(92, 58, 22, 0.32);
            transform: translateY(-1px);
        }

        .btn-custom-login:focus,
        .btn-custom-login:active {
            color: #ffffff !important;
            outline: none !important;
            box-shadow: 0 0 0 4px rgba(92, 58, 22, 0.16) !important;
        }

        .register-wrapper {
            padding-top: 20px;
            margin-top: 20px;

            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.6;
            text-align: center;

            border-top: 1px solid var(--border-color);
        }

        .register-link {
            color: var(--gold-dark);
            font-weight: 700;
            text-decoration: none;
        }

        .register-link:hover {
            color: var(--primary-brown);
            text-decoration: none;
        }

        .alert-validation {
            padding: 12px 14px;
            margin-bottom: 20px;

            color: #842029;
            font-size: 0.88rem;
            text-align: left;

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

        @media (max-width: 575.98px) {
            .login-page {
                padding: 16px 14px;
            }

            .login-card {
                max-height: calc(100vh - 32px);
                max-height: calc(100dvh - 32px);

                padding: 30px 24px 26px;
                border-radius: 20px;
            }

            .brand-logo {
                min-height: 68px;
                margin-bottom: 12px;
            }

            .brand-logo img {
                max-width: 190px;
                max-height: 75px;
            }

            .login-title {
                font-size: 1.7rem;
            }

            .login-subtitle {
                margin-bottom: 22px;
                font-size: 0.9rem;
            }

            .form-control,
            .btn-custom-login {
                min-height: 50px;
            }
        }

        @media (max-height: 700px) {
            .login-page {
                padding-top: 14px;
                padding-bottom: 14px;
            }

            .login-card {
                max-height: calc(100vh - 28px);
                max-height: calc(100dvh - 28px);
                padding-top: 28px;
                padding-bottom: 26px;
            }

            .brand-logo {
                min-height: 60px;
                margin-bottom: 8px;
            }

            .brand-logo img {
                max-width: 180px;
                max-height: 65px;
            }

            .login-title {
                font-size: 1.65rem;
            }

            .login-subtitle {
                margin-bottom: 18px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            .register-wrapper {
                padding-top: 16px;
                margin-top: 16px;
            }
        }
    </style>
</head>

<body>
    <main class="login-page">
        <div class="login-decoration login-decoration-one"></div>
        <div class="login-decoration login-decoration-two"></div>

        <section class="login-card">
            <div class="brand-logo">
                <img
                    src="{{ asset('img/logo.png') }}"
                    alt="Logo Sawdeera Toor"
                >
            </div>

            <h1 class="login-title">
                Selamat Datang
            </h1>

            <p class="login-subtitle">
                Silakan masuk menggunakan akun Sawdeera Toor Anda.
            </p>

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
                action="{{ url('/actionlogin') }}"
                method="POST"
                autocomplete="off"
            >
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">
                        Alamat Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan alamat email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        Kata Sandi
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan kata sandi"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-custom-login">
                    Masuk
                </button>

                <div class="text-center mt-3">
                    <a href="#" class="auth-link" id="forgotPassword">Lupa Password?</a>
                </div>

                <div class="register-wrapper">
                    Belum memiliki akun?

                    <a
                        href="{{ url('/register') }}"
                        class="register-link"
                    >
                        Daftar sekarang
                    </a>
                </div>
            </form>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('forgotPassword').addEventListener('click', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Lupa Password?',
                text: 'Hubungi admin untuk reset password',
                icon: 'info',
                confirmButtonColor: '#5c3a16'
            });
        });
    </script>

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
                title: 'Login Gagal',
                text: @json(session('gagal')),
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#5c3a16'
            });
        </script>
    @endif
</body>
</html>
