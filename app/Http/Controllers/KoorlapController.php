<?php

namespace App\Http\Controllers;

use App\Models\Koorlap;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class KoorlapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // ✅ Tambahkan eager loading untuk user.role dan user.bidang
        $koorlaps = Koorlap::with(['user.role', 'user.bidang', 'users'])->paginate(10);
        $users = User::all();
        
        return view('koorlaps.index', compact('koorlaps', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id', 'unique:koorlaps,user_id'],
        ], [
            'user_id.unique' => 'User ini sudah terdaftar sebagai Koorlap.',
        ]);

        try {
            Koorlap::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Koorlap berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Koorlap.',
                'errors' => ['general' => ['Terjadi kesalahan sistem.']]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Koorlap $koorlap): JsonResponse
    {
        // ✅ Tambahkan eager loading untuk user.role dan user.bidang di users juga
        $koorlap->load(['user.role', 'user.bidang', 'users.role', 'users.bidang']);
        
        return response()->json([
            'success' => true,
            'data' => $koorlap
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Koorlap $koorlap): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id', 'unique:koorlaps,user_id,' . $koorlap->id],
        ], [
            'user_id.unique' => 'User ini sudah terdaftar sebagai Koorlap.',
        ]);

        try {
            $koorlap->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Koorlap berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Koorlap.',
                'errors' => ['general' => ['Terjadi kesalahan sistem.']]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Koorlap $koorlap): JsonResponse
    {
        try {
            $koorlap->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Koorlap berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Koorlap.'
            ], 500);
        }
    }
}