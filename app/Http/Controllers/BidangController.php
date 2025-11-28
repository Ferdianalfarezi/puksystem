<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class BidangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $bidangs = Bidang::withCount('users')->paginate(10);
        return view('bidangs.index', compact('bidangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('bidangs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:bidangs,nama',
            'deskripsi' => 'nullable|string'
        ]);

        try {
            Bidang::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Bidang berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan bidang.',
                'errors' => ['general' => ['Terjadi kesalahan sistem.']]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bidang $bidang): JsonResponse
    {
        $bidang->load('users');
        
        return response()->json([
            'success' => true,
            'data' => $bidang
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bidang $bidang): View
    {
        return view('bidangs.edit', compact('bidang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bidang $bidang): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:bidangs,nama,' . $bidang->id,
            'deskripsi' => 'nullable|string'
        ]);

        try {
            $bidang->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Bidang berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui bidang.',
                'errors' => ['general' => ['Terjadi kesalahan sistem.']]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bidang $bidang): JsonResponse
    {
        // Check if bidang has users
        if ($bidang->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bidang tidak dapat dihapus karena masih memiliki user.'
            ], 400);
        }

        try {
            $bidang->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Bidang berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus bidang.'
            ], 500);
        }
    }
}