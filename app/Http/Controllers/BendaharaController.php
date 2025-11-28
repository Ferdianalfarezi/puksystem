<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BendaharaController extends Controller
{
    /**
     * Display program kerja yang menunggu konfirmasi bendahara
     */
    public function index()
    {
        // Ambil semua program kerja dari semua bidang yang statusnya menunggu konfirmasi bendahara
        $programKerjas = ProgramKerja::with(['bidang', 'submittedBy'])
            ->where('status', 'menunggu_konfirmasi_bendahara')
            ->latest('submitted_at')
            ->paginate(10);

        return view('bendahara.index', compact('programKerjas'));
    }

    /**
     * Show detail program kerja
     */
    public function show(ProgramKerja $programKerja)
    {
        // Load relationships
        $programKerja->load(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua']);

        return view('bendahara.detail', compact('programKerja'));
    }

    /**
     * Approve program kerja oleh bendahara
     */
    public function approve(Request $request, ProgramKerja $programKerja)
    {
        // Validasi status
        if ($programKerja->status !== 'menunggu_konfirmasi_bendahara') {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak dapat disetujui.'
            ]);
        }

        $validated = $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Update status ke menunggu approval ketua
        $programKerja->update([
            'status' => 'menunggu_approval_ketua',
            'reviewed_by_bendahara' => Auth::id(),
            'reviewed_at_bendahara' => now(),
            'catatan_bendahara' => $validated['catatan'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil disetujui dan diteruskan ke ketua.'
        ]);
    }

    /**
     * Reject program kerja oleh bendahara
     */
    public function reject(Request $request, ProgramKerja $programKerja)
    {
        // Validasi status
        if ($programKerja->status !== 'menunggu_konfirmasi_bendahara') {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak dapat ditolak.'
            ]);
        }

        $validated = $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        // Update status ke ditolak bendahara
        $programKerja->update([
            'status' => 'ditolak_bendahara',
            'reviewed_by_bendahara' => Auth::id(),
            'reviewed_at_bendahara' => now(),
            'catatan_bendahara' => $validated['catatan'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil ditolak.'
        ]);
    }
}