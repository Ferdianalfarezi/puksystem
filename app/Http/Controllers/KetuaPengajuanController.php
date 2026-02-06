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
        // Load semua relationships yang dibutuhkan
        $pengajuanBudget->load([
            'bidang', 
            'submittedBy', 
            'reviewedByBendahara', 
            'reviewedByKetua',
            'pencairan.dicairkanOleh',
            'programKerja'
        ]);

        // ✅ SELALU RETURN JSON (HAPUS if ajax check)
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
                
                // Bidang
                'bidang' => [
                    'id' => $pengajuanBudget->bidang->id,
                    'nama' => $pengajuanBudget->bidang->nama,
                ],
                'bidang_id' => $pengajuanBudget->bidang_id,
                
                // Info Dasar
                'anggaran' => $pengajuanBudget->anggaran,
                'tahun' => $pengajuanBudget->tahun,
                'tanggal' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('Y-m-d') : null,
                'tanggal_formatted' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('d M Y') : '-',
                'status' => $pengajuanBudget->status,
                'jenis_pengeluaran' => $pengajuanBudget->jenis_pengeluaran,
                
                // Data Aksi
                'no_surat' => $pengajuanBudget->no_surat,
                'jumlah_anggota' => $pengajuanBudget->jumlah_anggota,
                'nama_aksi' => $pengajuanBudget->nama_aksi,
                'tempat_aksi' => $pengajuanBudget->tempat_aksi,
                'jam_aksi' => $pengajuanBudget->jam_aksi,
                
                // Lampiran
                'lampiran' => $pengajuanBudget->lampiran,
                'lampiran_url' => $pengajuanBudget->lampiran ? asset('storage/' . $pengajuanBudget->lampiran) : null,
                'lampiran_filename' => $pengajuanBudget->lampiran ? basename($pengajuanBudget->lampiran) : null,
                
                // Submission
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
                
                // Pencairan
                'pencairan' => $pengajuanBudget->pencairan ? [
                    'jumlah_dicairkan' => $pengajuanBudget->pencairan->jumlah_dicairkan,
                    'tanggal_pencairan_formatted' => $pengajuanBudget->pencairan->tanggal_pencairan 
                        ? $pengajuanBudget->pencairan->tanggal_pencairan->format('d M Y, H:i') . ' WIB' 
                        : '-',
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