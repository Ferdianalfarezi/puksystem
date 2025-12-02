<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBudget;
use App\Models\PengajuanBudgetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanBudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        $allBidangs = \App\Models\Bidang::orderBy('nama')->get();
        
        $perPage = $request->get('perPage', 20);
        
        if (in_array($userRole, ['superadmin', 'sekretaris'])) {
            $bidangs = $allBidangs;
            $selectedBidangId = $request->get('bidang_id', 'all');
            
            $baseQuery = PengajuanBudget::with(['bidang', 'submittedBy']);
            
            if ($selectedBidangId !== 'all') {
                $baseQuery->where('bidang_id', $selectedBidangId);
            }
            
            $allPengajuanBudgets = $baseQuery->get();
            
            if ($perPage === 'all') {
                $pengajuanBudgets = $baseQuery->latest()->get();
                $pengajuanBudgets = new \Illuminate\Pagination\LengthAwarePaginator(
                    $pengajuanBudgets,
                    $pengajuanBudgets->count(),
                    $pengajuanBudgets->count(),
                    1,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } else {
                $pengajuanBudgets = $baseQuery->latest()->paginate($perPage);
            }
            
            if ($selectedBidangId !== 'all') {
                $pengajuanBudgets->appends(['bidang_id' => $selectedBidangId]);
            }
            $pengajuanBudgets->appends(['perPage' => $perPage]);
            
            return view('pengajuan-budget.index', compact(
                'pengajuanBudgets',
                'allPengajuanBudgets',
                'bidangs', 
                'selectedBidangId', 
                'allBidangs',
                'perPage'
            ));
            
        } else {
            $baseQuery = PengajuanBudget::with(['bidang', 'submittedBy'])
                ->forBidang($user->bidang_id);
            
            $allPengajuanBudgets = $baseQuery->get();
            
            if ($perPage === 'all') {
                $pengajuanBudgets = $baseQuery->latest()->get();
                $pengajuanBudgets = new \Illuminate\Pagination\LengthAwarePaginator(
                    $pengajuanBudgets,
                    $pengajuanBudgets->count(),
                    $pengajuanBudgets->count(),
                    1,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } else {
                $pengajuanBudgets = $baseQuery->latest()->paginate($perPage);
            }
            
            $pengajuanBudgets->appends(['perPage' => $perPage]);
            
            $bidangs = collect();
            $selectedBidangId = null;
            
            return view('pengajuan-budget.index', compact(
                'pengajuanBudgets',
                'allPengajuanBudgets',
                'bidangs', 
                'selectedBidangId', 
                'allBidangs',
                'perPage'
            ));
        }
    }

    public function create()
    {
        return view('pengajuan-budget.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'jenis_pengeluaran' => 'required|in:' . implode(',', PengajuanBudget::JENIS_PENGELUARAN),
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
            $pengajuanBudget = PengajuanBudget::create($validated);

            PengajuanBudgetHistory::create([
                'pengajuan_budget_id' => $pengajuanBudget->id,
                'tanggal_pengajuan' => $pengajuanBudget->tanggal,
                'status_dari' => null,
                'status_ke' => 'draft',
                'catatan' => 'Pengajuan budget dibuat',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $pengajuanBudget->nama,
                    'anggaran' => $pengajuanBudget->anggaran,
                    'bidang' => $pengajuanBudget->bidang->nama,
                    'tahun' => $pengajuanBudget->tahun,
                    'tanggal' => $pengajuanBudget->tanggal->format('Y-m-d'),
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan budget berhasil dibuat dengan status draft.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(PengajuanBudget $pengajuanBudget)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin', 'sekretaris'])) {
            if ($pengajuanBudget->bidang_id !== $user->bidang_id) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access.'
                    ], 403);
                }
                abort(403, 'Unauthorized access.');
            }
        }

        $pengajuanBudget->load([
            'bidang', 
            'submittedBy', 
            'reviewedByBendahara', 
            'reviewedByKetua', 
            'pencairan.dicairkanOleh'
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pengajuanBudget->id,
                    'nama' => $pengajuanBudget->nama,
                    'anggaran' => $pengajuanBudget->anggaran,
                    'tahun' => $pengajuanBudget->tahun,
                    'tanggal' => $pengajuanBudget->tanggal,
                    'tanggal_formatted' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('d M Y') : '-',
                    'status' => $pengajuanBudget->status,
                    'jenis_pengeluaran' => $pengajuanBudget->jenis_pengeluaran,
                    'is_draft' => $pengajuanBudget->isDraft(),
                    
                    'bidang' => [
                        'id' => $pengajuanBudget->bidang->id,
                        'nama' => $pengajuanBudget->bidang->nama,
                    ],
                    'bidang_id' => $pengajuanBudget->bidang_id,
                    
                    'submitted_at' => $pengajuanBudget->submitted_at,
                    'submitted_at_formatted' => $pengajuanBudget->submitted_at 
                        ? $pengajuanBudget->submitted_at->format('d M Y, H:i') . ' WIB' 
                        : null,
                    'submitted_by_name' => $pengajuanBudget->submittedBy?->name,
                    
                    'reviewed_at_bendahara' => $pengajuanBudget->reviewed_at_bendahara,
                    'reviewed_at_bendahara_formatted' => $pengajuanBudget->reviewed_at_bendahara 
                        ? $pengajuanBudget->reviewed_at_bendahara->format('d M Y, H:i') . ' WIB' 
                        : null,
                    'reviewed_by_bendahara_name' => $pengajuanBudget->reviewedByBendahara?->name,
                    'catatan_bendahara' => $pengajuanBudget->catatan_bendahara,
                    
                    'reviewed_at_ketua' => $pengajuanBudget->reviewed_at_ketua,
                    'reviewed_at_ketua_formatted' => $pengajuanBudget->reviewed_at_ketua 
                        ? $pengajuanBudget->reviewed_at_ketua->format('d M Y, H:i') . ' WIB' 
                        : null,
                    'reviewed_by_ketua_name' => $pengajuanBudget->reviewedByKetua?->name,
                    'catatan_ketua' => $pengajuanBudget->catatan_ketua,
                    
                    'created_at_formatted' => $pengajuanBudget->created_at->format('d M Y, H:i') . ' WIB',
                    'updated_at_formatted' => $pengajuanBudget->updated_at->format('d M Y, H:i') . ' WIB',
                    
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

        $pengajuanBudget->load(['histories']);
        $allBidangs = \App\Models\Bidang::orderBy('nama')->get();
        
        return view('pengajuan-budget.detail', compact('pengajuanBudget', 'allBidangs'));
    }

    public function edit(PengajuanBudget $pengajuanBudget)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin'])) {
            if ($pengajuanBudget->bidang_id !== $user->bidang_id) {
                abort(403, 'Unauthorized access.');
            }
        }

        if (!$pengajuanBudget->isDraft()) {
            return redirect()->route('pengajuan-budget.index')
                ->with('error', 'Hanya pengajuan budget dengan status draft yang bisa diedit.');
        }

        return view('pengajuan-budget.edit', compact('pengajuanBudget'));
    }

    public function update(Request $request, PengajuanBudget $pengajuanBudget)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin'])) {
            if ($pengajuanBudget->bidang_id !== $user->bidang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        }

        if (!$pengajuanBudget->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan budget dengan status draft yang bisa diedit.'
            ]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'jenis_pengeluaran' => 'required|in:' . implode(',', PengajuanBudget::JENIS_PENGELUARAN),
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
                'nama' => $pengajuanBudget->nama,
                'anggaran' => $pengajuanBudget->anggaran,
                'bidang' => $pengajuanBudget->bidang->nama,
                'tahun' => $pengajuanBudget->tahun,
                'tanggal' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('Y-m-d') : null,
            ];

            $pengajuanBudget->update($validated);
            $pengajuanBudget->refresh();

            PengajuanBudgetHistory::create([
                'pengajuan_budget_id' => $pengajuanBudget->id,
                'tanggal_pengajuan' => $pengajuanBudget->tanggal,
                'status_dari' => 'draft',
                'status_ke' => 'draft',
                'catatan' => 'Pengajuan budget diupdate',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'data_lama' => $dataLama,
                    'data_baru' => [
                        'nama' => $pengajuanBudget->nama,
                        'anggaran' => $pengajuanBudget->anggaran,
                        'bidang' => $pengajuanBudget->bidang->nama,
                        'tahun' => $pengajuanBudget->tahun,
                        'tanggal' => $pengajuanBudget->tanggal->format('Y-m-d'),
                    ],
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan budget berhasil diupdate.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(PengajuanBudget $pengajuanBudget)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin'])) {
            if ($pengajuanBudget->bidang_id !== $user->bidang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        }

        if (!$pengajuanBudget->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan budget dengan status draft yang bisa dihapus.'
            ]);
        }

        DB::beginTransaction();
        try {
            $dataPengajuanBudget = [
                'nama' => $pengajuanBudget->nama,
                'anggaran' => $pengajuanBudget->anggaran,
                'bidang' => $pengajuanBudget->bidang->nama,
                'tahun' => $pengajuanBudget->tahun,
                'tanggal' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('Y-m-d') : null,
            ];

            $pengajuanBudgetId = $pengajuanBudget->id;

            PengajuanBudgetHistory::create([
                'pengajuan_budget_id' => $pengajuanBudgetId,
                'tanggal_pengajuan' => $pengajuanBudget->tanggal,
                'status_dari' => 'draft',
                'status_ke' => 'deleted',
                'catatan' => 'Pengajuan budget dihapus',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => $dataPengajuanBudget,
            ]);

            $pengajuanBudget->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan budget berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submit(PengajuanBudget $pengajuanBudget)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['superadmin'])) {
            if ($pengajuanBudget->bidang_id !== $user->bidang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        }

        if (!$pengajuanBudget->canBeSubmitted()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan budget ini tidak bisa diajukan.'
            ]);
        }

        DB::beginTransaction();
        try {
            $statusLama = $pengajuanBudget->status;

            $pengajuanBudget->update([
                'status' => 'menunggu_konfirmasi_bendahara',
                'submitted_at' => now(),
                'submitted_by' => Auth::id(),
            ]);

            PengajuanBudgetHistory::create([
                'pengajuan_budget_id' => $pengajuanBudget->id,
                'tanggal_pengajuan' => $pengajuanBudget->tanggal,
                'status_dari' => $statusLama,
                'status_ke' => 'menunggu_konfirmasi_bendahara',
                'catatan' => 'Pengajuan budget diajukan untuk dikonfirmasi bendahara',
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $pengajuanBudget->nama,
                    'anggaran' => $pengajuanBudget->anggaran,
                    'bidang' => $pengajuanBudget->bidang->nama,
                    'tahun' => $pengajuanBudget->tahun,
                    'tanggal' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('Y-m-d') : null,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan budget berhasil diajukan ke bendahara.'
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