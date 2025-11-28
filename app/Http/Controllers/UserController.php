<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::with(['role', 'bidang'])->paginate(10);
        $roles = Role::all();
        $bidangs = Bidang::all();
        
        return view('users.index', compact('users', 'roles', 'bidangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::all();
        $bidangs = Bidang::all();
        
        return view('users.create', compact('roles', 'bidangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:5'],
            'role_id' => ['required', 'exists:roles,id'],
            'bidang_id' => ['required', 'exists:bidangs,id'],
            'status' => ['required', 'in:active,not active']
        ], [
            'password.min' => 'Password minimal harus 5 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        try {
            User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'bidang_id' => $validated['bidang_id'],
                'status' => $validated['status'],
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user.',
                'errors' => ['general' => ['Terjadi kesalahan sistem.']]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['role', 'bidang']);
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        $bidangs = Bidang::all();
        
        return view('users.edit', compact('user', 'roles', 'bidangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'bidang_id' => ['required', 'exists:bidangs,id'],
            'status' => ['required', 'in:active,not active']
        ];

        $messages = [];

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', 'min:5'];
            $messages['password.min'] = 'Password minimal harus 5 karakter.';
            $messages['password.confirmed'] = 'Konfirmasi password tidak sesuai.';
        }

        $validated = $request->validate($rules, $messages);

        try {
            $updateData = [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'role_id' => $validated['role_id'],
                'bidang_id' => $validated['bidang_id'],
                'status' => $validated['status'],
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user.',
                'errors' => ['general' => ['Terjadi kesalahan sistem.']]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deleting current logged in user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus user yang sedang login.'
            ], 400);
        }

        try {
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user.'
            ], 500);
        }
    }
}