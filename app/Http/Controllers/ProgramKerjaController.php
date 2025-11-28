<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\ProgramKerjaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgramKerjaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        $allBidangs = \App\Models\Bidang::orderBy('nama')->get();
        
        if (in_array($userRole, ['superadmin', 'sekretaris'])) {
            $bidangs = $allBidangs;
            $selectedBidangId = $request->get('bidang_id', 'all');
            
            if ($selectedBidangId === 'all') {
                $programKerjas = ProgramKerja::with(['bidang', 'submittedBy'])
                    ->latest()
                    ->paginate(10);
            } else {
                $programKerjas = ProgramKerja::with(['bidang', 'submittedBy'])
                    ->where('bidang_id', $selectedBidangId)
                    ->latest()
                    ->paginate(10)
                    ->appends(['bidang_id' => $selectedBidangId]);
            }
            
            return view('program-kerja.index', compact('programKerjas', 'bidangs', 'selectedBidangId', 'allBidangs'));
        } else {
            $programKerjas = ProgramKerja::with(['bidang', 'submittedBy'])
                ->forBidang($user->bidang_id)
                ->latest()
                ->paginate(10);
            
            $bidangs = collect();
            $selectedBidangId = null;
            
            return view('program-kerja.index', compact('programKerjas', 'bidangs', 'selectedBidangId', 'allBidangs'));
        }
    }

    public function create()
    {
        return view('program-kerja.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'tahun' => 'required|digits:4|integer|min:2000|max:2100',
            'tanggal' => 'required|date',
            'bidang_id' => $userRole === 'superadmin' ? 'required|exists:bidangs,id' : 'nullable',
        ]);

        if ($userRole === 'superadmin') {
            $validated['bidang_id'] = $request->bidang_id;
        } else {
            $validated['bidang_id'] = $user->bidang_id;
        }
        
        $validated['status'] = 'draft';

        DB::beginTransaction();
        try {
            $programKerja = ProgramKerja::create($validated);

            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => null,
                'status_ke' => 'draft',
                'catatan' => 'Program kerja dibuat',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $programKerja->nama,
                    'anggaran' => $programKerja->anggaran,
                    'bidang' => $programKerja->bidang->nama,
                    'tahun' => $programKerja->tahun,
                    'tanggal' => $programKerja->tanggal->format('Y-m-d'),
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Program kerja berhasil dibuat dengan status draft.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin', 'sekretaris'])) {
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

        if (request()->ajax()) {
            $data = $programKerja->toArray();
            $data['bidang'] = $programKerja->bidang;
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        $programKerja->load(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua', 'pencairan', 'histories']);
        
        $allBidangs = \App\Models\Bidang::orderBy('nama')->get();
        
        return view('program-kerja.detail', compact('programKerja', 'allBidangs'));
    }

    public function edit(ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin'])) {
            if ($programKerja->bidang_id !== $user->bidang_id) {
                abort(403, 'Unauthorized access.');
            }
        }

        if (!$programKerja->isDraft()) {
            return redirect()->route('program-kerja.index')
                ->with('error', 'Hanya program kerja dengan status draft yang bisa diedit.');
        }

        return view('program-kerja.edit', compact('programKerja'));
    }

    public function update(Request $request, ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin'])) {
            if ($programKerja->bidang_id !== $user->bidang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        }

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
            'tanggal' => 'required|date',
            'bidang_id' => $userRole === 'superadmin' ? 'required|exists:bidangs,id' : 'nullable',
        ]);

        if ($userRole === 'superadmin') {
            $validated['bidang_id'] = $request->bidang_id;
        }

        DB::beginTransaction();
        try {
            $dataLama = [
                'nama' => $programKerja->nama,
                'anggaran' => $programKerja->anggaran,
                'bidang' => $programKerja->bidang->nama,
                'tahun' => $programKerja->tahun,
                'tanggal' => $programKerja->tanggal ? $programKerja->tanggal->format('Y-m-d') : null,
            ];

            $programKerja->update($validated);
            $programKerja->refresh();

            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => 'draft',
                'status_ke' => 'draft',
                'catatan' => 'Program kerja diupdate',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'data_lama' => $dataLama,
                    'data_baru' => [
                        'nama' => $programKerja->nama,
                        'anggaran' => $programKerja->anggaran,
                        'bidang' => $programKerja->bidang->nama,
                        'tahun' => $programKerja->tahun,
                        'tanggal' => $programKerja->tanggal->format('Y-m-d'),
                    ],
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Program kerja berhasil diupdate.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin'])) {
            if ($programKerja->bidang_id !== $user->bidang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        }

        if (!$programKerja->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya program kerja dengan status draft yang bisa dihapus.'
            ]);
        }

        DB::beginTransaction();
        try {
            $dataProgramKerja = [
                'nama' => $programKerja->nama,
                'anggaran' => $programKerja->anggaran,
                'bidang' => $programKerja->bidang->nama,
                'tahun' => $programKerja->tahun,
                'tanggal' => $programKerja->tanggal ? $programKerja->tanggal->format('Y-m-d') : null,
            ];

            $programKerjaId = $programKerja->id;

            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerjaId,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => 'draft',
                'status_ke' => 'deleted',
                'catatan' => 'Program kerja dihapus',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => $dataProgramKerja,
            ]);

            $programKerja->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Program kerja berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submit(ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin'])) {
            if ($programKerja->bidang_id !== $user->bidang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        }

        if (!$programKerja->canBeSubmitted()) {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak bisa diajukan.'
            ]);
        }

        DB::beginTransaction();
        try {
            $statusLama = $programKerja->status;

            $programKerja->update([
                'status' => 'menunggu_konfirmasi_bendahara',
                'submitted_at' => now(),
                'submitted_by' => Auth::id(),
            ]);

            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => $statusLama,
                'status_ke' => 'menunggu_konfirmasi_bendahara',
                'catatan' => 'Program kerja diajukan untuk dikonfirmasi bendahara',
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
                'message' => 'Program kerja berhasil diajukan ke bendahara.'
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