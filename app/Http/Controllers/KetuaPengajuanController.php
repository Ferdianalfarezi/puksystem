<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBudget;
use App\Models\PengajuanBudgetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KetuaPengajuanController extends Controller
{
    public function index()
    {
        $pengajuanBudgets = PengajuanBudget::with(['bidang', 'submittedBy', 'reviewedByBendahara'])
            ->where('status', 'menunggu_approval_ketua')
            ->latest('reviewed_at_bendahara')
            ->paginate(15);

        return view('ketua.pengajuan.index', compact('pengajuanBudgets'));
    }

    public function show(PengajuanBudget $pengajuanBudget)
    {
        $pengajuanBudget->load([
            'bidang', 
            'submittedBy', 
            'reviewedByBendahara', 
            'reviewedByKetua'
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pengajuanBudget->id,
                    'nama' => $pengajuanBudget->nama,
                    'anggaran' => $pengajuanBudget->anggaran,
                    'tahun' => $pengajuanBudget->tahun,
                    'tanggal' => $pengajuanBudget->tanggal,
                    'tanggal_formatted' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('d M Y') : '-',
                    'status' => $pengajuanBudget->status,
                    'jenis_pengeluaran' => $pengajuanBudget->jenis_pengeluaran,
                    
                    'bidang' => [
                        'id' => $pengajuanBudget->bidang->id,
                        'nama' => $pengajuanBudget->bidang->nama,
                    ],
                    
                    'submitted_at_formatted' => $pengajuanBudget->submitted_at 
                        ? $pengajuanBudget->submitted_at->format('d M Y, H:i') . ' WIB' 
                        : null,
                    'submitted_by_name' => $pengajuanBudget->submittedBy?->name,
                    
                    'reviewed_at_bendahara_formatted' => $pengajuanBudget->reviewed_at_bendahara 
                        ? $pengajuanBudget->reviewed_at_bendahara->format('d M Y, H:i') . ' WIB' 
                        : null,
                    'reviewed_by_bendahara_name' => $pengajuanBudget->reviewedByBendahara?->name,
                    'catatan_bendahara' => $pengajuanBudget->catatan_bendahara,
                    
                    'created_at_formatted' => $pengajuanBudget->created_at->format('d M Y, H:i') . ' WIB',
                ]
            ]);
        }

        return view('ketua.pengajuan.detail', compact('pengajuanBudget'));
    }

    public function approve(Request $request, PengajuanBudget $pengajuanBudget)
    {
        if (!$pengajuanBudget->isWaitingKetua()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan budget tidak dalam status menunggu approval ketua.'
            ]);
        }

        DB::beginTransaction();
        try {
            $pengajuanBudget->update([
                'status' => 'menunggu_pencairan',
                'reviewed_by_ketua' => Auth::id(),
                'reviewed_at_ketua' => now(),
                'catatan_ketua' => $request->catatan,
            ]);

            PengajuanBudgetHistory::create([
                'pengajuan_budget_id' => $pengajuanBudget->id,
                'tanggal_pengajuan' => $pengajuanBudget->tanggal,
                'status_dari' => 'menunggu_approval_ketua',
                'status_ke' => 'menunggu_pencairan',
                'catatan' => $request->catatan ?? 'Disetujui oleh ketua',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $pengajuanBudget->nama,
                    'anggaran' => $pengajuanBudget->anggaran,
                    'bidang' => $pengajuanBudget->bidang->nama,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan budget berhasil di-approve dan siap untuk dicairkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, PengajuanBudget $pengajuanBudget)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000'
        ]);

        if (!$pengajuanBudget->isWaitingKetua()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan budget tidak dalam status menunggu approval ketua.'
            ]);
        }

        DB::beginTransaction();
        try {
            $pengajuanBudget->update([
                'status' => 'ditolak_ketua',
                'reviewed_by_ketua' => Auth::id(),
                'reviewed_at_ketua' => now(),
                'catatan_ketua' => $request->catatan,
            ]);

            PengajuanBudgetHistory::create([
                'pengajuan_budget_id' => $pengajuanBudget->id,
                'tanggal_pengajuan' => $pengajuanBudget->tanggal,
                'status_dari' => 'menunggu_approval_ketua',
                'status_ke' => 'ditolak_ketua',
                'catatan' => $request->catatan,
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $pengajuanBudget->nama,
                    'anggaran' => $pengajuanBudget->anggaran,
                    'bidang' => $pengajuanBudget->bidang->nama,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan budget berhasil ditolak.'
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