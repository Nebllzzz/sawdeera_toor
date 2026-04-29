@extends('layouts.main')
@section('title', 'Profile')

@section('content')

<div class="content-wrapper" style="background:#E8C999; min-height:100vh; padding:30px;">
    <div class="d-flex justify-content-center">

        <div class="card p-4" style="width:100%; max-width:600px; border-radius:15px;">

            @php
                $name = auth()->user()->name;
                $words = explode(' ', $name);
                $initial = strtoupper(substr($words[0],0,1) . substr($words[1] ?? '',0,1));
                $j = auth()->user()->jemaah;
            @endphp

            <!-- Avatar -->
            <div class="text-center mb-3">
                <div style="
                    width:80px;
                    height:80px;
                    border-radius:50%;
                    background:#5C3A16;
                    color:white;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:28px;
                    font-weight:bold;
                    margin:auto;
                ">
                    {{ $initial }}
                </div>

                <h5 class="mt-3">Data diri & Informasi akun anda</h5>
            </div>

            <!-- FORM VIEW -->
            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Nama Lengkap</label>
                    <input class="form-control" value="{{ $name }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input class="form-control" value="{{ auth()->user()->email }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>NIK</label>
                    <input class="form-control" value="{{ $j->nik ?? '-' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Nomor Telepon</label>
                    <input class="form-control" value="{{ $j->no_telepon ?? '-' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Tempat Lahir</label>
                    <input class="form-control" value="{{ $j->tempat_lahir ?? '-' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Tanggal Lahir</label>
                    <input class="form-control" value="{{ $j->tanggal_lahir ?? '-' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Jenis Kelamin</label>
                    <input class="form-control" value="{{ $j->jenis_kelamin ?? '-' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Status Pernikahan</label>
                    <input class="form-control" value="{{ $j->status_pernikahan ?? '-' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Pekerjaan</label>
                    <input class="form-control" value="{{ $j->pekerjaan ?? '-' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Alamat</label>
                    <textarea class="form-control" readonly>{{ $j->alamat ?? '-' }}</textarea>
                </div>

            </div>

            <!-- BUTTON -->
            <button class="btn btn-dark w-100" data-toggle="modal" data-target="#modalEdit">
                Edit Profil
            </button>

        </div>
    </div>
</div>

{{-- ================= MODAL EDIT ================= --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST" action="/profile/update">
                @csrf

                <div class="modal-header">
                    <h5>Edit Profil</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ $name }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>NIK</label>
                            <input type="text" name="nik" class="form-control" value="{{ $j->nik ?? '' }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>No Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" value="{{ $j->no_telepon ?? '' }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ $j->tempat_lahir ?? '' }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ $j->tanggal_lahir ?? '' }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="laki_laki" {{ $j->jenis_kelamin == 'laki_laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ $j->jenis_kelamin == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status Pernikahan</label>
                            <select name="status_pernikahan" class="form-control">
                                <option value="menikah" {{ $j->status_pernikahan == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="belum_menikah" {{ $j->status_pernikahan == 'belum_menikah' ? 'selected' : '' }}>Belum</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" value="{{ $j->pekerjaan ?? '' }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control">{{ $j->alamat ?? '' }}</textarea>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-dark w-100">Simpan Perubahan</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection
