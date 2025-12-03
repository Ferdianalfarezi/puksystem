<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHutang;
use App\Models\PengajuanHutangHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BendaharaHutangController extends Controller
{
    public function index()
    {
        $pengajuanHutang = PengajuanHutang::with(['user', 'bidang', 'submittedBy'])
            ->where('status', 'menunggu_konfirmasi_bendahara')
            ->latest('submitted_at')
            ->paginate(15);

        return view('bendahara.hutang.index', compact('pengajuanHutang'));
    }

    public function show(PengajuanHutang $pengajuanHutang)
    {
        $pengajuanHutang->load(['user', 'bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua']);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pengajuanHutang->id,
                    'nama' => $pengajuanHutang->nama,
                    'user' => [
                        'id' => $pengajuanHutang->user->id,
                        'name' => $pengajuanHutang->user->name,
                    ],
                    'bidang' => [
                        'nama' => $pengajuanHutang->bidang->nama
                    ],
                    'jumlah' => $pengajuanHutang->jumlah,
                    'keperluan' => $pengajuanHutang->keperluan,
                    'tahun' => $pengajuanHutang->tahun,
                    'tanggal' => $pengajuanHutang->tanggal ? $pengajuanHutang->tanggal->format('Y-m-d') : null,
                    'tanggal_formatted' => $pengajuanHutang->tanggal ? $pengajuanHutang->tanggal->format('d M Y') : '-',
                    'submitted_by_name' => $pengajuanHutang->submittedBy->name ?? null,
                    'submitted_at' => $pengajuanHutang->submitted_at ? $pengajuanHutang->submitted_at->toISOString() : null,
                    'submitted_at_formatted' => $pengajuanHutang->submitted_at 
                        ? $pengajuanHutang->submitted_at->format('d M Y, H:i') . ' WIB' 
                        : null,
                ]
            ]);
        }

        return view('bendahara.hutang.detail', compact('pengajuanHutang'));
    }

    public function approve(Request $request, PengajuanHutang $pengajuanHutang)
    {
        if ($pengajuanHutang->status !== 'menunggu_konfirmasi_bendahara') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan hutang ini tidak dapat disetujui.'
            ]);
        }

        $validated = $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $pengajuanHutang->status;

            $pengajuanHutang->update([
                'status' => 'menunggu_approval_ketua',
                'reviewed_by_bendahara' => Auth::id(),
                'reviewed_at_bendahara' => now(),
                'catatan_bendahara' => $validated['catatan'] ?? null,
            ]);

            PengajuanHutangHistory::create([
                'pengajuan_hutang_id' => $pengajuanHutang->id,
                'status_dari' => $statusLama,
                'status_ke' => 'menunggu_approval_ketua',
                'catatan' => $validated['catatan'] ?? 'Dikonfirmasi oleh bendahara',
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
                'message' => 'Pengajuan hutang berhasil disetujui dan diteruskan ke ketua.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, PengajuanHutang $pengajuanHutang)
    {
        if ($pengajuanHutang->status !== 'menunggu_konfirmasi_bendahara') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan hutang ini tidak dapat ditolak.'
            ]);
        }

        $validated = $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $pengajuanHutang->status;

            $pengajuanHutang->update([
                'status' => 'ditolak_bendahara',
                'reviewed_by_bendahara' => Auth::id(),
                'reviewed_at_bendahara' => now(),
                'catatan_bendahara' => $validated['catatan'],
            ]);

            PengajuanHutangHistory::create([
                'pengajuan_hutang_id' => $pengajuanHutang->id,
                'status_dari' => $statusLama,
                'status_ke' => 'ditolak_bendahara',
                'catatan' => $validated['catatan'],
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
                'message' => 'Pengajuan hutang berhasil ditolak.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}