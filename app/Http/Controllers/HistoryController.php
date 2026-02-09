<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\ProgramKerjaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanHutang;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // ✅ QUERY DARI TABEL PROGRAM_KERJAS YANG STATUS NYA 'dicairkan'
        // Karena history hanya untuk yang sudah dicairkan
        $query = ProgramKerja::with(['bidang', 'pencairan.dicairkanOleh', 'histories.dilakukanOleh'])
            ->where('status', 'dicairkan'); // ✅ FILTER HANYA YANG DICAIRKAN

        if ($userRole === 'admin') {
            $query->where('bidang_id', $user->bidang_id);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Filter status - untuk history sebaiknya dihapus atau disesuaikan
        // karena history page hanya show yang sudah dicairkan
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

        $programKerjas = $query->latest()->paginate(15);

        if (in_array($userRole, ['superadmin', 'bendahara', 'sekretaris', 'ketua'])) {
            $bidangs = \App\Models\Bidang::orderBy('nama')->get();
        } else {
            $bidangs = \App\Models\Bidang::where('id', $user->bidang_id)->get();
        }
        
        $tahuns = ProgramKerja::distinct()->pluck('tahun')->sort()->values();

        return view('history.program-kerja', compact('programKerjas', 'bidangs', 'tahuns'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        $programKerja = ProgramKerja::with([
            'bidang', 
            'submittedBy', 
            'reviewedByBendahara', 
            'reviewedByKetua',
            'pencairan.dicairkanOleh',
            'histories.dilakukanOleh'
        ])->findOrFail($id);

        // Check access permission
        if ($userRole === 'admin') {
            if ($programKerja->bidang_id !== $user->bidang_id) {
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access.'
                    ], 403);
                }
                abort(403, 'Unauthorized access.');
            }
        }

        // Check if AJAX request
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $programKerja->id,
                    'nama' => $programKerja->nama,
                    'bidang' => [
                        'id' => $programKerja->bidang->id,
                        'nama' => $programKerja->bidang->nama,
                    ],
                    'anggaran' => $programKerja->anggaran,
                    'tahun' => $programKerja->tahun,
                    'tanggal' => $programKerja->tanggal,
                    'tanggal_formatted' => $programKerja->tanggal ? $programKerja->tanggal->format('d M Y') : null,
                    'jenis_pengeluaran' => $programKerja->jenis_pengeluaran,
                    'status' => $programKerja->status,
                    'created_at_formatted' => $programKerja->created_at->format('d M Y, H:i'),
                    'updated_at_formatted' => $programKerja->updated_at->format('d M Y, H:i'),
                    'submitted_at_formatted' => $programKerja->submitted_at ? $programKerja->submitted_at->format('d M Y, H:i') . ' WIB' : null,
                    'submitted_by_name' => $programKerja->submittedBy ? $programKerja->submittedBy->name : null,
                    'histories_count' => $programKerja->histories->count(),
                    
                    // Timeline histories
                    'histories' => $programKerja->histories->sortByDesc('dilakukan_pada')->values()->map(function($history) {
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
                    
                    // Pencairan info
                    'pencairan' => $programKerja->pencairan ? [
                        'jumlah_dicairkan' => $programKerja->pencairan->jumlah_dicairkan,
                        'tanggal_pencairan' => $programKerja->pencairan->tanggal_pencairan,
                        'tanggal_pencairan_formatted' => $programKerja->pencairan->tanggal_pencairan->format('d M Y, H:i') . ' WIB',
                        'metode_pencairan' => $programKerja->pencairan->metode_pencairan,
                        'metode_pencairan_label' => $this->getMetodePencairanLabel($programKerja->pencairan->metode_pencairan),
                        'nomor_referensi' => $programKerja->pencairan->nomor_referensi,
                        'catatan' => $programKerja->pencairan->catatan,
                        'dicairkan_oleh_name' => $programKerja->pencairan->dicairkanOleh ? $programKerja->pencairan->dicairkanOleh->name : null,
                    ] : null,
                ]
            ]);
        }

        return view('history.detail', compact('programKerja'));
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

    public function hutang(Request $request)
    {
        $perPage = $request->get('perPage', 20);
        
        $baseQuery = PengajuanHutang::with(['user', 'bidang', 'pembayaran.dibayarOleh'])
            ->where('status', 'lunas');
        
        if ($perPage === 'all') {
            $hutangLunas = $baseQuery->latest()->get();
            $hutangLunas = new \Illuminate\Pagination\LengthAwarePaginator(
                $hutangLunas,
                $hutangLunas->count(),
                $hutangLunas->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $hutangLunas = $baseQuery->latest()->paginate($perPage);
        }
        
        $hutangLunas->appends(['perPage' => $perPage]);
        
        return view('history.hutang', compact('hutangLunas', 'perPage'));
    }
}