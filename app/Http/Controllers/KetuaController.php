<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KetuaController extends Controller
{
    /**
     * Display program kerja yang menunggu approval ketua
     */
    public function index()
    {
        // Ambil semua program kerja dari semua bidang yang statusnya menunggu approval ketua
        $programKerjas = ProgramKerja::with(['bidang', 'submittedBy', 'reviewedByBendahara'])
            ->where('status', 'menunggu_approval_ketua')
            ->latest('reviewed_at_bendahara')
            ->paginate(10);

        return view('ketua.index', compact('programKerjas'));
    }

    /**
     * Show detail program kerja
     */
    public function show(ProgramKerja $programKerja)
    {
        // Load relationships
        $programKerja->load(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua']);

        return view('ketua.detail', compact('programKerja'));
    }

    /**
     * Approve program kerja oleh ketua (final approval)
     */
    public function approve(Request $request, ProgramKerja $programKerja)
    {
        // Validasi status
        if ($programKerja->status !== 'menunggu_approval_ketua') {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak dapat disetujui.'
            ]);
        }

        $validated = $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Update status ke disetujui (final)
        $programKerja->update([
            'status' => 'disetujui',
            'reviewed_by_ketua' => Auth::id(),
            'reviewed_at_ketua' => now(),
            'catatan_ketua' => $validated['catatan'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil disetujui. Status final: DISETUJUI.'
        ]);
    }

    /**
     * Reject program kerja oleh ketua
     */
    public function reject(Request $request, ProgramKerja $programKerja)
    {
        // Validasi status
        if ($programKerja->status !== 'menunggu_approval_ketua') {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak dapat ditolak.'
            ]);
        }

        $validated = $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        // Update status ke ditolak ketua
        $programKerja->update([
            'status' => 'ditolak_ketua',
            'reviewed_by_ketua' => Auth::id(),
            'reviewed_at_ketua' => now(),
            'catatan_ketua' => $validated['catatan'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil ditolak.'
        ]);
    }
}