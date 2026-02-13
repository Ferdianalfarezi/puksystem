<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\DispensasiHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SekretarisDispensasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Hanya sekretaris dan superadmin yang bisa akses
        if (!in_array($userRole, ['sekretaris', 'superadmin'])) {
            abort(403, 'Unauthorized access.');
        }
        
        $query = Dispensasi::with(['pengajuanBudget.bidang', 'bidang', 'submittedBy'])
            ->where('status', 'menunggu_approval_sekretaris');
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('pengajuanBudget', function($q) use ($request) {
                $q->where('nama_aksi', 'like', '%' . $request->search . '%')
                  ->orWhere('tempat_aksi', 'like', '%' . $request->search . '%');
            });
        }
        
        $perPage = $request->get('perPage', 20);
        
        if ($perPage === 'all') {
            $allData = $query->latest('submitted_at')->get();
            $dispensasis = new \Illuminate\Pagination\LengthAwarePaginator(
                $allData,
                $allData->count(),
                $allData->count(),
                1
            );
        } else {
            $dispensasis = $query->latest('submitted_at')->paginate($perPage);
        }
        
        return view('sekretaris.dispensasi.index', compact('dispensasis', 'perPage'));
    }
    
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['sekretaris', 'superadmin'])) {
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
        
        if (!$dispensasi->isWaitingSekretaris()) {
            return response()->json([
                'success' => false,
                'message' => 'Dispensasi tidak dalam status menunggu approval sekretaris'
            ], 422);
        }
        
        DB::transaction(function() use ($dispensasi, $user, $request) {
            $oldStatus = $dispensasi->status;
            
            $dispensasi->update([
                'status' => 'menunggu_approval_ketua',
                'reviewed_by_sekretaris' => $user->id,
                'reviewed_at_sekretaris' => now(),
                'catatan_sekretaris' => $request->catatan,
            ]);
            
            // Record history
            DispensasiHistory::create([
                'dispensasi_id' => $dispensasi->id,
                'status_dari' => $oldStatus,
                'status_ke' => 'menunggu_approval_ketua',
                'catatan' => $request->catatan ?? 'Disetujui oleh sekretaris',
                'dilakukan_oleh' => $user->id,
                'dilakukan_pada' => now(),
            ]);
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Dispensasi berhasil disetujui dan diteruskan ke ketua'
        ]);
    }
    
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if (!in_array($userRole, ['sekretaris', 'superadmin'])) {
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
        
        if (!$dispensasi->isWaitingSekretaris()) {
            return response()->json([
                'success' => false,
                'message' => 'Dispensasi tidak dalam status menunggu approval sekretaris'
            ], 422);
        }
        
        DB::transaction(function() use ($dispensasi, $user, $request) {
            $oldStatus = $dispensasi->status;
            
            $dispensasi->update([
                'status' => 'ditolak_sekretaris',
                'reviewed_by_sekretaris' => $user->id,
                'reviewed_at_sekretaris' => now(),
                'catatan_sekretaris' => $request->catatan,
            ]);
            
            // Record history
            DispensasiHistory::create([
                'dispensasi_id' => $dispensasi->id,
                'status_dari' => $oldStatus,
                'status_ke' => 'ditolak_sekretaris',
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