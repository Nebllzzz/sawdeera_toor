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
            max-width: 800px;
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

                <!-- DATA AKUN -->
                <div class="section-title">Data Akun</div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" value="{{ old('password') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}" required>
                    </div>
                </div>

                <!-- DATA JEMAAH -->
                <div class="section-title">Data Jemaah</div>
                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Nomor Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="">Pilih</option>
                            <option value="laki_laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Status Pernikahan</label>
                        <select name="status_pernikahan" class="form-control" required>
                            <option value="">Pilih</option>
                            <option value="menikah">Menikah</option>
                            <option value="belum_menikah">Belum Menikah</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}" required>
                    </div>

                </div>

                <button type="submit" class="btn btn-custom btn-block mt-3">Daftar</button>

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
