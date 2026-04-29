<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sawdeera Toor - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        /* Mengubah latar belakang halaman */
        .content-wrapper {
            background: #FDF5E6 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }

        .auth-form-light {
            background: transparent !important;
            border: none !important;
            width: 100%;
            max-width: 400px;
        }

        /* Styling Judul Login */
        h2.login-title {
            color: #5D4037;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        /* Styling Input Field */
        .form-control {
            width: 100%;
            border-radius: 10px !important;
            border: none !important;
            padding: 15px 20px !important; /* samakan dengan desain awal */
            height: auto !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        /* Tombol Login Cokelat Tua */
        .btn-custom-login {
            background-color: #5C3A16 !important;
            border: none !important;
            color: white !important;
            font-weight: bold;
            padding: 12px !important;
            border-radius: 10px !important;
            width: 100%;
            font-size: 1.1rem;
            margin-top: 10px;
        }

        .btn-custom-login:hover {
            background-color: #462c11 !important;
        }

        /* Link Lupa Password & Daftar */
        .auth-link {
            color: #5C3A16 !important;
            font-weight: 300;
            text-decoration: none;
        }

        .register-text {
            color: #8D6E63;
            font-weight: 600;
        }

        .register-link {
            color: #D4AF37; /* Warna emas/kuning untuk pendaftaran */
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper auth px-0">
                <div class="d-flex justify-content-center w-100">
                    <div class="text-center">
                        <div class="auth-form-light py-5 px-4 px-sm-3">

                            <div class="brand-logo mb-4">
                                <img src="{{ asset('img/logo.png') }}" alt="logo" style="width: 250px;">
                            </div>

                            <h2 class="login-title">Login</h2>

                            <form class="pt-3" action="/actionlogin" method="POST" autocomplete="off">
                                @csrf

                                <input type="email" name="email" class="form-control" placeholder="Masukan Email" required>

                                <input type="password" name="password" class="form-control" placeholder="Masukan Kata Sandi" required>

                                <div class="text-right mb-2">
                                    <a href="#" class="auth-link small">Lupa Kata Sandi?</a>
                                </div>

                                <button type="submit" class="btn btn-custom-login">Login</button>

                                <div class="text-center mt-2 font-weight-light register-text">
                                    Belum Memiliki Akun? <a href="/register" class="register-link">Daftar Sekarang Disini</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('berhasil'))
    <script>
        Swal.fire({
        title: "Good job!",
        text: "{{session('berhasil')}}",
        icon: "success"
        });
    </script>
    @elseif (session('gagal'))
    <script>
        Swal.fire({
        title: "Error!",
        text: "{{session('gagal')}}",
        icon: "error"
        });
    </script>
    @endif

</body>
</html>
