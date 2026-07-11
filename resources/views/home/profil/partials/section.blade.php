<h5 class="section-heading">{{ $title }}</h5>

@if($section === 'personal')

<div class="row">

    <div class="form-group col-md-6">
        <label class="required">Nama Lengkap</label>
        <input name="name" class="form-control bg-light" value="{{ $user->name }}" readonly>
    </div>

    <div class="form-group col-md-6">
        <label class="required">NIK</label>
        <input name="nik" class="form-control" value="{{ old('nik', $j->nik ?? '') }}" required>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Tempat Lahir</label>
        <input name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $j->tempat_lahir ?? '') }}" required>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $j?->tanggal_lahir?->format('Y-m-d') ?? '') }}" required>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-control" required>
            <option value="">Pilih</option>
            <option value="laki_laki" @selected(old('jenis_kelamin', $j->jenis_kelamin ?? '') === 'laki_laki')>
                Laki-laki
            </option>
            <option value="perempuan" @selected(old('jenis_kelamin', $j->jenis_kelamin ?? '') === 'perempuan')>
                Perempuan
            </option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Status Pernikahan</label>
        <select name="status_pernikahan" class="form-control" required>
            <option value="">Pilih</option>
            <option value="menikah" @selected(old('status_pernikahan', $j->status_pernikahan ?? '') === 'menikah')>
                Menikah
            </option>
            <option value="belum_menikah" @selected(old('status_pernikahan', $j->status_pernikahan ?? '') === 'belum_menikah')>
                Belum Menikah
            </option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Pekerjaan</label>
        <input name="pekerjaan" class="form-control" value="{{ old('pekerjaan', $j->pekerjaan ?? '') }}" required>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Alamat Lengkap</label>
        <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', $j->alamat ?? '') }}</textarea>
    </div>

</div>

@elseif($section === 'contact')

<div class="row">

    <div class="form-group col-md-6">
        <label class="required">Nomor HP</label>
        <input name="no_telepon" class="form-control bg-light" value="{{ $j->no_telepon ?? '' }}" readonly>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Email</label>
        <input type="email" name="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Kontak Darurat</label>
        <input name="kontak_darurat" class="form-control" value="{{ old('kontak_darurat', $j->kontak_darurat ?? '') }}" required>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Hubungan dengan Kontak Darurat</label>
        <input name="hubungan_kontak_darurat" class="form-control" value="{{ old('hubungan_kontak_darurat', $j->hubungan_kontak_darurat ?? '') }}" required>
    </div>

</div>

@elseif($section === 'passport')

<div class="row">

    <div class="form-group col-md-6">
        <label class="required">Nomor Paspor</label>
        <input name="nomor_paspor" class="form-control" value="{{ old('nomor_paspor', $j->nomor_paspor ?? '') }}" required>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Tempat Penerbitan</label>
        <input name="tempat_penerbitan_paspor" class="form-control" value="{{ old('tempat_penerbitan_paspor', $j->tempat_penerbitan_paspor ?? '') }}" required>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Tanggal Terbit</label>
        <input type="date" name="tanggal_terbit_paspor" class="form-control" value="{{ old('tanggal_terbit_paspor', $j?->tanggal_terbit_paspor?->format('Y-m-d') ?? '') }}" required>
    </div>

    <div class="form-group col-md-6">
        <label class="required">Tanggal Kedaluwarsa</label>
        <input type="date" name="tanggal_kedaluwarsa_paspor" class="form-control" value="{{ old('tanggal_kedaluwarsa_paspor', $j?->tanggal_kedaluwarsa_paspor?->format('Y-m-d') ?? '') }}" required>
    </div>

</div>

@elseif($section === 'health')

<div class="row">

    <div class="form-group col-md-4">
        <label class="required">Golongan Darah</label>
        <select name="golongan_darah" class="form-control" required>
            <option value="">Pilih</option>
            @foreach(['A', 'B', 'AB', 'O'] as $blood)
                <option value="{{ $blood }}" @selected(old('golongan_darah', $j->golongan_darah ?? '') === $blood)>
                    {{ $blood }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-4">
        <label>Riwayat Penyakit</label>
        <textarea name="riwayat_penyakit" class="form-control" rows="3" placeholder="Tulis Tidak ada bila kosong">{{ old('riwayat_penyakit', $j->riwayat_penyakit ?? '') }}</textarea>
    </div>

    <div class="form-group col-md-4">
        <label>Alergi</label>
        <textarea name="alergi" class="form-control" rows="3" placeholder="Tulis Tidak ada bila kosong">{{ old('alergi', $j->alergi ?? '') }}</textarea>
    </div>

</div>

@endif
