<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BendaharaPengajuanController extends Controller
{
    public function index()
    {
        $pengajuanBudgets = PengajuanBudget::with(['bidang', 'submittedBy'])
            ->where('status', 'menunggu_konfirmasi_bendahara')
            ->orderBy('submitted_at', 'desc')
            ->paginate(15);

        // Statistics
        $totalMenunggu = $pengajuanBudgets->total();
        $totalAnggaran = PengajuanBudget::where('status', 'menunggu_konfirmasi_bendahara')
            ->sum('anggaran');
        $bidangTerlibat = PengajuanBudget::where('status', 'menunggu_konfirmasi_bendahara')
            ->distinct('bidang_id')
            ->count('bidang_id');

        return view('bendahara.pengajuan.index', compact(
            'pengajuanBudgets',
            'totalMenunggu',
            'totalAnggaran',
            'bidangTerlibat'
        ));
    }

    public function show(PengajuanBudget $pengajuanBudget)
    {
        $pengajuanBudget->load(['bidang', 'submittedBy']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pengajuanBudget->id,
                'nama' => $pengajuanBudget->nama,
                'bidang' => $pengajuanBudget->bidang->nama,
                'anggaran' => $pengajuanBudget->anggaran,
                'anggaran_formatted' => 'Rp ' . number_format($pengajuanBudget->anggaran, 0, ',', '.'),
                'tahun' => $pengajuanBudget->tahun,
                'jenis_pengeluaran' => $pengajuanBudget->jenis_pengeluaran,
                'tanggal' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('d M Y') : '-',
                'submitted_at' => $pengajuanBudget->submitted_at ? $pengajuanBudget->submitted_at->format('d M Y H:i') : '-',
                'submitted_by' => $pengajuanBudget->submittedBy->name ?? '-',
            ]
        ]);
    }

    public function approve(Request $request, PengajuanBudget $pengajuanBudget)
    {
        if ($pengajuanBudget->status !== 'menunggu_konfirmasi_bendahara') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan budget ini tidak dapat disetujui'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $pengajuanBudget->status;

            $pengajuanBudget->update([
                'status' => 'menunggu_approval_ketua',
                'reviewed_by_bendahara' => Auth::id(),
                'reviewed_at_bendahara' => now(),
                'catatan_bendahara' => $request->catatan,
            ]);

            // Log to history
            $pengajuanBudget->histories()->create([
                'tanggal_pengajuan' => $pengajuanBudget->tanggal ?? now(),
                'status_dari' => $oldStatus,
                'status_ke' => 'menunggu_approval_ketua',
                'catatan' => $request->catatan ?? 'Disetujui oleh Bendahara',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan budget berhasil disetujui dan diteruskan ke Ketua'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui pengajuan budget: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, PengajuanBudget $pengajuanBudget)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Catatan penolakan wajib diisi',
            'catatan.max' => 'Catatan maksimal 1000 karakter',
        ]);

        if ($pengajuanBudget->status !== 'menunggu_konfirmasi_bendahara') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan budget ini tidak dapat ditolak'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $pengajuanBudget->status;

            $pengajuanBudget->update([
                'status' => 'ditolak_bendahara',
                'reviewed_by_bendahara' => Auth::id(),
                'reviewed_at_bendahara' => now(),
                'catatan_bendahara' => $request->catatan,
            ]);

            // Log to history
            $pengajuanBudget->histories()->create([
                'tanggal_pengajuan' => $pengajuanBudget->tanggal ?? now(),
                'status_dari' => $oldStatus,
                'status_ke' => 'ditolak_bendahara',
                'catatan' => $request->catatan,
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan budget berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pengajuan budget: ' . $e->getMessage()
            ], 500);
        }
    }
}