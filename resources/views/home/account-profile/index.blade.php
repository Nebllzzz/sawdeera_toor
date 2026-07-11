@extends('layouts.main')
@section('title', 'Profile')

@section('content')
@php
    $user = auth()->user();
    $jemaah = $user->jemaah;

    $initial = collect(explode(' ', $user->name))
        ->filter()
        ->take(2)
        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
        ->implode('');
@endphp

<style>
    .profile-page {
        background: linear-gradient(135deg, #fbf8f2 0%, #fff 45%, #fff7e8 100%);
        min-height: calc(100vh - 57px);
    }

    .profile-header-card {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        background: linear-gradient(135deg, #fff 0%, #fff8ec 100%);
        box-shadow: 0 16px 40px rgba(31, 41, 55, .08);
    }

    .profile-cover {
        height: 145px;
        background:
            radial-gradient(circle at 20% 20%, rgba(255, 193, 7, .35), transparent 35%),
            radial-gradient(circle at 80% 30%, rgba(231, 67, 100, .18), transparent 30%),
            linear-gradient( #F3E7D2 20%, #E6C27A 80%);
        position: relative;
    }

    .profile-avatar-wrap {
        margin-top: -58px;
        position: relative;
        z-index: 2;
    }

    .profile-avatar,
    .profile-avatar-placeholder {
        width: 112px;
        height: 112px;
        border-radius: 50%;
        border: 5px solid #fff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .12);
        object-fit: cover;
    }

    .profile-avatar-placeholder {
        background: linear-gradient(135deg, #fff0f2, #ffe5c4);
        color: #e74364;
        font-size: 34px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-info-card {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 16px 40px rgba(31, 41, 55, .08);
    }

    .profile-section-title {
        font-size: 15px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .profile-section-title i {
        color: #f59e0b;
    }

    .profile-field label {
        font-size: 13px;
        font-weight: 700;
        color: #6b7280;
        margin-bottom: 7px;
    }

    .profile-field .form-control {
        border-radius: 13px;
        border: 1px solid #e5e7eb;
        padding: 11px 14px;
        min-height: 46px;
        background: #fff;
        transition: all .2s ease;
    }

    .profile-field .form-control:disabled,
    .profile-field .form-control[readonly] {
        background: #f9fafb;
        color: #374151;
        cursor: not-allowed;
    }

    .profile-field .form-control:not(:disabled):focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 .2rem rgba(245, 158, 11, .15);
    }

    .profile-action-bar {
        background: #f9fafb;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        padding: 14px;
    }

    .profile-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #fff7e8;
        color: #b45309;
        font-size: 13px;
        font-weight: 700;
    }

    .photo-upload-btn {
        border-radius: 999px;
        padding: 9px 14px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(0, 0, 0, .08);
    }

    .btn-modern-warning {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        border: 0;
        color: #111827;
        font-weight: 800;
        border-radius: 12px;
        padding: 10px 18px;
    }

    .btn-modern-warning:hover {
        color: #111827;
        filter: brightness(.98);
    }

    .btn-modern-secondary {
        border-radius: 12px;
        font-weight: 700;
        padding: 10px 18px;
    }

    .password-box {
        display: none;
    }

    .is-editing .password-box {
        display: block;
    }

    .is-editing .view-hint {
        display: none;
    }

    @media (max-width: 767.98px) {
        .profile-cover {
            height: 120px;
        }

        .profile-avatar,
        .profile-avatar-placeholder {
            width: 96px;
            height: 96px;
        }

        .profile-avatar-wrap {
            margin-top: -50px;
        }
    }
</style>

<div class="content-wrapper profile-page">
    <section class="content py-4">
        <div class="container-fluid">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h2 class="font-weight-bold mb-1">Profile</h2>
                    <small class="text-muted">Dashboard &nbsp;›&nbsp; Profile</small>
                </div>

                <a href="/dashboard" class="btn btn-outline-secondary btn-modern-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>

            @if(session('berhasil'))
                <div class="alert alert-success border-0 shadow-sm" style="border-radius:14px">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('berhasil') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm" style="border-radius:14px">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div id="profileEditWrapper" class="row">
                    {{-- LEFT PROFILE SUMMARY --}}
                    <div class="col-lg-4 mb-4">
                        <div class="card profile-header-card h-100">
                            <div class="profile-cover"></div>

                            <div class="card-body text-center px-4 pb-4">
                                <div class="profile-avatar-wrap mb-3">
                                    @if($jemaah?->foto_profil)
                                        <img
                                            id="profileAvatarPreview"
                                            src="{{ asset('storage/'.$jemaah->foto_profil) }}"
                                            class="profile-avatar"
                                            alt="Foto profil"
                                        >
                                    @else
                                        <div id="profileAvatarInitial" class="profile-avatar-placeholder mx-auto">
                                            {{ $initial ?: 'U' }}
                                        </div>

                                        <img
                                            id="profileAvatarPreview"
                                            src=""
                                            class="profile-avatar d-none"
                                            alt="Foto profil"
                                        >
                                    @endif
                                </div>

                                <h4 class="font-weight-bold mb-1">{{ $user->name }}</h4>
                                <p class="text-muted mb-3">{{ $user->email }}</p>

                                <div class="profile-badge mb-4">
                                    <i class="fas fa-user-shield"></i>
                                    {{ ucfirst($user->role) }}
                                </div>

                                @if($user->role === 'jemaah')
                                    <div class="mb-3">
                                        <label id="photoUploadLabel" class="btn btn-light photo-upload-btn mb-0 disabled">
                                            <i class="fas fa-camera mr-2"></i>
                                            Ganti Foto
                                            <input
                                                id="fotoProfilInput"
                                                type="file"
                                                name="foto_profil"
                                                accept=".jpg,.jpeg,.png"
                                                class="d-none profile-input"
                                                disabled
                                            >
                                        </label>

                                        <small class="d-block text-muted mt-2">
                                            Format JPG, JPEG, atau PNG.
                                        </small>
                                    </div>
                                @endif

                                <div class="view-hint text-muted small">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Klik tombol edit untuk mengubah data profil.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT FORM --}}
                    <div class="col-lg-8 mb-4">
                        <div class="card profile-info-card">
                            <div class="card-body p-4">

                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                                    <div>
                                        <h4 class="font-weight-bold mb-1">Informasi Akun</h4>
                                        <p class="text-muted mb-0">Data profil utama yang terhubung dengan akun Anda.</p>
                                    </div>

                                    <button type="button" id="btnEditProfile" class="btn btn-outline-warning btn-modern-secondary mt-3 mt-md-0">
                                        <i class="fas fa-pen mr-2"></i>
                                        Edit Profile
                                    </button>
                                </div>

                                <div class="profile-section-title">
                                    <i class="fas fa-id-card"></i>
                                    Data Personal
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group profile-field">
                                            <label>Nama Lengkap</label>
                                            <input
                                                name="name"
                                                class="form-control profile-input"
                                                value="{{ old('name', $user->name) }}"
                                                required
                                                disabled
                                            >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group profile-field">
                                            <label>Email</label>
                                            <input
                                                class="form-control"
                                                value="{{ $user->email }}"
                                                readonly
                                            >
                                        </div>
                                    </div>

                                    @if($user->role === 'jemaah')
                                        <div class="col-md-6">
                                            <div class="form-group profile-field">
                                                <label>No. Telepon</label>
                                                <input
                                                    name="no_telepon"
                                                    class="form-control profile-input"
                                                    value="{{ old('no_telepon', $jemaah?->no_telepon) }}"
                                                    placeholder="Masukkan no. telepon"
                                                    disabled
                                                >
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-6">
                                        <div class="form-group profile-field">
                                            <label>Role Akun</label>
                                            <input
                                                class="form-control"
                                                value="{{ ucfirst($user->role) }}"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="password-box">
                                    <div class="profile-section-title">
                                        <i class="fas fa-lock"></i>
                                        Keamanan Akun
                                    </div>

                                    <p class="text-muted mb-3">
                                        Isi password baru hanya jika Anda ingin mengganti password.
                                    </p>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group profile-field">
                                                <label>Password Baru</label>
                                                <input
                                                    type="password"
                                                    name="password"
                                                    class="form-control profile-input"
                                                    placeholder="Masukkan password baru"
                                                    disabled
                                                >
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group profile-field">
                                                <label>Konfirmasi Password</label>
                                                <input
                                                    type="password"
                                                    name="password_confirmation"
                                                    class="form-control profile-input"
                                                    placeholder="Ulangi password baru"
                                                    disabled
                                                >
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                </div>

                                <div class="profile-action-bar d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                    <div class="text-muted small mb-3 mb-md-0">
                                        <i class="fas fa-shield-alt mr-1"></i>
                                        Pastikan data yang Anda simpan sudah benar.
                                    </div>

                                    <div class="d-flex">
                                        <button type="button" id="btnCancelEdit" class="btn btn-light btn-modern-secondary mr-2 d-none">
                                            Batal
                                        </button>

                                        <button type="submit" id="btnSaveProfile" class="btn btn-modern-warning d-none">
                                            <i class="fas fa-save mr-2"></i>
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.getElementById('profileEditWrapper');
        const btnEdit = document.getElementById('btnEditProfile');
        const btnCancel = document.getElementById('btnCancelEdit');
        const btnSave = document.getElementById('btnSaveProfile');
        const inputs = document.querySelectorAll('.profile-input');
        const photoLabel = document.getElementById('photoUploadLabel');
        const fotoInput = document.getElementById('fotoProfilInput');
        const avatarPreview = document.getElementById('profileAvatarPreview');
        const avatarInitial = document.getElementById('profileAvatarInitial');

        function setEditMode(isEdit) {
            wrapper.classList.toggle('is-editing', isEdit);

            inputs.forEach(function (input) {
                input.disabled = !isEdit;
            });

            btnEdit.classList.toggle('d-none', isEdit);
            btnCancel.classList.toggle('d-none', !isEdit);
            btnSave.classList.toggle('d-none', !isEdit);

            if (photoLabel) {
                photoLabel.classList.toggle('disabled', !isEdit);
                photoLabel.classList.toggle('btn-light', !isEdit);
                photoLabel.classList.toggle('btn-outline-warning', isEdit);
            }
        }

        btnEdit.addEventListener('click', function () {
            setEditMode(true);
        });

        btnCancel.addEventListener('click', function () {
            window.location.reload();
        });

        if (fotoInput) {
            fotoInput.addEventListener('change', function (event) {
                const file = event.target.files[0];

                if (!file) {
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (e) {
                    if (avatarPreview) {
                        avatarPreview.src = e.target.result;
                        avatarPreview.classList.remove('d-none');
                    }

                    if (avatarInitial) {
                        avatarInitial.classList.add('d-none');
                    }
                };

                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endsection
