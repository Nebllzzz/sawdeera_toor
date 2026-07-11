<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Sawdeera Tour</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #FDF5E6;
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .card-register {
            width: 100%;
            max-width: 560px;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .btn-custom {
            background-color: #5C3A16;
            color: #fff;
            border-radius: 10px;
            font-weight: bold;
            padding: 12px;
        }

        .btn-custom:hover {
            background-color: #462c11;
        }

        .logo {
            width: 180px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
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

    <div class="register-container">
        <div class="card card-register py-5">

            <!-- HEADER -->
            <div class="mb-4 text-center">
                <img src="{{ asset('img/logo.png') }}" class="logo">
                <h4 class="mt-2">Formulir Pendaftaran</h4>
            </div>

            <form action="/actionregister" method="POST">
                @csrf

                <p class="text-muted text-center">Buat akun terlebih dahulu. Data pendaftaran dapat dilengkapi setelah akun diaktifkan admin.</p>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group col-12">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group col-12">
                        <label>Nomor HP</label>
                        <input type="tel" name="no_telepon" class="form-control" value="{{ old('no_telepon') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-custom btn-block mt-3">Ajukan Aktivasi Akun</button>

            </form>

            <div class="text-center mt-2 font-weight-light register-text">
                Sudah Memiliki Akun? <a href="/login" class="register-link">Login Sekarang Disini</a>
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
