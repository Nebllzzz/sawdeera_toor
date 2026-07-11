<div class="d-flex flex-wrap justify-content-center" style="gap:5px">
    <button class="btn btn-warning btn-sm editJemaah" data-id="{{ $row->id }}" title="Edit"><i class="fas fa-edit text-white"></i></button>
    <button class="btn btn-info btn-sm detailJemaah" data-id="{{ $row->id }}" title="Detail"><i class="fas fa-eye text-white"></i></button>
    <button class="btn btn-success btn-sm toggleStatus" data-id="{{ $row->id }}" title="Ubah status akun"><i class="fas fa-user-check"></i></button>
    <button class="btn btn-primary btn-sm toggleData" data-id="{{ $row->id }}" title="Verifikasi data"><i class="fas fa-clipboard-check"></i></button>
    <button class="btn btn-danger btn-sm deleteJemaah" data-id="{{ $row->id }}" title="Hapus"><i class="fas fa-trash"></i></button>
</div>
