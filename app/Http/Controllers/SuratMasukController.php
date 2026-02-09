<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratMasukController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Hanya sekretaris yang bisa akses
        if (!in_array($userRole, ['sekretaris', 'superadmin'])) {
            abort(403, 'Unauthorized access.');
        }
        
        // Query dari tabel pengajuan_budgets - semua data yang ada lampiran
        $query = PengajuanBudget::with(['bidang', 'pencairan.dicairkanOleh', 'histories.dilakukanOleh'])
            ->whereNotNull('lampiran'); // Hanya yang ada lampiran
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        
        // Filter Bidang
        if ($request->has('bidang_id') && $request->bidang_id != '') {
            $query->where('bidang_id', $request->bidang_id);
        }
        
        // Filter Tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun', $request->tahun);
        }
        
        // Filter Jenis Pengeluaran
        if ($request->has('jenis_pengeluaran') && $request->jenis_pengeluaran != '') {
            $query->where('jenis_pengeluaran', $request->jenis_pengeluaran);
        }
        
        // Filter Status (opsional)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $perPage = $request->get('perPage', 20);
        
        if ($perPage === 'all') {
            $allData = $query->latest('updated_at')->get();
            $suratMasuk = new \Illuminate\Pagination\LengthAwarePaginator(
                $allData,
                $allData->count(),
                $allData->count(),
                1
            );
        } else {
            $suratMasuk = $query->latest('updated_at')->paginate($perPage);
        }
        
        $bidangs = \App\Models\Bidang::orderBy('nama')->get();
        $tahuns = PengajuanBudget::distinct()->pluck('tahun')->sort()->values();
        
        return view('sekretaris.surat-masuk.index', compact('suratMasuk', 'bidangs', 'tahuns', 'perPage'));
    }
    
    public function show($id)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['sekretaris', 'superadmin'])) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
            abort(403, 'Unauthorized access.');
        }
        
        $pengajuanBudget = PengajuanBudget::with([
            'bidang', 
            'submittedBy', 
            'reviewedByBendahara', 
            'reviewedByKetua',
            'pencairan.dicairkanOleh',
            'histories.dilakukanOleh',
            'programKerja'
        ])->findOrFail($id);
        
        // Validasi harus ada lampiran
        if (!$pengajuanBudget->lampiran) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak memiliki lampiran.'
                ], 404);
            }
            abort(404, 'Data tidak memiliki lampiran.');
        }
        
        // ✅ ALWAYS return JSON for AJAX requests
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
                'jenis' => $pengajuanBudget->jenis,
                'jenis_label' => $pengajuanBudget->jenis_label,
                'status' => $pengajuanBudget->status,
                'status_label' => $this->getStatusLabel($pengajuanBudget->status),
                
                // Program Kerja (jika ada)
                'program_kerja' => $pengajuanBudget->programKerja ? [
                    'id' => $pengajuanBudget->programKerja->id,
                    'nama' => $pengajuanBudget->programKerja->nama,
                ] : null,
                
                // Detail Aksi
                'no_surat' => $pengajuanBudget->no_surat,
                'jumlah_anggota' => $pengajuanBudget->jumlah_anggota,
                'nama_aksi' => $pengajuanBudget->nama_aksi,
                'tempat_aksi' => $pengajuanBudget->tempat_aksi,
                'jam_aksi' => $pengajuanBudget->jam_aksi,
                
                // Lampiran
                'lampiran_url' => $pengajuanBudget->lampiran ? asset('storage/' . $pengajuanBudget->lampiran) : null,
                'lampiran_filename' => $pengajuanBudget->lampiran ? basename($pengajuanBudget->lampiran) : null,
                
                'created_at_formatted' => $pengajuanBudget->created_at->format('d M Y, H:i'),
                'updated_at_formatted' => $pengajuanBudget->updated_at->format('d M Y, H:i'),
                'submitted_at_formatted' => $pengajuanBudget->submitted_at ? $pengajuanBudget->submitted_at->format('d M Y, H:i') . ' WIB' : null,
                'submitted_by_name' => $pengajuanBudget->submittedBy ? $pengajuanBudget->submittedBy->name : null,
                
                'reviewed_at_bendahara_formatted' => $pengajuanBudget->reviewed_at_bendahara ? $pengajuanBudget->reviewed_at_bendahara->format('d M Y, H:i') . ' WIB' : null,
                'reviewed_by_bendahara_name' => $pengajuanBudget->reviewedByBendahara ? $pengajuanBudget->reviewedByBendahara->name : null,
                'catatan_bendahara' => $pengajuanBudget->catatan_bendahara,
                
                'reviewed_at_ketua_formatted' => $pengajuanBudget->reviewed_at_ketua ? $pengajuanBudget->reviewed_at_ketua->format('d M Y, H:i') . ' WIB' : null,
                'reviewed_by_ketua_name' => $pengajuanBudget->reviewedByKetua ? $pengajuanBudget->reviewedByKetua->name : null,
                'catatan_ketua' => $pengajuanBudget->catatan_ketua,
                
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