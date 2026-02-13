<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\PengajuanBudget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DispensasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Hanya superadmin dan sekretaris yang bisa akses
        if (!in_array($userRole, ['superadmin', 'sekretaris'])) {
            abort(403, 'Unauthorized access.');
        }
        
        $query = Dispensasi::with(['pengajuanBudget.bidang']);
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('pengajuanBudget', function($q) use ($request) {
                $q->where('nama_aksi', 'like', '%' . $request->search . '%')
                  ->orWhere('tempat_aksi', 'like', '%' . $request->search . '%');
            });
        }
        
        $perPage = $request->get('perPage', 20);
        
        if ($perPage === 'all') {
            $allData = $query->latest()->get();
            $dispensasis = new \Illuminate\Pagination\LengthAwarePaginator(
                $allData,
                $allData->count(),
                $allData->count(),
                1
            );
        } else {
            $dispensasis = $query->latest()->paginate($perPage);
        }
        
        // Data untuk dropdown create
        $aksiPengajuans = PengajuanBudget::with('bidang')
            ->where('jenis_pengeluaran', 'Aksi')
            ->whereNotNull('lampiran')
            ->latest()
            ->get();
        
        $users = User::where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return view('dispensasi.index', compact('dispensasis', 'aksiPengajuans', 'users', 'perPage'));
    }
    
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'pengajuan_budget_id' => 'required|exists:pengajuan_budgets,id',
        'user_ids' => 'required|array|min:1',
        'user_ids.*' => 'exists:users,id',
        'keterangan' => 'nullable|string|max:1000',
    ], [
        'pengajuan_budget_id.required' => 'Pilih aksi wajib diisi',
        'pengajuan_budget_id.exists' => 'Aksi tidak valid',
        'user_ids.required' => 'Minimal pilih 1 user',
        'user_ids.array' => 'Format user tidak valid',
        'user_ids.min' => 'Minimal pilih 1 user',
        'user_ids.*.exists' => 'User tidak valid',
        'keterangan.max' => 'Keterangan maksimal 1000 karakter',
    ]);
    
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }
    
    // Validasi: Pengajuan budget harus jenis Aksi dan ada lampiran
    $pengajuan = PengajuanBudget::find($request->pengajuan_budget_id);
    if ($pengajuan->jenis_pengeluaran !== 'Aksi' || !$pengajuan->lampiran) {
        return response()->json([
            'success' => false,
            'message' => 'Pengajuan budget harus jenis Aksi dan memiliki lampiran'
        ], 422);
    }
    
    $dispensasi = Dispensasi::create([
        'pengajuan_budget_id' => $request->pengajuan_budget_id,
        'bidang_id' => $pengajuan->bidang_id, // SET bidang_id dari pengajuan budget
        'user_ids' => $request->user_ids,
        'keterangan' => $request->keterangan,
        'status' => 'draft', // Default status
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Dispensasi berhasil ditambahkan',
        'data' => $dispensasi
    ]);
}
    
    public function show($id)
    {
        $dispensasi = Dispensasi::with(['pengajuanBudget.bidang'])->findOrFail($id);
        
        // Get users dari user_ids (karena field user_ids itu JSON array)
        $userIds = $dispensasi->user_ids ?? [];
        $users = User::with('bidang')->whereIn('id', $userIds)->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $dispensasi->id,
                'pengajuan_budget' => [
                    'id' => $dispensasi->pengajuanBudget->id,
                    'nama_aksi' => $dispensasi->pengajuanBudget->nama_aksi,
                    'tempat_aksi' => $dispensasi->pengajuanBudget->tempat_aksi,
                    'tanggal' => $dispensasi->pengajuanBudget->tanggal ? $dispensasi->pengajuanBudget->tanggal->format('d M Y') : null,
                    'jam_aksi' => $dispensasi->pengajuanBudget->jam_aksi,
                    'lampiran_url' => $dispensasi->pengajuanBudget->lampiran ? asset('storage/' . $dispensasi->pengajuanBudget->lampiran) : null,
                ],
                'bidang_nama' => $dispensasi->pengajuanBudget->bidang->nama ?? '-',
                'user_ids' => $userIds, // Array user IDs untuk edit
                'users' => $users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'nik' => $user->nik ?? 'No NIK',
                        'bidang_nama' => $user->bidang->nama ?? '-',
                    ];
                }),
                'keterangan' => $dispensasi->keterangan,
                'created_at_formatted' => $dispensasi->created_at->format('d M Y, H:i') . ' WIB',
            ]
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $dispensasi = Dispensasi::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'pengajuan_budget_id' => 'required|exists:pengajuan_budgets,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'pengajuan_budget_id.required' => 'Pilih aksi wajib diisi',
            'user_ids.required' => 'Minimal pilih 1 user',
            'user_ids.min' => 'Minimal pilih 1 user',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $dispensasi->update([
            'pengajuan_budget_id' => $request->pengajuan_budget_id,
            'user_ids' => $request->user_ids,
            'keterangan' => $request->keterangan,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Dispensasi berhasil diupdate'
        ]);
    }
    
    public function destroy($id)
    {
        $dispensasi = Dispensasi::findOrFail($id);
        $dispensasi->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Dispensasi berhasil dihapus'
        ]);
    }

    public function submit($id)
{
    $dispensasi = Dispensasi::findOrFail($id);
    
    // Cek status harus draft
    if ($dispensasi->status !== 'draft') {
        return response()->json([
            'success' => false,
            'message' => 'Hanya dispensasi dengan status draft yang bisa diajukan'
        ], 422);
    }
    
    // Update status ke menunggu approval sekretaris
    $dispensasi->update([
        'status' => 'menunggu_approval_sekretaris'
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Dispensasi berhasil diajukan ke sekretaris'
    ]);
}

public function print($id)
{
    $dispensasi = Dispensasi::with(['pengajuanBudget.bidang'])->findOrFail($id);
    
    // Cek status harus approved
    if ($dispensasi->status !== 'approved') {
        abort(403, 'Hanya dispensasi yang sudah disetujui yang bisa dicetak');
    }
    
    // Get users dari user_ids
    $userIds = $dispensasi->user_ids ?? [];
    $users = User::with('bidang')->whereIn('id', $userIds)->get();
    
    return view('dispensasi.print', compact('dispensasi', 'users'));
}
}