<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\ProgramKerjaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KetuaController extends Controller
{
    public function index()
    {
        $programKerjas = ProgramKerja::with(['bidang', 'submittedBy', 'reviewedByBendahara'])
            ->where('status', 'menunggu_approval_ketua')
            ->latest('reviewed_at_bendahara')
            ->paginate(10);

        return view('ketua.index', compact('programKerjas'));
    }

    public function show(ProgramKerja $programKerja)
    {
        $programKerja->load(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua']);

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
                    'jenis_pengeluaran' => $programKerja->jenis_pengeluaran,
                    'submitted_by_name' => $programKerja->submittedBy ? $programKerja->submittedBy->name : null,
                    'submitted_at' => $programKerja->submitted_at,
                    'reviewed_by_bendahara_name' => $programKerja->reviewedByBendahara ? $programKerja->reviewedByBendahara->name : null,
                    'reviewed_at_bendahara' => $programKerja->reviewed_at_bendahara,
                    'catatan_bendahara' => $programKerja->catatan_bendahara,
                ]
            ]);
        }

        // Return view untuk non-AJAX request
        return view('ketua.detail', compact('programKerja'));
    }

    public function approve(Request $request, ProgramKerja $programKerja)
    {
        if ($programKerja->status !== 'menunggu_approval_ketua') {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak dapat disetujui.'
            ]);
        }

        $validated = $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $programKerja->status;

            $programKerja->update([
                'status' => 'menunggu_pencairan',
                'reviewed_by_ketua' => Auth::id(),
                'reviewed_at_ketua' => now(),
                'catatan_ketua' => $validated['catatan'] ?? null,
            ]);

            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => $statusLama,
                'status_ke' => 'menunggu_pencairan',
                'catatan' => $validated['catatan'] ?? 'Disetujui oleh ketua, menunggu pencairan dana',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $programKerja->nama,
                    'anggaran' => $programKerja->anggaran,
                    'bidang' => $programKerja->bidang->nama,
                    'tahun' => $programKerja->tahun,
                    'tanggal' => $programKerja->tanggal ? $programKerja->tanggal->format('Y-m-d') : null,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Program kerja berhasil disetujui dan menunggu pencairan dana.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, ProgramKerja $programKerja)
    {
        if ($programKerja->status !== 'menunggu_approval_ketua') {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak dapat ditolak.'
            ]);
        }

        $validated = $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $programKerja->status;

            $programKerja->update([
                'status' => 'ditolak_ketua',
                'reviewed_by_ketua' => Auth::id(),
                'reviewed_at_ketua' => now(),
                'catatan_ketua' => $validated['catatan'],
            ]);

            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => $statusLama,
                'status_ke' => 'ditolak_ketua',
                'catatan' => $validated['catatan'],
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $programKerja->nama,
                    'anggaran' => $programKerja->anggaran,
                    'bidang' => $programKerja->bidang->nama,
                    'tahun' => $programKerja->tahun,
                    'tanggal' => $programKerja->tanggal ? $programKerja->tanggal->format('Y-m-d') : null,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Program kerja berhasil ditolak.'
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