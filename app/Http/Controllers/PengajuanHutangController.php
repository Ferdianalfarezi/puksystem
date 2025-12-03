<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHutang;
use App\Models\PengajuanHutangHistory;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanHutangController extends Controller
{
   public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Check authorization
        if (!in_array($userRole, ['superadmin']) && !($userRole === 'admin' && $user->bidang_id == 4)) {
            abort(403, 'Unauthorized access.');
        }
        
        $perPage = $request->get('perPage', 20);
        
        $baseQuery = PengajuanHutang::with(['user', 'bidang', 'submittedBy']);
        
        $allPengajuanHutang = $baseQuery->get();
        
        // Handle pagination
        if ($perPage === 'all') {
            $pengajuanHutang = $baseQuery->latest()->get();
            $pengajuanHutang = new \Illuminate\Pagination\LengthAwarePaginator(
                $pengajuanHutang,
                $pengajuanHutang->count(),
                $pengajuanHutang->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $pengajuanHutang = $baseQuery->latest()->paginate($perPage);
        }
        
        $pengajuanHutang->appends(['perPage' => $perPage]);
        
        return view('pengajuan-hutang.index', compact(
            'pengajuanHutang',
            'allPengajuanHutang',
            'perPage'
        ));
    }

    public function create()
    {
        // Return users untuk dropdown
        $users = User::where('status', 'active')
            ->with('bidang')
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'success' => true,
            'users' => $users->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'bidang_id' => $u->bidang_id,
                'bidang_nama' => $u->bidang->nama ?? '-'
            ])
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Check authorization
        if (!in_array($userRole, ['superadmin']) && !($userRole === 'admin' && $user->bidang_id == 4)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'jumlah' => 'required|numeric|min:1',
            'keperluan' => 'required|string|max:1000',
            'tahun' => 'required|digits:4|integer|min:2000|max:2100',
            'tanggal' => 'required|date',
        ], [
            'user_id.required' => 'Peminjam harus dipilih.',
            'user_id.exists' => 'Peminjam tidak valid.',
            'jumlah.required' => 'Jumlah hutang wajib diisi.',
            'jumlah.min' => 'Jumlah hutang minimal Rp 1.',
            'keperluan.required' => 'Keperluan wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            // Get user data
            $peminjam = User::with('bidang')->findOrFail($validated['user_id']);
            
            $validated['bidang_id'] = $peminjam->bidang_id;
            $validated['nama'] = $peminjam->name;
            $validated['status'] = 'draft';
            $validated['sisa_hutang'] = $validated['jumlah'];

            $pengajuanHutang = PengajuanHutang::create($validated);

            PengajuanHutangHistory::create([
                'pengajuan_hutang_id' => $pengajuanHutang->id,
                'status_dari' => null,
                'status_ke' => 'draft',
                'catatan' => 'Pengajuan hutang dibuat',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $pengajuanHutang->nama,
                    'jumlah' => $pengajuanHutang->jumlah,
                    'bidang' => $pengajuanHutang->bidang->nama,
                    'keperluan' => $pengajuanHutang->keperluan,
                    'tahun' => $pengajuanHutang->tahun,
                    'tanggal' => $pengajuanHutang->tanggal->format('Y-m-d'),
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan hutang berhasil dibuat dengan status draft.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(PengajuanHutang $pengajuanHutang)
{
    // Load relationships
    $pengajuanHutang->load([
        'user',
        'bidang',
        'submittedBy',
        'reviewedByBendahara',
        'reviewedByKetua',
        'pembayaran.dibayarOleh',
        'pembayaran.historyKas'
    ]);

    // Format data untuk JSON response
    $data = [
        'id' => $pengajuanHutang->id,
        'nama' => $pengajuanHutang->nama,
        'jumlah' => $pengajuanHutang->jumlah,
        'sisa_hutang' => $pengajuanHutang->sisa_hutang,
        'keperluan' => $pengajuanHutang->keperluan,
        'tanggal' => $pengajuanHutang->tanggal,
        'tanggal_formatted' => $pengajuanHutang->tanggal ? $pengajuanHutang->tanggal->format('d M Y') : '-',
        'tahun' => $pengajuanHutang->tahun,
        'status' => $pengajuanHutang->status,
        'persen_lunas' => $pengajuanHutang->persen_lunas,
        'total_terbayar' => $pengajuanHutang->total_terbayar,
        
        // User & Bidang
        'user' => [
            'id' => $pengajuanHutang->user->id,
            'name' => $pengajuanHutang->user->name,
        ],
        'bidang' => [
            'id' => $pengajuanHutang->bidang->id,
            'nama' => $pengajuanHutang->bidang->nama,
        ],
        
        // Submission
        'submitted_by_name' => $pengajuanHutang->submittedBy ? $pengajuanHutang->submittedBy->name : '-',
        'submitted_at_formatted' => $pengajuanHutang->submitted_at ? $pengajuanHutang->submitted_at->format('d M Y, H:i') . ' WIB' : '-',
        
        // Bendahara Review
        'reviewed_by_bendahara_name' => $pengajuanHutang->reviewedByBendahara ? $pengajuanHutang->reviewedByBendahara->name : null,
        'reviewed_at_bendahara_formatted' => $pengajuanHutang->reviewed_at_bendahara ? $pengajuanHutang->reviewed_at_bendahara->format('d M Y, H:i') . ' WIB' : null,
        'catatan_bendahara' => $pengajuanHutang->catatan_bendahara,
        
        // Ketua Review
        'reviewed_by_ketua_name' => $pengajuanHutang->reviewedByKetua ? $pengajuanHutang->reviewedByKetua->name : null,
        'reviewed_at_ketua_formatted' => $pengajuanHutang->reviewed_at_ketua ? $pengajuanHutang->reviewed_at_ketua->format('d M Y, H:i') . ' WIB' : null,
        'catatan_ketua' => $pengajuanHutang->catatan_ketua,
        
        // Pembayaran
        'pembayaran' => $pengajuanHutang->pembayaran->map(function($p) {
            return [
                'id' => $p->id,
                'jumlah_bayar' => $p->jumlah_bayar,
                'tanggal_bayar' => $p->tanggal_bayar ? $p->tanggal_bayar->format('d M Y') : '-',
                'metode_pembayaran' => $p->metode_pembayaran_label,
                'nomor_referensi' => $p->nomor_referensi,
                'catatan' => $p->catatan,
                'dibayar_oleh_name' => $p->dibayarOleh ? $p->dibayarOleh->name : '-',
            ];
        }),
        
        // Timestamps
        'created_at_formatted' => $pengajuanHutang->created_at->format('d M Y, H:i') . ' WIB',
        'updated_at_formatted' => $pengajuanHutang->updated_at->format('d M Y, H:i') . ' WIB',
    ];

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

    public function edit(PengajuanHutang $pengajuanHutang)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin']) && !($userRole === 'admin' && $user->bidang_id == 4)) {
            abort(403, 'Unauthorized access.');
        }

        if (!$pengajuanHutang->isDraft()) {
            return redirect()->route('pengajuan-hutang.index')
                ->with('error', 'Hanya pengajuan hutang dengan status draft yang bisa diedit.');
        }

        $users = User::where('status', 'active')
            ->with('bidang')
            ->orderBy('name')
            ->get();

        return view('pengajuan-hutang.edit', compact('pengajuanHutang', 'users'));
    }

    public function update(Request $request, PengajuanHutang $pengajuanHutang)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin']) && !($userRole === 'admin' && $user->bidang_id == 4)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        if (!$pengajuanHutang->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan hutang dengan status draft yang bisa diedit.'
            ]);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'jumlah' => 'required|numeric|min:1',
            'keperluan' => 'required|string|max:1000',
            'tahun' => 'required|digits:4|integer|min:2000|max:2100',
            'tanggal' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $dataLama = [
                'nama' => $pengajuanHutang->nama,
                'jumlah' => $pengajuanHutang->jumlah,
                'bidang' => $pengajuanHutang->bidang->nama,
                'keperluan' => $pengajuanHutang->keperluan,
                'tahun' => $pengajuanHutang->tahun,
                'tanggal' => $pengajuanHutang->tanggal->format('Y-m-d'),
            ];

            // Get user data
            $peminjam = User::with('bidang')->findOrFail($validated['user_id']);
            
            $validated['bidang_id'] = $peminjam->bidang_id;
            $validated['nama'] = $peminjam->name;
            $validated['sisa_hutang'] = $validated['jumlah'];

            $pengajuanHutang->update($validated);
            $pengajuanHutang->refresh();

            PengajuanHutangHistory::create([
                'pengajuan_hutang_id' => $pengajuanHutang->id,
                'status_dari' => 'draft',
                'status_ke' => 'draft',
                'catatan' => 'Pengajuan hutang diupdate',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'data_lama' => $dataLama,
                    'data_baru' => [
                        'nama' => $pengajuanHutang->nama,
                        'jumlah' => $pengajuanHutang->jumlah,
                        'bidang' => $pengajuanHutang->bidang->nama,
                        'keperluan' => $pengajuanHutang->keperluan,
                        'tahun' => $pengajuanHutang->tahun,
                        'tanggal' => $pengajuanHutang->tanggal->format('Y-m-d'),
                    ],
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan hutang berhasil diupdate.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(PengajuanHutang $pengajuanHutang)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin']) && !($userRole === 'admin' && $user->bidang_id == 4)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        if (!$pengajuanHutang->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan hutang dengan status draft yang bisa dihapus.'
            ]);
        }

        DB::beginTransaction();
        try {
            $dataHutang = [
                'nama' => $pengajuanHutang->nama,
                'jumlah' => $pengajuanHutang->jumlah,
                'bidang' => $pengajuanHutang->bidang->nama,
                'keperluan' => $pengajuanHutang->keperluan,
                'tahun' => $pengajuanHutang->tahun,
                'tanggal' => $pengajuanHutang->tanggal->format('Y-m-d'),
            ];

            $pengajuanHutangId = $pengajuanHutang->id;

            PengajuanHutangHistory::create([
                'pengajuan_hutang_id' => $pengajuanHutangId,
                'status_dari' => 'draft',
                'status_ke' => 'deleted',
                'catatan' => 'Pengajuan hutang dihapus',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => $dataHutang,
            ]);

            $pengajuanHutang->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan hutang berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submit(PengajuanHutang $pengajuanHutang)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin']) && !($userRole === 'admin' && $user->bidang_id == 4)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        if (!$pengajuanHutang->canBeSubmitted()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan hutang ini tidak bisa diajukan.'
            ]);
        }

        DB::beginTransaction();
        try {
            $statusLama = $pengajuanHutang->status;

            $pengajuanHutang->update([
                'status' => 'menunggu_konfirmasi_bendahara',
                'submitted_at' => now(),
                'submitted_by' => Auth::id(),
            ]);

            PengajuanHutangHistory::create([
                'pengajuan_hutang_id' => $pengajuanHutang->id,
                'status_dari' => $statusLama,
                'status_ke' => 'menunggu_konfirmasi_bendahara',
                'catatan' => 'Pengajuan hutang diajukan untuk dikonfirmasi bendahara',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $pengajuanHutang->nama,
                    'jumlah' => $pengajuanHutang->jumlah,
                    'bidang' => $pengajuanHutang->bidang->nama,
                    'keperluan' => $pengajuanHutang->keperluan,
                    'tahun' => $pengajuanHutang->tahun,
                    'tanggal' => $pengajuanHutang->tanggal->format('Y-m-d'),
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan hutang berhasil diajukan ke bendahara.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // List Hutang Aktif (yang sudah dicairkan)
    public function listHutangAktif(Request $request)
    {
        $perPage = $request->get('perPage', 20);
        
        $baseQuery = PengajuanHutang::with(['user', 'bidang', 'pembayaran'])
            ->where('status', 'dicairkan')
            ->where('sisa_hutang', '>', 0);
        
        if ($perPage === 'all') {
            $hutangAktif = $baseQuery->latest()->get();
            $hutangAktif = new \Illuminate\Pagination\LengthAwarePaginator(
                $hutangAktif,
                $hutangAktif->count(),
                $hutangAktif->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $hutangAktif = $baseQuery->latest()->paginate($perPage);
        }
        
        $hutangAktif->appends(['perPage' => $perPage]);
        
        return view('pengajuan-hutang.list-hutang', compact('hutangAktif', 'perPage'));
    }
}