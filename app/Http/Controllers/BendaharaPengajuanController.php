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
        // ✅ Load semua relationships yang dibutuhkan
        $pengajuanBudget->load([
            'bidang', 
            'submittedBy', 
            'reviewedByBendahara', 
            'reviewedByKetua',
            'pencairan.dicairkanOleh',
            'programKerja'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pengajuanBudget->id,
                'nama' => $pengajuanBudget->nama,
                'jenis' => $pengajuanBudget->jenis,
                'jenis_label' => $pengajuanBudget->jenis_label,
                
                // Program Kerja (kalau jenis = program_kerja)
                'program_kerja_id' => $pengajuanBudget->program_kerja_id,
                'program_kerja' => $pengajuanBudget->programKerja ? [
                    'id' => $pengajuanBudget->programKerja->id,
                    'nama' => $pengajuanBudget->programKerja->nama,
                ] : null,
                
                // Bidang info
                'bidang' => [
                    'id' => $pengajuanBudget->bidang->id,
                    'nama' => $pengajuanBudget->bidang->nama,
                ],
                'bidang_id' => $pengajuanBudget->bidang_id,
                
                // Anggaran & Info Dasar
                'anggaran' => $pengajuanBudget->anggaran,
                'anggaran_formatted' => 'Rp ' . number_format($pengajuanBudget->anggaran, 0, ',', '.'),
                'tahun' => $pengajuanBudget->tahun,
                'tanggal' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('Y-m-d') : null,
                'tanggal_formatted' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('d M Y') : '-',
                
                // Status
                'status' => $pengajuanBudget->status,
                'jenis_pengeluaran' => $pengajuanBudget->jenis_pengeluaran,
                
                // ✅ Data Aksi (kalau jenis pengeluaran = Aksi)
                'no_surat' => $pengajuanBudget->no_surat,
                'jumlah_anggota' => $pengajuanBudget->jumlah_anggota,
                'nama_aksi' => $pengajuanBudget->nama_aksi,
                'tempat_aksi' => $pengajuanBudget->tempat_aksi,
                'jam_aksi' => $pengajuanBudget->jam_aksi,
                
                // ✅ Lampiran PDF
                'lampiran' => $pengajuanBudget->lampiran,
                'lampiran_url' => $pengajuanBudget->lampiran ? asset('storage/' . $pengajuanBudget->lampiran) : null,
                'lampiran_filename' => $pengajuanBudget->lampiran ? basename($pengajuanBudget->lampiran) : null,
                
                // Submission info
                'submitted_at' => $pengajuanBudget->submitted_at,
                'submitted_at_formatted' => $pengajuanBudget->submitted_at 
                    ? $pengajuanBudget->submitted_at->format('d M Y, H:i') . ' WIB' 
                    : null,
                'submitted_by_name' => $pengajuanBudget->submittedBy?->name,
                
                // Review Bendahara
                'reviewed_at_bendahara' => $pengajuanBudget->reviewed_at_bendahara,
                'reviewed_at_bendahara_formatted' => $pengajuanBudget->reviewed_at_bendahara 
                    ? $pengajuanBudget->reviewed_at_bendahara->format('d M Y, H:i') . ' WIB' 
                    : null,
                'reviewed_by_bendahara_name' => $pengajuanBudget->reviewedByBendahara?->name,
                'catatan_bendahara' => $pengajuanBudget->catatan_bendahara,
                
                // Review Ketua
                'reviewed_at_ketua' => $pengajuanBudget->reviewed_at_ketua,
                'reviewed_at_ketua_formatted' => $pengajuanBudget->reviewed_at_ketua 
                    ? $pengajuanBudget->reviewed_at_ketua->format('d M Y, H:i') . ' WIB' 
                    : null,
                'reviewed_by_ketua_name' => $pengajuanBudget->reviewedByKetua?->name,
                'catatan_ketua' => $pengajuanBudget->catatan_ketua,
                
                // Timestamps
                'created_at_formatted' => $pengajuanBudget->created_at->format('d M Y, H:i') . ' WIB',
                'updated_at_formatted' => $pengajuanBudget->updated_at->format('d M Y, H:i') . ' WIB',
                
                // Pencairan (kalau sudah dicairkan)
                'pencairan' => $pengajuanBudget->pencairan ? [
                    'jumlah_dicairkan' => $pengajuanBudget->pencairan->jumlah_dicairkan,
                    'tanggal_pencairan' => $pengajuanBudget->pencairan->tanggal_pencairan,
                    'tanggal_pencairan_formatted' => $pengajuanBudget->pencairan->tanggal_pencairan 
                        ? $pengajuanBudget->pencairan->tanggal_pencairan->format('d M Y, H:i') . ' WIB' 
                        : '-',
                    'metode_pencairan' => $pengajuanBudget->pencairan->metode_pencairan,
                    'metode_pencairan_label' => $pengajuanBudget->pencairan->metode_pencairan_label ?? ucfirst(str_replace('_', ' ', $pengajuanBudget->pencairan->metode_pencairan)),
                    'nomor_referensi' => $pengajuanBudget->pencairan->nomor_referensi,
                    'dicairkan_oleh_name' => $pengajuanBudget->pencairan->dicairkanOleh?->name,
                    'catatan' => $pengajuanBudget->pencairan->catatan,
                ] : null,
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
                'data_snapshot' => [
                    'nama' => $pengajuanBudget->nama,
                    'anggaran' => $pengajuanBudget->anggaran,
                    'bidang' => $pengajuanBudget->bidang->nama,
                    'tahun' => $pengajuanBudget->tahun,
                ],
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
                'data_snapshot' => [
                    'nama' => $pengajuanBudget->nama,
                    'anggaran' => $pengajuanBudget->anggaran,
                    'bidang' => $pengajuanBudget->bidang->nama,
                    'tahun' => $pengajuanBudget->tahun,
                ],
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