<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHutang;
use App\Models\PengajuanHutangHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KetuaHutangController extends Controller
{
    public function index()
    {
        $pengajuanHutang = PengajuanHutang::with(['user', 'bidang', 'submittedBy', 'reviewedByBendahara'])
            ->where('status', 'menunggu_approval_ketua')
            ->latest('reviewed_at_bendahara')
            ->paginate(15);

        return view('ketua.hutang.index', compact('pengajuanHutang'));
    }

    public function show(PengajuanHutang $pengajuanHutang)
    {
        $pengajuanHutang->load(['user', 'bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua']);

        if (request()->wantsJson() || request()->ajax()) {
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
                        'id' => $pengajuanHutang->bidang->id,
                        'nama' => $pengajuanHutang->bidang->nama,
                    ],
                    'jumlah' => $pengajuanHutang->jumlah,
                    'keperluan' => $pengajuanHutang->keperluan,
                    'tahun' => $pengajuanHutang->tahun,
                    'tanggal' => $pengajuanHutang->tanggal,
                    'tanggal_formatted' => $pengajuanHutang->tanggal->format('d M Y'),
                    'submitted_by_name' => $pengajuanHutang->submittedBy ? $pengajuanHutang->submittedBy->name : null,
                    'submitted_at' => $pengajuanHutang->submitted_at,
                    'submitted_at_formatted' => $pengajuanHutang->submitted_at 
                        ? $pengajuanHutang->submitted_at->format('d M Y, H:i') . ' WIB' 
                        : null,
                    'reviewed_by_bendahara_name' => $pengajuanHutang->reviewedByBendahara ? $pengajuanHutang->reviewedByBendahara->name : null,
                    'reviewed_at_bendahara' => $pengajuanHutang->reviewed_at_bendahara,
                    'reviewed_at_bendahara_formatted' => $pengajuanHutang->reviewed_at_bendahara 
                        ? $pengajuanHutang->reviewed_at_bendahara->format('d M Y, H:i') . ' WIB' 
                        : null,
                    'catatan_bendahara' => $pengajuanHutang->catatan_bendahara,
                ]
            ]);
        }

        return view('ketua.hutang.detail', compact('pengajuanHutang'));
    }

    public function approve(Request $request, PengajuanHutang $pengajuanHutang)
    {
        if ($pengajuanHutang->status !== 'menunggu_approval_ketua') {
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
                'status' => 'menunggu_pencairan',
                'reviewed_by_ketua' => Auth::id(),
                'reviewed_at_ketua' => now(),
                'catatan_ketua' => $validated['catatan'] ?? null,
            ]);

            PengajuanHutangHistory::create([
                'pengajuan_hutang_id' => $pengajuanHutang->id,
                'status_dari' => $statusLama,
                'status_ke' => 'menunggu_pencairan',
                'catatan' => $validated['catatan'] ?? 'Disetujui oleh ketua, menunggu pencairan dana',
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
                'message' => 'Pengajuan hutang berhasil disetujui dan menunggu pencairan dana.'
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
        if ($pengajuanHutang->status !== 'menunggu_approval_ketua') {
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
                'status' => 'ditolak_ketua',
                'reviewed_by_ketua' => Auth::id(),
                'reviewed_at_ketua' => now(),
                'catatan_ketua' => $validated['catatan'],
            ]);

            PengajuanHutangHistory::create([
                'pengajuan_hutang_id' => $pengajuanHutang->id,
                'status_dari' => $statusLama,
                'status_ke' => 'ditolak_ketua',
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