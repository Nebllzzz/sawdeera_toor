<?php

namespace App\Http\Controllers;

use App\Models\Keberangkatan;
use App\Models\KeberangkatanJemaah;
use App\Models\KeberangkatanJemaahReschedule;
use App\Models\Maskapai;
use App\Models\PaketUmrah;
use App\Models\TourLeader;
use App\Services\KeberangkatanStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class KeberangkatanController extends Controller
{
    public function __construct(private KeberangkatanStatusService $statusService) {}

    public function index()
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);

        return view('home.keberangkatan.index');
    }

    public function detail($id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);
        $keberangkatan = $this->baseQuery()->findOrFail($id);
        $actions = $this->statusService->actionsFor($keberangkatan, auth()->user());
        $formData = [
            'pakets' => PaketUmrah::where('is_active', true)->orderBy('nama_paket')->get(),
            'maskapais' => Maskapai::where('is_active', 1)->orderBy('nama')->get(),
            'leaders' => TourLeader::orderBy('nama')->get(),
        ];

        return view('home.keberangkatan.detail', compact('keberangkatan', 'actions', 'formData'));
    }

    public function list(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);

        $query = $this->baseQuery()->orderByDesc('tanggal_keberangkatan');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kode', fn (Keberangkatan $row) => $row->kode_keberangkatan)
            ->addColumn('paket', fn (Keberangkatan $row) => $row->paket?->nama_paket ?? '-')
            ->addColumn('tanggal_berangkat', fn (Keberangkatan $row) => $row->tanggal_keberangkatan?->translatedFormat('d M Y') ?? '-')
            ->addColumn('tanggal_pulang', fn (Keberangkatan $row) => $row->tanggal_pulang?->translatedFormat('d M Y') ?? '-')
            ->addColumn('terisi', fn (Keberangkatan $row) => $row->terisi)
            ->addColumn('status_badge', fn (Keberangkatan $row) => $this->statusBadge($row->status))
            ->addColumn('action', fn (Keberangkatan $row) => '<a href="/keberangkatan/detail/'.$row->id.'" class="btn btn-sm btn-primary"><i class="fas fa-eye mr-1"></i> Lihat Detail</a>')
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function detail_data($id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);

        return response()->json($this->baseQuery()->findOrFail($id));
    }

    public function jemaah(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);
        $request->validate(['keberangkatan_id' => 'required|exists:keberangkatan,id']);

        $data = KeberangkatanJemaah::with([
            'jemaah.user',
            'paketUmrah',
            'keberangkatan',
            'pendingReschedule',
            'reschedules' => fn ($q) => $q->latest(),
        ])->where('keberangkatan_id', $request->keberangkatan_id);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama', fn ($row) => $row->jemaah->user->name ?? '-')
            ->addColumn('paket', fn ($row) => $row->paketUmrah->nama_paket ?? '-')
            ->addColumn('jadwal', fn ($row) => $row->keberangkatan?->kode_keberangkatan.' - '.($row->keberangkatan?->tanggal_keberangkatan?->translatedFormat('d M Y') ?? '-'))
            ->addColumn('status', fn ($row) => '<span class="badge badge-'.($row->status === 'setuju' ? 'success' : ($row->status === 'reschedule' ? 'warning' : 'secondary')).'">'.KeberangkatanJemaah::statusLabel($row->status).'</span>')
            ->addColumn('tanggal_pengajuan', fn ($row) => $row->created_at?->translatedFormat('d M Y H:i') ?? '-')
            ->addColumn('action', function ($row) {
                $html = '<div class="jemaah-action-list">';
                $html .= '<a href="/jemaah/data-verifikasi/'.$row->jemaah->user_id.'" class="btn btn-sm btn-light-primary"><i class="far fa-eye mr-1"></i>Detail</a>';

                $reschedule = $row->pendingReschedule ?: $row->reschedules->first();
                if ($reschedule) {
                    $isPending = $reschedule->status === KeberangkatanJemaahReschedule::STATUS_MENUNGGU;
                    $html .= '<a href="'.action([self::class, 'reviewReschedule'], $reschedule->id).'" class="btn btn-sm '.($isPending ? 'btn-warning' : 'btn-outline-warning').'">';
                    $html .= '<i class="fas fa-random mr-1"></i>'.($isPending ? 'Review Reschedule' : 'Lihat Reschedule').'</a>';
                }

                return $html.'</div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function getFormData()
    {
        abort_unless(auth()->user()->role === 'operator', 403);

        return response()->json([
            'paket' => PaketUmrah::where('is_active', true)->orderBy('nama_paket')->get(['id', 'nama_paket', 'harga']),
            'maskapai' => Maskapai::where('is_active', 1)->orderBy('nama')->get(),
            'leader' => TourLeader::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'operator', 403);

        $data = $this->validatedData($request);
        $data['status'] = Keberangkatan::STATUS_DRAFT;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $keberangkatan = Keberangkatan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal keberangkatan berhasil dibuat sebagai draft.',
            'data' => $keberangkatan,
        ]);
    }

    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->role === 'operator', 403);
        $keberangkatan = Keberangkatan::findOrFail($id);
        abort_unless(in_array($keberangkatan->status, [Keberangkatan::STATUS_DRAFT, Keberangkatan::STATUS_DIREVISI], true), 422, 'Jadwal hanya bisa diedit saat draft atau direvisi.');

        $data = $this->validatedData($request);
        $data['updated_by'] = auth()->id();
        $keberangkatan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal keberangkatan berhasil diperbarui.',
        ]);
    }

    public function delete($id)
    {
        abort_unless(auth()->user()->role === 'operator', 403);
        $keberangkatan = Keberangkatan::withCount('jemaah')->findOrFail($id);
        abort_unless($keberangkatan->status === Keberangkatan::STATUS_DRAFT, 422, 'Hanya jadwal draft yang dapat dihapus.');
        abort_unless($keberangkatan->jemaah_count === 0, 422, 'Jadwal sudah memiliki jemaah dan tidak aman untuk dihapus.');

        $keberangkatan->delete();

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus.']);
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:keberangkatan,id',
            'action' => 'required|string',
            'alasan_revisi' => 'nullable|string|max:2000',
        ]);

        $keberangkatan = Keberangkatan::findOrFail($data['id']);
        $this->statusService->transition($keberangkatan, auth()->user(), $data['action'], $data['alasan_revisi'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Status jadwal berhasil diperbarui.',
            'status' => $keberangkatan->status,
            'label' => Keberangkatan::statusLabel($keberangkatan->status),
        ]);
    }

    public function reviewReschedule($id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $reschedule = $this->rescheduleQuery()->findOrFail($id);

        return view('home.keberangkatan.review-reschedule', compact('reschedule'));
    }

    public function approveReschedule($id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        DB::transaction(function () use ($id) {
            $reschedule = KeberangkatanJemaahReschedule::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_unless($reschedule->status === KeberangkatanJemaahReschedule::STATUS_MENUNGGU, 422, 'Pengajuan sudah diproses.');

            $pengajuan = KeberangkatanJemaah::whereKey($reschedule->keberangkatan_jemaah_id)->lockForUpdate()->firstOrFail();
            $tujuan = Keberangkatan::whereKey($reschedule->keberangkatan_tujuan_id)->withCount('jemaah')->lockForUpdate()->firstOrFail();
            abort_unless(! $tujuan->isFull(), 422, 'Kuota jadwal tujuan sudah penuh.');

            $pengajuan->update([
                'keberangkatan_id' => $reschedule->keberangkatan_tujuan_id,
                'status' => KeberangkatanJemaah::STATUS_PENDAFTARAN,
            ]);
            $reschedule->update([
                'status' => KeberangkatanJemaahReschedule::STATUS_DISETUJUI,
                'diproses_pada' => now(),
                'diproses_oleh' => auth()->id(),
            ]);
        });

        return redirect()->back()->with('berhasil', 'Reschedule disetujui. Jemaah perlu menyetujui jadwal baru.');
    }

    public function rejectReschedule(Request $request, $id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $data = $request->validate(['alasan_tolak_reschedule' => 'required|string|max:2000']);

        DB::transaction(function () use ($id, $data) {
            $reschedule = KeberangkatanJemaahReschedule::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_unless($reschedule->status === KeberangkatanJemaahReschedule::STATUS_MENUNGGU, 422, 'Pengajuan sudah diproses.');

            KeberangkatanJemaah::whereKey($reschedule->keberangkatan_jemaah_id)->update([
                'status' => KeberangkatanJemaah::STATUS_PENDAFTARAN,
            ]);
            $reschedule->update([
                'status' => KeberangkatanJemaahReschedule::STATUS_DITOLAK,
                'alasan_tolak_reschedule' => $data['alasan_tolak_reschedule'],
                'diproses_pada' => now(),
                'diproses_oleh' => auth()->id(),
            ]);
        });

        return redirect()->back()->with('berhasil', 'Reschedule ditolak.');
    }

    private function baseQuery()
    {
        return Keberangkatan::with(['paket.hotelMakkah', 'paket.hotelMadinah', 'maskapaiBerangkat', 'maskapaiPulang', 'leader', 'pembuat', 'pengubah'])
            ->withCount(['jemaah' => fn ($q) => $q->whereIn('status', KeberangkatanJemaah::STATUSES)]);
    }

    private function rescheduleQuery()
    {
        return KeberangkatanJemaahReschedule::with([
            'keberangkatanJemaah.jemaah.user',
            'keberangkatanJemaah.paketUmrah',
            'keberangkatanAsal.paket',
            'keberangkatanTujuan.paket',
            'pemroses',
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'paket_id' => 'required|exists:paket_umrah,id',
            'maskapai_berangkat_id' => 'required|exists:maskapai,id',
            'maskapai_pulang_id' => 'required|exists:maskapai,id',
            'tour_leader_id' => 'nullable|exists:tour_leaders,id',
            'kuota' => 'required|integer|min:1|max:1000',
            'tanggal_keberangkatan' => 'required|date|after_or_equal:today',
            'jam_berangkat' => 'required',
            'jam_tiba' => 'required',
            'tanggal_pulang' => 'required|date|after_or_equal:tanggal_keberangkatan',
            'jam_pulang' => 'required',
            'jam_tiba_pulang' => 'required',
            'keterangan' => 'nullable|string|max:2000',
        ]);
    }

    private function statusBadge(?string $status): string
    {
        return '<span class="badge badge-'.Keberangkatan::statusBadgeClass($status).'">'.Keberangkatan::statusLabel($status).'</span>';
    }
}
