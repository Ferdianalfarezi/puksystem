<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\ProgramKerjaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BendaharaController extends Controller
{
    public function index()
    {
        $programKerjas = ProgramKerja::with(['bidang', 'submittedBy'])
            ->where('status', 'menunggu_konfirmasi_bendahara')
            ->latest('submitted_at')
            ->paginate(10);

        return view('bendahara.index', compact('programKerjas'));
    }

    public function show(ProgramKerja $programKerja)
    {
        $programKerja->load(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua']);

        // Jika AJAX request (untuk modal)
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $programKerja->id,
                    'nama' => $programKerja->nama,
                    'bidang' => [
                        'nama' => $programKerja->bidang->nama
                    ],
                    'jenis_pengeluaran' => $programKerja->jenis_pengeluaran,
                    'anggaran' => $programKerja->anggaran,
                    'tahun' => $programKerja->tahun,
                    'tanggal' => $programKerja->tanggal ? $programKerja->tanggal->format('Y-m-d') : null,
                    'submitted_by_name' => $programKerja->submittedBy->name ?? null,
                    'submitted_at' => $programKerja->submitted_at ? $programKerja->submitted_at->toISOString() : null,
                ]
            ]);
        }

        // Jika bukan AJAX, return view biasa (opsional)
        return view('bendahara.detail', compact('programKerja'));
    }

    public function approve(Request $request, ProgramKerja $programKerja)
    {
        if ($programKerja->status !== 'menunggu_konfirmasi_bendahara') {
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
                'status' => 'menunggu_approval_ketua',
                'reviewed_by_bendahara' => Auth::id(),
                'reviewed_at_bendahara' => now(),
                'catatan_bendahara' => $validated['catatan'] ?? null,
            ]);

            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => $statusLama,
                'status_ke' => 'menunggu_approval_ketua',
                'catatan' => $validated['catatan'] ?? 'Dikonfirmasi oleh bendahara',
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
                'message' => 'Program kerja berhasil disetujui dan diteruskan ke ketua.'
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
        if ($programKerja->status !== 'menunggu_konfirmasi_bendahara') {
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
                'status' => 'ditolak_bendahara',
                'reviewed_by_bendahara' => Auth::id(),
                'reviewed_at_bendahara' => now(),
                'catatan_bendahara' => $validated['catatan'],
            ]);

            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => $statusLama,
                'status_ke' => 'ditolak_bendahara',
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