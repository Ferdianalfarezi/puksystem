<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\Pencairan;
use App\Models\ProgramKerjaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    public function index()
    {
        $programKerjas = ProgramKerja::with(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua'])
            ->where('status', 'menunggu_pencairan')
            ->latest('reviewed_at_ketua')
            ->paginate(10);

        return view('pencairan.index', compact('programKerjas'));
    }

    public function show(ProgramKerja $programKerja)
    {
        $programKerja->load(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua', 'pencairan']);

        return view('pencairan.detail', compact('programKerja'));
    }

    public function cairkan(Request $request, ProgramKerja $programKerja)
    {
        if ($programKerja->status !== 'menunggu_pencairan') {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak dapat dicairkan.'
            ]);
        }

        $validated = $request->validate([
            'jumlah_dicairkan' => 'required|numeric|min:0|max:' . $programKerja->anggaran,
            'metode_pencairan' => 'required|in:transfer,tunai,cek',
            'nomor_referensi' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'jumlah_dicairkan.required' => 'Jumlah pencairan wajib diisi.',
            'jumlah_dicairkan.max' => 'Jumlah pencairan tidak boleh melebihi anggaran yang disetujui.',
            'metode_pencairan.required' => 'Metode pencairan wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $programKerja->status;

            Pencairan::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'jumlah_dicairkan' => $validated['jumlah_dicairkan'],
                'tanggal_pencairan' => now(),
                'metode_pencairan' => $validated['metode_pencairan'],
                'nomor_referensi' => $validated['nomor_referensi'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'dicairkan_oleh' => Auth::id(),
            ]);

            $programKerja->update([
                'status' => 'dicairkan',
            ]);

            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => $statusLama,
                'status_ke' => 'dicairkan',
                'catatan' => 'Dana dicairkan sebesar Rp ' . number_format($validated['jumlah_dicairkan'], 0, ',', '.') . ' via ' . $validated['metode_pencairan'],
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $programKerja->nama,
                    'anggaran' => $programKerja->anggaran,
                    'jumlah_dicairkan' => $validated['jumlah_dicairkan'],
                    'metode_pencairan' => $validated['metode_pencairan'],
                    'bidang' => $programKerja->bidang->nama,
                    'tahun' => $programKerja->tahun,
                    'tanggal' => $programKerja->tanggal ? $programKerja->tanggal->format('Y-m-d') : null,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dana berhasil dicairkan.'
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