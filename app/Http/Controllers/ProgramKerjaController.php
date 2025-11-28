<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramKerjaController extends Controller
{
    /**
     * Display a listing - Auto filter by bidang untuk admin, semua untuk superadmin
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Ambil semua bidang untuk dropdown
        $allBidangs = \App\Models\Bidang::orderBy('nama')->get();
        
        // Superadmin dan sekretaris bisa pilih bidang yang mau dilihat
        if (in_array($userRole, ['superadmin', 'sekretaris'])) {
            $bidangs = $allBidangs;
            
            // Cek apakah ada filter bidang dari query string
            $selectedBidangId = $request->get('bidang_id', 'all');
            
            if ($selectedBidangId === 'all') {
                // Tampilkan semua program kerja
                $programKerjas = ProgramKerja::with(['bidang', 'submittedBy'])
                    ->latest()
                    ->paginate(10);
            } else {
                // Filter berdasarkan bidang yang dipilih
                $programKerjas = ProgramKerja::with(['bidang', 'submittedBy'])
                    ->where('bidang_id', $selectedBidangId)
                    ->latest()
                    ->paginate(10)
                    ->appends(['bidang_id' => $selectedBidangId]);
            }
            
            return view('program-kerja.index', compact('programKerjas', 'bidangs', 'selectedBidangId', 'allBidangs'));
        } else {
            // Admin hanya lihat program kerja bidangnya
            $programKerjas = ProgramKerja::with(['bidang', 'submittedBy'])
                ->forBidang($user->bidang_id)
                ->latest()
                ->paginate(10);
            
            // Set empty variables untuk role selain superadmin/sekretaris
            $bidangs = collect();
            $selectedBidangId = null;
            
            return view('program-kerja.index', compact('programKerjas', 'bidangs', 'selectedBidangId', 'allBidangs'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('program-kerja.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'tahun' => 'required|digits:4|integer|min:2000|max:2100',
            'bidang_id' => $userRole === 'superadmin' ? 'required|exists:bidangs,id' : 'nullable',
        ]);

        // Superadmin bisa pilih bidang, Admin otomatis pakai bidang sendiri
        if ($userRole === 'superadmin') {
            $validated['bidang_id'] = $request->bidang_id;
        } else {
            $validated['bidang_id'] = $user->bidang_id;
        }
        
        $validated['status'] = 'draft';

        ProgramKerja::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil dibuat dengan status draft.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgramKerja $programKerja)
{
    $user = Auth::user();
    $userRole = $user->role->nama ?? '';
    
    // Superadmin dan Sekretaris bisa lihat semua
    if (!in_array($userRole, ['superadmin', 'sekretaris'])) {
        // Cek ownership untuk role lain
        if ($programKerja->bidang_id !== $user->bidang_id) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
            abort(403, 'Unauthorized access.');
        }
    }

    // Jika AJAX request (untuk modal edit)
    if (request()->ajax()) {
        // Include data bidang untuk edit
        $data = $programKerja->toArray();
        $data['bidang'] = $programKerja->bidang;
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Jika request biasa (untuk halaman detail)
    $programKerja->load(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua']);
    
    // Pass allBidangs untuk edit modal di detail page
    $allBidangs = \App\Models\Bidang::orderBy('nama')->get();
    
    return view('program-kerja.detail', compact('programKerja', 'allBidangs'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Superadmin bisa edit semua
        if (!in_array($userRole, ['superadmin'])) {
            // Cek ownership untuk role lain
            if ($programKerja->bidang_id !== $user->bidang_id) {
                abort(403, 'Unauthorized access.');
            }
        }

        // Hanya bisa edit kalau masih draft
        if (!$programKerja->isDraft()) {
            return redirect()->route('program-kerja.index')
                ->with('error', 'Hanya program kerja dengan status draft yang bisa diedit.');
        }

        return view('program-kerja.edit', compact('programKerja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Superadmin bisa update semua
        if (!in_array($userRole, ['superadmin'])) {
            // Cek ownership untuk role lain
            if ($programKerja->bidang_id !== $user->bidang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        }

        // Hanya bisa update kalau masih draft
        if (!$programKerja->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya program kerja dengan status draft yang bisa diedit.'
            ]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'tahun' => 'required|digits:4|integer|min:2000|max:2100',
            'bidang_id' => $userRole === 'superadmin' ? 'required|exists:bidangs,id' : 'nullable',
        ]);

        // Superadmin bisa ganti bidang, Admin tidak bisa
        if ($userRole === 'superadmin') {
            $validated['bidang_id'] = $request->bidang_id;
        }

        $programKerja->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil diupdate.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Superadmin bisa delete semua
        if (!in_array($userRole, ['superadmin'])) {
            // Cek ownership untuk role lain
            if ($programKerja->bidang_id !== $user->bidang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        }

        // Hanya bisa hapus kalau masih draft
        if (!$programKerja->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya program kerja dengan status draft yang bisa dihapus.'
            ]);
        }

        $programKerja->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil dihapus.'
        ]);
    }

    /**
     * Submit program kerja untuk approval
     */
    public function submit(ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Superadmin bisa submit semua
        if (!in_array($userRole, ['superadmin'])) {
            // Cek ownership untuk role lain
            if ($programKerja->bidang_id !== $user->bidang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        }

        // Hanya bisa submit kalau masih draft
        if (!$programKerja->canBeSubmitted()) {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak bisa diajukan.'
            ]);
        }

        $programKerja->update([
            'status' => 'menunggu_konfirmasi_bendahara',
            'submitted_at' => now(),
            'submitted_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil diajukan ke bendahara.'
        ]);
    }
}