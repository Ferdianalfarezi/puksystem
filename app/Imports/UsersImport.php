<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\Bidang;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    private $failures = [];

    public function model(array $row)
    {
        // Find role by name
        $role = Role::where('nama', $row['role'])->first();
        if (!$role) {
            throw new \Exception("Role '{$row['role']}' tidak ditemukan");
        }

        // Find bidang by name (optional)
        $bidang = null;
        if (!empty($row['bidang'])) {
            $bidang = Bidang::where('nama', $row['bidang'])->first();
        }

        return new User([
            'name' => $row['name'],
            'username' => $row['username'],
            'password' => Hash::make($row['password']),
            'role_id' => $role->id,
            'bidang_id' => $bidang ? $bidang->id : null,
            'status' => $row['status'] ?? 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'status' => 'nullable|in:active,not active',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role.required' => 'Role wajib diisi',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = $failures;
    }

    public function getFailures()
    {
        return $this->failures;
    }
}