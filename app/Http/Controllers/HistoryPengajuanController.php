<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // ✅ QUERY DARI TABEL PENGAJUAN_BUDGETS YANG STATUS NYA 'dicairkan'
        $query = PengajuanBudget::with(['bidang', 'pencairan.dicairkanOleh', 'histories.dilakukanOleh'])
            ->where('status', 'dicairkan'); // ✅ FILTER HANYA YANG DICAIRKAN

        if ($userRole === 'admin') {
            $query->where('bidang_id', $user->bidang_id);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Filter status - untuk history sebaiknya disesuaikan
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if (in_array($userRole, ['superadmin', 'bendahara', 'sekretaris', 'ketua'])) {
            if ($request->has('bidang_id') && $request->bidang_id != '') {
                $query->where('bidang_id', $request->bidang_id);
            }
        }

        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun', $request->tahun);
        }

        $pengajuanBudgets = $query->latest()->paginate(15);

        if (in_array($userRole, ['superadmin', 'bendahara', 'sekretaris', 'ketua'])) {
            $bidangs = \App\Models\Bidang::orderBy('nama')->get();
        } else {
            $bidangs = \App\Models\Bidang::where('id', $user->bidang_id)->get();
        }
        
        $tahuns = PengajuanBudget::distinct()->pluck('tahun')->sort()->values();

        return view('history.pengajuan-budget', compact('pengajuanBudgets', 'bidangs', 'tahuns'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        $pengajuanBudget = PengajuanBudget::with([
            'bidang', 
            'submittedBy', 
            'reviewedByBendahara', 
            'reviewedByKetua',
            'pencairan.dicairkanOleh',
            'histories.dilakukanOleh'
        ])->findOrFail($id);

        if ($userRole === 'admin') {
            if ($pengajuanBudget->bidang_id !== $user->bidang_id) {
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access.'
                    ], 403);
                }
                abort(403, 'Unauthorized access.');
            }
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pengajuanBudget->id,
                    'nama' => $pengajuanBudget->nama,
                    'bidang' => [
                        'id' => $pengajuanBudget->bidang->id,
                        'nama' => $pengajuanBudget->bidang->nama,
                    ],
                    'anggaran' => $pengajuanBudget->anggaran,
                    'tahun' => $pengajuanBudget->tahun,
                    'tanggal' => $pengajuanBudget->tanggal,
                    'tanggal_formatted' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('d M Y') : null,
                    'jenis_pengeluaran' => $pengajuanBudget->jenis_pengeluaran,
                    'status' => $pengajuanBudget->status,
                    'created_at_formatted' => $pengajuanBudget->created_at->format('d M Y, H:i'),
                    'updated_at_formatted' => $pengajuanBudget->updated_at->format('d M Y, H:i'),
                    'submitted_at_formatted' => $pengajuanBudget->submitted_at ? $pengajuanBudget->submitted_at->format('d M Y, H:i') . ' WIB' : null,
                    'submitted_by_name' => $pengajuanBudget->submittedBy ? $pengajuanBudget->submittedBy->name : null,
                    'histories_count' => $pengajuanBudget->histories->count(),
                    
                    'histories' => $pengajuanBudget->histories->sortByDesc('dilakukan_pada')->values()->map(function($history) {
                        return [
                            'status_dari' => $history->status_dari,
                            'status_ke' => $history->status_ke,
                            'status_ke_label' => $this->getStatusLabel($history->status_ke),
                            'catatan' => $history->catatan,
                            'dilakukan_pada' => $history->dilakukan_pada,
                            'dilakukan_pada_formatted' => $history->dilakukan_pada->format('d M Y, H:i') . ' WIB',
                            'dilakukan_oleh_name' => $history->dilakukanOleh ? $history->dilakukanOleh->name : null,
                        ];
                    }),
                    
                    'pencairan' => $pengajuanBudget->pencairan ? [
                        'jumlah_dicairkan' => $pengajuanBudget->pencairan->jumlah_dicairkan,
                        'tanggal_pencairan' => $pengajuanBudget->pencairan->tanggal_pencairan,
                        'tanggal_pencairan_formatted' => $pengajuanBudget->pencairan->tanggal_pencairan->format('d M Y, H:i') . ' WIB',
                        'metode_pencairan' => $pengajuanBudget->pencairan->metode_pencairan,
                        'metode_pencairan_label' => $this->getMetodePencairanLabel($pengajuanBudget->pencairan->metode_pencairan),
                        'nomor_referensi' => $pengajuanBudget->pencairan->nomor_referensi,
                        'catatan' => $pengajuanBudget->pencairan->catatan,
                        'dicairkan_oleh_name' => $pengajuanBudget->pencairan->dicairkanOleh ? $pengajuanBudget->pencairan->dicairkanOleh->name : null,
                    ] : null,
                ]
            ]);
        }

        return view('history.pengajuan-detail', compact('pengajuanBudget'));
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'draft' => 'Draft',
            'menunggu_konfirmasi_bendahara' => 'Menunggu Konfirmasi Bendahara',
            'menunggu_approval_ketua' => 'Menunggu Approval Ketua',
            'menunggu_pencairan' => 'Menunggu Pencairan',
            'dicairkan' => 'Dicairkan',
            'ditolak_bendahara' => 'Ditolak Bendahara',
            'ditolak_ketua' => 'Ditolak Ketua',
        ];

        return $labels[$status] ?? $status;
    }

    private function getMetodePencairanLabel($metode)
    {
        $labels = [
            'transfer_bank' => 'Transfer Bank',
            'tunai' => 'Tunai',
            'cek' => 'Cek',
            'giro' => 'Giro',
        ];

        return $labels[$metode] ?? $metode;
    }
}