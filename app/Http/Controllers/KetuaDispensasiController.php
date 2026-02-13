<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\DispensasiHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KetuaDispensasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Hanya ketua dan superadmin yang bisa akses
        if (!in_array($userRole, ['ketua', 'superadmin'])) {
            abort(403, 'Unauthorized access.');
        }
        
        $query = Dispensasi::with(['pengajuanBudget.bidang', 'bidang', 'submittedBy', 'reviewedBySekretaris'])
            ->where('status', 'menunggu_approval_ketua');
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('pengajuanBudget', function($q) use ($request) {
                $q->where('nama_aksi', 'like', '%' . $request->search . '%')
                  ->orWhere('tempat_aksi', 'like', '%' . $request->search . '%');
            });
        }
        
        $perPage = $request->get('perPage', 20);
        
        if ($perPage === 'all') {
            $allData = $query->latest('reviewed_at_sekretaris')->get();
            $dispensasis = new \Illuminate\Pagination\LengthAwarePaginator(
                $allData,
                $allData->count(),
                $allData->count(),
                1
            );
        } else {
            $dispensasis = $query->latest('reviewed_at_sekretaris')->paginate($perPage);
        }
        
        return view('ketua.dispensasi.index', compact('dispensasis', 'perPage'));
    }
    
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['ketua', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'catatan' => 'nullable|string|max:500',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $dispensasi = Dispensasi::findOrFail($id);
        
        if (!$dispensasi->isWaitingKetua()) {
            return response()->json([
                'success' => false,
                'message' => 'Dispensasi tidak dalam status menunggu approval ketua'
            ], 422);
        }
        
        DB::transaction(function() use ($dispensasi, $user, $request) {
            $oldStatus = $dispensasi->status;
            
            $dispensasi->update([
                'status' => 'approved',
                'reviewed_by_ketua' => $user->id,
                'reviewed_at_ketua' => now(),
                'catatan_ketua' => $request->catatan,
            ]);
            
            // Record history
            DispensasiHistory::create([
                'dispensasi_id' => $dispensasi->id,
                'status_dari' => $oldStatus,
                'status_ke' => 'approved',
                'catatan' => $request->catatan ?? 'Disetujui oleh ketua',
                'dilakukan_oleh' => $user->id,
                'dilakukan_pada' => now(),
            ]);
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Dispensasi berhasil disetujui'
        ]);
    }
    
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['ketua', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'catatan' => 'required|string|max:500',
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $dispensasi = Dispensasi::findOrFail($id);
        
        if (!$dispensasi->isWaitingKetua()) {
            return response()->json([
                'success' => false,
                'message' => 'Dispensasi tidak dalam status menunggu approval ketua'
            ], 422);
        }
        
        DB::transaction(function() use ($dispensasi, $user, $request) {
            $oldStatus = $dispensasi->status;
            
            $dispensasi->update([
                'status' => 'ditolak_ketua',
                'reviewed_by_ketua' => $user->id,
                'reviewed_at_ketua' => now(),
                'catatan_ketua' => $request->catatan,
            ]);
            
            // Record history
            DispensasiHistory::create([
                'dispensasi_id' => $dispensasi->id,
                'status_dari' => $oldStatus,
                'status_ke' => 'ditolak_ketua',
                'catatan' => $request->catatan,
                'dilakukan_oleh' => $user->id,
                'dilakukan_pada' => now(),
            ]);
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Dispensasi telah ditolak'
        ]);
    }
}