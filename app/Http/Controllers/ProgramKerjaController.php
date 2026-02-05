<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramKerjaController extends Controller
{
    public function index(Request $request)
    {
        $userRole = Auth::user()->role->nama ?? '';
        $userBidangId = Auth::user()->bidang_id ?? null;
        
        // Query untuk semua program kerja (untuk statistics dan Gantt Chart)
        $queryAll = ProgramKerja::with('bidang')->latest();
        
        // Query untuk tabel (bisa difilter per bidang)
        $query = ProgramKerja::with('bidang')->latest();
        
        // Filter berdasarkan role
        if (!in_array($userRole, ['superadmin', 'sekretaris'])) {
            // Admin hanya lihat bidang sendiri
            $query->where('bidang_id', $userBidangId);
            $queryAll->where('bidang_id', $userBidangId);
        }
        
        // Filter berdasarkan bidang yang dipilih (hanya untuk superadmin/sekretaris)
        $selectedBidangId = 'all';
        
        if (in_array($userRole, ['superadmin', 'sekretaris']) && 
            $request->has('bidang_id') && 
            $request->bidang_id !== 'all') {
            
            $selectedBidangId = $request->bidang_id;
            $query->where('bidang_id', $selectedBidangId);
            $queryAll->where('bidang_id', $selectedBidangId);
        }
        
        // Get semua data untuk statistics dan Gantt Chart
        $allProgramKerjas = $queryAll->get();
        
        // Paginate untuk table
        $perPage = $request->get('perPage', 20);
        
        if ($perPage === 'all') {
            $programKerjas = $query->get();
        } else {
            $programKerjas = $query->paginate($perPage);
        }
        
        // Ambil data bidang untuk filter dan modal create
        $bidangsForFilter = collect();
        $bidangsForCreate = collect();
        
        if (in_array($userRole, ['superadmin', 'sekretaris'])) {
            $bidangsForFilter = Bidang::all();
            $bidangsForCreate = Bidang::all();
        } else {
            // Untuk admin biasa, hanya bidang mereka sendiri
            $bidangsForFilter = Bidang::where('id', $userBidangId)->get();
            $bidangsForCreate = Bidang::where('id', $userBidangId)->get();
        }
        
        return view('program-kerja.index', [
            'programKerjas' => $programKerjas,
            'bidangs' => $bidangsForFilter, // Untuk filter dropdown
            'bidangsForCreate' => $bidangsForCreate, // Untuk modal create
            'selectedBidangId' => $selectedBidangId,
            'perPage' => $perPage,
            'allProgramKerjas' => $allProgramKerjas,
            'userRole' => $userRole
        ]);
    }

    public function store(Request $request)
    {
        $userRole = Auth::user()->role->nama ?? '';
        $userBidangId = Auth::user()->bidang_id ?? null;
        
        // Validasi
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'jenis_pengeluaran' => 'nullable|string|max:100',
            'tahun' => 'required|integer|min:2000|max:2100',
            'tanggal' => 'required|date',
        ]);
        
        // Untuk admin biasa, otomatis set bidang_id dari user
        if (!in_array($userRole, ['superadmin', 'sekretaris'])) {
            $validated['bidang_id'] = $userBidangId;
        } else {
            // Untuk superadmin/sekretaris, ambil dari input form
            $request->validate(['bidang_id' => 'required|exists:bidangs,id']);
            $validated['bidang_id'] = $request->bidang_id;
        }

        ProgramKerja::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil ditambahkan!'
        ]);
    }

    public function show($id)
    {
        $userRole = Auth::user()->role->nama ?? '';
        $userBidangId = Auth::user()->bidang_id ?? null;
        
        $programKerja = ProgramKerja::with('bidang')->findOrFail($id);
        
        // Cek authorization
        if (!in_array($userRole, ['superadmin', 'sekretaris']) && 
            $programKerja->bidang_id != $userBidangId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke program kerja ini'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'data' => $programKerja
        ]);
    }

    public function update(Request $request, $id)
    {
        $userRole = Auth::user()->role->nama ?? '';
        $userBidangId = Auth::user()->bidang_id ?? null;
        
        $programKerja = ProgramKerja::findOrFail($id);
        
        // Cek authorization
        if (!in_array($userRole, ['superadmin', 'sekretaris']) && 
            $programKerja->bidang_id != $userBidangId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengedit program kerja ini'
            ], 403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'jenis_pengeluaran' => 'nullable|string|max:100',
            'tahun' => 'required|integer|min:2000|max:2100',
            'tanggal' => 'required|date',
        ]);
        
        // Jika superadmin/sekretaris, bisa ubah bidang
        if (in_array($userRole, ['superadmin', 'sekretaris'])) {
            $request->validate(['bidang_id' => 'required|exists:bidangs,id']);
            $validated['bidang_id'] = $request->bidang_id;
        } else {
            // Admin biasa tidak bisa ubah bidang
            $validated['bidang_id'] = $programKerja->bidang_id;
        }

        $programKerja->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $userRole = Auth::user()->role->nama ?? '';
        $userBidangId = Auth::user()->bidang_id ?? null;
        
        $programKerja = ProgramKerja::findOrFail($id);
        
        // Cek authorization
        if (!in_array($userRole, ['superadmin', 'sekretaris']) && 
            $programKerja->bidang_id != $userBidangId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus program kerja ini'
            ], 403);
        }

        $programKerja->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program kerja berhasil dihapus!'
        ]);
    }

    public function detail($id)
    {
        $userRole = Auth::user()->role->nama ?? '';
        $userBidangId = Auth::user()->bidang_id ?? null;
        
        $programKerja = ProgramKerja::with('bidang')->findOrFail($id);
        
        // Cek authorization
        if (!in_array($userRole, ['superadmin', 'sekretaris']) && 
            $programKerja->bidang_id != $userBidangId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke detail program kerja ini'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'data' => $programKerja
        ]);
    }
}