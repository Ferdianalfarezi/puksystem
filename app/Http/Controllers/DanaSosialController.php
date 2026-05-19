<?php

namespace App\Http\Controllers;

use App\Models\DanaSosial;
use App\Models\DanaSosialHistory;
use App\Models\Koorlap;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DanaSosialController extends Controller
{
    /**
     * Check if user can access Dana Sosial
     */
    private function checkAccess(): bool
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Superadmin always has access
        if ($userRole === 'superadmin') {
            return true;
        }
        
        // User registered as Koorlap
        $isKoorlap = Koorlap::where('user_id', $user->id)->exists();
        if ($isKoorlap) {
            return true;
        }
        
        // User from Bidang Sosial (id=4)
        if ($user->bidang_id == DanaSosial::BIDANG_SOSIAL_ID) {
            return true;
        }
        
        return false;
    }

    /**
     * Get current user's Koorlap data
     */
    private function getUserKoorlap(): ?Koorlap
    {
        return Koorlap::where('user_id', Auth::id())->first();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        if (!$this->checkAccess()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        $userKoorlap = $this->getUserKoorlap();
        $isBidangSosial = $user->bidang_id == DanaSosial::BIDANG_SOSIAL_ID;
        
        $perPage = $request->get('perPage', 20);
        
        // Base query with eager loading
        $query = DanaSosial::with(['koorlap.user', 'user', 'approvedBy', 'verifiedBy']);
        
        // Filter berdasarkan role
        if ($userRole === 'superadmin' || $isBidangSosial) {
            // Superadmin & Bidang Sosial: lihat semua
            // Optional: filter by koorlap
            if ($request->filled('koorlap_id')) {
                $query->where('koorlap_id', $request->koorlap_id);
            }
        } else if ($userKoorlap) {
            // Koorlap: hanya lihat data bawahannya
            $query->where('koorlap_id', $userKoorlap->id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        // Get all for statistics (before pagination)
        $allDanaSosial = (clone $query)->get();
        
        // Paginate
        if ($perPage === 'all') {
            $danaSosials = $query->latest()->get();
            $danaSosials = new \Illuminate\Pagination\LengthAwarePaginator(
                $danaSosials,
                $danaSosials->count(),
                $danaSosials->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $danaSosials = $query->latest()->paginate($perPage);
        }
        
        $danaSosials->appends($request->query());
        
        // Get koorlaps for filter dropdown (superadmin & bidang sosial only)
        $koorlaps = collect();
        if ($userRole === 'superadmin' || $isBidangSosial) {
            $koorlaps = Koorlap::with('user')->orderBy('nama')->get();
        }
        
        // Get users for create modal
        $availableUsers = collect();
        if ($userRole === 'superadmin') {
            // Superadmin: semua user yang punya koorlap
            $availableUsers = User::whereNotNull('koorlap_id')
                ->with(['koorlap', 'bidang'])
                ->orderBy('name')
                ->get();
        } else if ($userKoorlap) {
            // Koorlap: hanya user bawahannya
            $availableUsers = User::where('koorlap_id', $userKoorlap->id)
                ->with('bidang')
                ->orderBy('name')
                ->get();
        }
        
        return view('dana-sosial.index', compact(
            'danaSosials',
            'allDanaSosial',
            'koorlaps',
            'availableUsers',
            'userKoorlap',
            'perPage'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        $userKoorlap = $this->getUserKoorlap();
        
        // Only Koorlap and Superadmin can create
        if ($userRole !== 'superadmin' && !$userKoorlap) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk membuat pengajuan.'
            ], 403);
        }
        
        $rules = [
            'user_id' => 'required|exists:users,id',
            'jenis' => 'required|in:' . implode(',', array_keys(DanaSosial::JENIS)),
            'nominal' => 'required_if:jenis,duka_cita|nullable|numeric|min:0',
            'evident' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
        
        // Superadmin bisa pilih koorlap
        if ($userRole === 'superadmin') {
            $rules['koorlap_id'] = 'required|exists:koorlaps,id';
        }
        
        try {
            $validated = $request->validate($rules);
            
            // Determine koorlap_id
            if ($userRole === 'superadmin') {
                $koorlapId = $validated['koorlap_id'];
            } else {
                $koorlapId = $userKoorlap->id;
            }
            
            // Validate user belongs to koorlap (unless superadmin)
            if ($userRole !== 'superadmin') {
                $targetUser = User::find($validated['user_id']);
                if (!$targetUser || $targetUser->koorlap_id !== $koorlapId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User yang dipilih bukan bawahan Anda.'
                    ], 422);
                }
            }
            
            // Determine nominal
            $jenis = $validated['jenis'];
            if ($jenis === 'duka_cita') {
                $nominal = $validated['nominal'];
            } else {
                $nominal = DanaSosial::getNominalByJenis($jenis);
            }
            
            // Handle file upload
            $evidentPath = null;
            if ($request->hasFile('evident')) {
                $file = $request->file('evident');
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $evidentPath = $file->storeAs('evident-dana-sosial', $fileName, 'public');
            }
            
            DB::beginTransaction();
            
            $danaSosial = DanaSosial::create([
                'koorlap_id' => $koorlapId,
                'user_id' => $validated['user_id'],
                'jenis' => $jenis,
                'nominal' => $nominal,
                'evident' => $evidentPath,
                'status' => 'menunggu_persetujuan_bidang_sosial',
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan dana sosial berhasil dibuat.'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus file jika ada error
            if (isset($evidentPath) && Storage::disk('public')->exists($evidentPath)) {
                Storage::disk('public')->delete($evidentPath);
            }
            
            \Log::error('Error creating dana sosial: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DanaSosial $danaSosial): JsonResponse
    {
        if (!$this->checkAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        $userKoorlap = $this->getUserKoorlap();
        $isBidangSosial = $user->bidang_id == DanaSosial::BIDANG_SOSIAL_ID;
        
        // Authorization check
        if ($userRole !== 'superadmin' && !$isBidangSosial) {
            if ($userKoorlap && $danaSosial->koorlap_id !== $userKoorlap->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }
        
        $danaSosial->load(['koorlap.user', 'user.bidang', 'approvedBy', 'verifiedBy']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $danaSosial->id,
                'koorlap' => [
                    'id' => $danaSosial->koorlap->id,
                    'nama' => $danaSosial->koorlap->nama,
                    'user_name' => $danaSosial->koorlap->user->name ?? '-',
                ],
                'user' => [
                    'id' => $danaSosial->user->id,
                    'name' => $danaSosial->user->name,
                    'nik' => $danaSosial->user->nik,
                    'bidang' => $danaSosial->user->bidang->nama ?? '-',
                ],
                'jenis' => $danaSosial->jenis,
                'jenis_label' => $danaSosial->jenis_label,
                'nominal' => $danaSosial->nominal,
                'evident' => $danaSosial->evident,
                'evident_url' => $danaSosial->evident ? asset('storage/' . $danaSosial->evident) : null,
                'status' => $danaSosial->status,
                'status_label' => $danaSosial->status_label,
                'status_badge_class' => $danaSosial->status_badge_class,
                
                // Approval info
                'approved_by_name' => $danaSosial->approvedBy?->name,
                'approved_at' => $danaSosial->approved_at?->format('d M Y, H:i'),
                'catatan_approval' => $danaSosial->catatan_approval,
                
                // Verification info
                'verified_by_name' => $danaSosial->verifiedBy?->name,
                'verified_at' => $danaSosial->verified_at?->format('d M Y, H:i'),
                
                'created_at' => $danaSosial->created_at->format('d M Y, H:i'),
                'updated_at' => $danaSosial->updated_at->format('d M Y, H:i'),
            ]
        ]);
    }

    /**
     * Approve/Reject by Bidang Sosial
     */
    public function approve(Request $request, DanaSosial $danaSosial): JsonResponse
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        $isBidangSosial = $user->bidang_id == DanaSosial::BIDANG_SOSIAL_ID;
        
        // Only Bidang Sosial and Superadmin can approve
        if ($userRole !== 'superadmin' && !$isBidangSosial) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk approval.'
            ], 403);
        }
        
        if (!$danaSosial->canBeApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini tidak dapat di-approve.'
            ], 422);
        }
        
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan' => 'nullable|string|max:1000',
        ]);
        
        try {
            DB::beginTransaction();
            
            if ($validated['action'] === 'approve') {
                $danaSosial->update([
                    'status' => 'disetujui',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'catatan_approval' => $validated['catatan'],
                ]);
                $message = 'Pengajuan berhasil disetujui.';
            } else {
                $danaSosial->update([
                    'status' => 'ditolak',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'catatan_approval' => $validated['catatan'],
                ]);
                
                // Move to history
                $danaSosial->moveToHistory();
                $message = 'Pengajuan ditolak dan dipindahkan ke history.';
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify (mark as diserahkan) by Koorlap
     */
    public function verify(Request $request, DanaSosial $danaSosial): JsonResponse
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        $userKoorlap = $this->getUserKoorlap();
        
        // Only Koorlap (owner) and Superadmin can verify
        if ($userRole !== 'superadmin') {
            if (!$userKoorlap || $danaSosial->koorlap_id !== $userKoorlap->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk verifikasi.'
                ], 403);
            }
        }
        
        if (!$danaSosial->canBeVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini tidak dapat diverifikasi.'
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $danaSosial->update([
                'status' => 'diserahkan',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);
            
            // Move to history
            $danaSosial->moveToHistory();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Dana sosial berhasil diserahkan dan dipindahkan ke history.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display history
     */
    public function history(Request $request): View
    {
        if (!$this->checkAccess()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        $userKoorlap = $this->getUserKoorlap();
        $isBidangSosial = $user->bidang_id == DanaSosial::BIDANG_SOSIAL_ID;
        
        $perPage = $request->get('perPage', 20);
        
        $query = DanaSosialHistory::with(['koorlap.user', 'user', 'approvedBy', 'verifiedBy']);
        
        // Filter berdasarkan role
        if ($userRole === 'superadmin' || $isBidangSosial) {
            if ($request->filled('koorlap_id')) {
                $query->where('koorlap_id', $request->koorlap_id);
            }
        } else if ($userKoorlap) {
            $query->where('koorlap_id', $userKoorlap->id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Paginate
        if ($perPage === 'all') {
            $histories = $query->latest('completed_at')->get();
            $histories = new \Illuminate\Pagination\LengthAwarePaginator(
                $histories,
                $histories->count(),
                $histories->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $histories = $query->latest('completed_at')->paginate($perPage);
        }
        
        $histories->appends($request->query());
        
        // Get koorlaps for filter
        $koorlaps = collect();
        if ($userRole === 'superadmin' || $isBidangSosial) {
            $koorlaps = Koorlap::with('user')->orderBy('nama')->get();
        }
        
        return view('dana-sosial.history', compact(
            'histories',
            'koorlaps',
            'userKoorlap',
            'perPage'
        ));
    }

    /**
     * Get users by koorlap (for AJAX)
     */
    public function getUsersByKoorlap(Request $request): JsonResponse
    {
        $koorlapId = $request->get('koorlap_id');
        
        if (!$koorlapId) {
            return response()->json([
                'success' => false,
                'data' => []
            ]);
        }
        
        $users = User::where('koorlap_id', $koorlapId)
            ->with('bidang')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nik' => $user->nik,
                    'bidang' => $user->bidang->nama ?? '-',
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}