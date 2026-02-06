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
        
        // Ambil semua program kerja untuk dropdown
        $allProgramKerjas = \App\Models\ProgramKerja::with('bidang')->orderBy('nama')->get();
        
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
                'allProgramKerjas',
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
                'allProgramKerjas',
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
    try {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if ($userRole !== 'superadmin' && !$user->bidang_id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki bidang yang terdaftar.'
            ], 400);
        }
        
        $rules = [
            'jenis' => 'required|in:' . implode(',', array_keys(PengajuanBudget::JENIS)),
            'program_kerja_id' => 'nullable|required_if:jenis,program_kerja|exists:program_kerjas,id',
            'nama' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'jenis_pengeluaran' => 'required|in:' . implode(',', PengajuanBudget::JENIS_PENGELUARAN),
            'tahun' => 'required|digits:4|integer|min:2000|max:2100',
            'lampiran' => 'nullable|file|mimes:pdf|max:5120', // ✅ Max 5MB
        ];
        
        // Validasi tambahan untuk jenis pengeluaran Aksi
        if ($request->jenis_pengeluaran === 'Aksi') {
            $rules['no_surat'] = 'required|string|max:255';
            $rules['jumlah_anggota'] = 'required|integer|min:1';
            $rules['nama_aksi'] = 'required|string|max:255';
            $rules['tempat_aksi'] = 'required|string|max:255';
            $rules['jam_aksi'] = 'required|date_format:H:i';
        }
        
        if ($userRole === 'superadmin') {
            $rules['bidang_id'] = 'required|exists:bidangs,id';
        }
        
        $validated = $request->validate($rules);

        if ($validated['jenis'] !== 'program_kerja') {
            $validated['program_kerja_id'] = null;
        }

        if ($userRole === 'superadmin') {
            $validated['bidang_id'] = $request->bidang_id;
        } else {
            $validated['bidang_id'] = $user->bidang_id;
        }
        
        // Jika bukan Aksi, set field aksi jadi null
        if ($request->jenis_pengeluaran !== 'Aksi') {
            $validated['no_surat'] = null;
            $validated['jumlah_anggota'] = null;
            $validated['nama_aksi'] = null;
            $validated['tempat_aksi'] = null;
            $validated['jam_aksi'] = null;
        }
        
        // ✅ Handle upload lampiran PDF
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('lampiran-pengajuan', $fileName, 'public');
            $validated['lampiran'] = $filePath;
        }
        
        $validated['tanggal'] = now()->toDateString();
        $validated['status'] = 'draft';

        DB::beginTransaction();
        
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
                'jenis' => $pengajuanBudget->jenis,
                'anggaran' => $pengajuanBudget->anggaran,
                'bidang' => $pengajuanBudget->bidang->nama ?? '-',
                'tahun' => $pengajuanBudget->tahun,
                'tanggal' => is_string($pengajuanBudget->tanggal) 
                    ? $pengajuanBudget->tanggal 
                    : $pengajuanBudget->tanggal->format('Y-m-d'),
                'no_surat' => $pengajuanBudget->no_surat,
                'jumlah_anggota' => $pengajuanBudget->jumlah_anggota,
                'nama_aksi' => $pengajuanBudget->nama_aksi,
                'tempat_aksi' => $pengajuanBudget->tempat_aksi,
                'jam_aksi' => $pengajuanBudget->jam_aksi,
                'lampiran' => $pengajuanBudget->lampiran, // ✅ Snapshot file
            ],
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan budget berhasil dibuat dengan status draft.'
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        // ✅ Hapus file jika ada error
        if (isset($filePath) && \Storage::disk('public')->exists($filePath)) {
            \Storage::disk('public')->delete($filePath);
        }
        
        \Log::error('Error creating pengajuan budget: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'error_detail' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
}

    public function show(PengajuanBudget $pengajuanBudget)
{
    $user = Auth::user();
    $userRole = $user->role->nama ?? '';
    
    // Authorization check
    if (!in_array($userRole, ['superadmin', 'sekretaris'])) {
        if ($pengajuanBudget->bidang_id !== $user->bidang_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
    }

    // Load relationships
    $pengajuanBudget->load([
        'bidang', 
        'submittedBy', 
        'reviewedByBendahara', 
        'reviewedByKetua', 
        'pencairan.dicairkanOleh',
        'programKerja'
    ]);

    // ✅ ALWAYS RETURN JSON
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $pengajuanBudget->id,
            'nama' => $pengajuanBudget->nama,
            'jenis' => $pengajuanBudget->jenis,
            'jenis_label' => $pengajuanBudget->jenis_label,
            
            'program_kerja_id' => $pengajuanBudget->program_kerja_id,
            'program_kerja' => $pengajuanBudget->programKerja ? [
                'id' => $pengajuanBudget->programKerja->id,
                'nama' => $pengajuanBudget->programKerja->nama,
            ] : null,
            
            'anggaran' => $pengajuanBudget->anggaran,
            'tahun' => $pengajuanBudget->tahun,
            'tanggal' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('Y-m-d') : null,
            'tanggal_formatted' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('d M Y') : '-',
            
            'status' => $pengajuanBudget->status,
            'jenis_pengeluaran' => $pengajuanBudget->jenis_pengeluaran,
            'is_draft' => $pengajuanBudget->isDraft(),
            
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
            
            // Bidang
            'bidang' => [
                'id' => $pengajuanBudget->bidang->id,
                'nama' => $pengajuanBudget->bidang->nama,
            ],
            'bidang_id' => $pengajuanBudget->bidang_id,
            
            // Submission info
            'submitted_at' => $pengajuanBudget->submitted_at,
            'submitted_at_formatted' => $pengajuanBudget->submitted_at 
                ? $pengajuanBudget->submitted_at->format('d M Y, H:i') . ' WIB' 
                : null,
            'submitted_by_name' => $pengajuanBudget->submittedBy?->name,
            
            // Review bendahara
            'reviewed_at_bendahara' => $pengajuanBudget->reviewed_at_bendahara,
            'reviewed_at_bendahara_formatted' => $pengajuanBudget->reviewed_at_bendahara 
                ? $pengajuanBudget->reviewed_at_bendahara->format('d M Y, H:i') . ' WIB' 
                : null,
            'reviewed_by_bendahara_name' => $pengajuanBudget->reviewedByBendahara?->name,
            'catatan_bendahara' => $pengajuanBudget->catatan_bendahara,
            
            // Review ketua
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

    $rules = [
        'jenis' => 'required|in:' . implode(',', array_keys(PengajuanBudget::JENIS)),
        'program_kerja_id' => 'nullable|required_if:jenis,program_kerja|exists:program_kerjas,id',
        'nama' => 'required|string|max:255',
        'anggaran' => 'required|numeric|min:0',
        'jenis_pengeluaran' => 'required|in:' . implode(',', PengajuanBudget::JENIS_PENGELUARAN),
        'tahun' => 'required|digits:4|integer|min:2000|max:2100',
        'tanggal' => 'required|date',
        'lampiran' => 'nullable|file|mimes:pdf|max:5120', // ✅ Max 5MB
    ];
    
    // Validasi tambahan untuk jenis pengeluaran Aksi
    if ($request->jenis_pengeluaran === 'Aksi') {
        $rules['no_surat'] = 'required|string|max:255';
        $rules['jumlah_anggota'] = 'required|integer|min:1';
        $rules['nama_aksi'] = 'required|string|max:255';
        $rules['tempat_aksi'] = 'required|string|max:255';
        $rules['jam_aksi'] = 'required|date_format:H:i';
    }
    
    if ($userRole === 'superadmin') {
        $rules['bidang_id'] = 'required|exists:bidangs,id';
    }
    
    $validated = $request->validate($rules);

    if ($validated['jenis'] !== 'program_kerja') {
        $validated['program_kerja_id'] = null;
    }

    if ($userRole === 'superadmin') {
        $validated['bidang_id'] = $request->bidang_id;
    }
    
    // Jika bukan Aksi, set field aksi jadi null
    if ($request->jenis_pengeluaran !== 'Aksi') {
        $validated['no_surat'] = null;
        $validated['jumlah_anggota'] = null;
        $validated['nama_aksi'] = null;
        $validated['tempat_aksi'] = null;
        $validated['jam_aksi'] = null;
    }
    
    // ✅ Handle upload lampiran PDF
    if ($request->hasFile('lampiran')) {
        // Hapus file lama jika ada
        if ($pengajuanBudget->lampiran && \Storage::disk('public')->exists($pengajuanBudget->lampiran)) {
            \Storage::disk('public')->delete($pengajuanBudget->lampiran);
        }
        
        $file = $request->file('lampiran');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $filePath = $file->storeAs('lampiran-pengajuan', $fileName, 'public');
        $validated['lampiran'] = $filePath;
    }

    DB::beginTransaction();
    try {
        $dataLama = [
            'nama' => $pengajuanBudget->nama,
            'jenis' => $pengajuanBudget->jenis,
            'anggaran' => $pengajuanBudget->anggaran,
            'bidang' => $pengajuanBudget->bidang->nama,
            'tahun' => $pengajuanBudget->tahun,
            'tanggal' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('Y-m-d') : null,
            'lampiran' => $pengajuanBudget->lampiran,
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
                    'jenis' => $pengajuanBudget->jenis,
                    'anggaran' => $pengajuanBudget->anggaran,
                    'bidang' => $pengajuanBudget->bidang->nama,
                    'tahun' => $pengajuanBudget->tahun,
                    'tanggal' => $pengajuanBudget->tanggal->format('Y-m-d'),
                    'lampiran' => $pengajuanBudget->lampiran,
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
        
        // ✅ Hapus file baru jika ada error
        if (isset($filePath) && \Storage::disk('public')->exists($filePath)) {
            \Storage::disk('public')->delete($filePath);
        }
        
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
            'jenis' => $pengajuanBudget->jenis,
            'anggaran' => $pengajuanBudget->anggaran,
            'bidang' => $pengajuanBudget->bidang->nama,
            'tahun' => $pengajuanBudget->tahun,
            'tanggal' => $pengajuanBudget->tanggal ? $pengajuanBudget->tanggal->format('Y-m-d') : null,
            'lampiran' => $pengajuanBudget->lampiran,
        ];

        $pengajuanBudgetId = $pengajuanBudget->id;
        $lampiranPath = $pengajuanBudget->lampiran;

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
        
        // ✅ Hapus file lampiran jika ada
        if ($lampiranPath && \Storage::disk('public')->exists($lampiranPath)) {
            \Storage::disk('public')->delete($lampiranPath);
        }

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
                    'jenis' => $pengajuanBudget->jenis,
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