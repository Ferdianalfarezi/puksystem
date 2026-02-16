@extends('layouts.app')

@section('title', 'Koorlap Management')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Koordinator Lapangan</h1>
                <p class="text-gray-600 mt-1">Manage Koordinator Lapangan (Koorlap)</p>
            </div>
            <button 
                onclick="openCreateModal()"
                class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Add Koorlap</span>
            </button>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Koorlap</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $koorlaps->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Total Users Managed</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">{{ $koorlaps->sum(function($k) { return $k->users->count(); }) }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Koorlap</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User Account</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Users Managed</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($koorlaps as $index => $koorlap)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $koorlap->nama }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-black flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">
                                                {{ substr($koorlap->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $koorlap->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $koorlap->user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-black text-white">
                                    {{ $koorlap->user->role->nama ?? 'No Role' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $koorlap->users->count() }} users
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $koorlap->created_at->format('d M Y') }}
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openDetailModal({{ $koorlap->id }})"
                                            class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition">
                                        Detail
                                    </button>
                                    <button onclick="openEditModal({{ $koorlap->id }})"
                                            class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-orange-600 transition">
                                        Edit
                                    </button>
                                    <button onclick="deleteKoorlap({{ $koorlap->id }})"
                                            class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No Koorlap found</p>
                                <p class="text-gray-500 text-sm">Click "Add Koorlap" to create one</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($koorlaps->hasPages())
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                {{ $koorlaps->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
@include('koorlaps.create')

<!-- Edit Modal -->
@include('koorlaps.edit')
<!-- Detail Modal -->
@include('koorlaps.detail')
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        const modal = document.getElementById('createModal');
        document.getElementById('createForm').reset();
        clearErrors();
        $('#createUserId').val('').trigger('change');
        document.body.style.overflow = 'hidden';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        void modal.offsetWidth;
        requestAnimationFrame(() => modal.classList.add('active'));
    }

    async function openEditModal(id) {
        try {
            const response = await fetch(`/koorlaps/${id}`);
            const data = await response.json();
            
            if (data.success) {
                const koorlap = data.data;
                document.getElementById('editKoorlapId').value = koorlap.id;
                document.getElementById('editNama').value = koorlap.nama;
                $('#editUserId').val(koorlap.user_id).trigger('change');
                
                clearErrors();
                const modal = document.getElementById('editModal');
                document.body.style.overflow = 'hidden';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                void modal.offsetWidth;
                requestAnimationFrame(() => modal.classList.add('active'));
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Failed to load koorlap data', 'error');
        }
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('createForm').reset();
            clearErrors();
            $('#createUserId').val('').trigger('change');
            document.body.style.overflow = '';
        }, 250);
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('editForm').reset();
            clearErrors();
            $('#editUserId').val('').trigger('change');
            document.body.style.overflow = '';
        }, 250);
    }

    async function openDetailModal(id) {
    try {
        const response = await fetch(`/koorlaps/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const koorlap = data.data;
            
            // Set koorlap info
            document.getElementById('detailKoorlapNama').textContent = koorlap.nama;
            document.getElementById('detailUserName').textContent = koorlap.user.name;
            document.getElementById('detailUserUsername').textContent = '@' + koorlap.user.username;
            document.getElementById('detailUserInitial').textContent = koorlap.user.name.charAt(0).toUpperCase();
            
            // Calculate statistics
            const totalUsers = koorlap.users.length;
            const activeUsers = koorlap.users.filter(u => u.status === 'active').length;
            const inactiveUsers = totalUsers - activeUsers;
            
            document.getElementById('detailTotalUsers').textContent = totalUsers;
            document.getElementById('detailActiveUsers').textContent = activeUsers;
            document.getElementById('detailInactiveUsers').textContent = inactiveUsers;
            
            // Populate users table
            const tbody = document.getElementById('detailUsersTableBody');
            tbody.innerHTML = '';
            
            if (totalUsers === 0) {
                document.getElementById('detailEmptyState').classList.remove('hidden');
            } else {
                document.getElementById('detailEmptyState').classList.add('hidden');
                
                koorlap.users.forEach((user, index) => {
                    const statusClass = user.status === 'active' 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800';
                    
                    const row = `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-900">${index + 1}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-black flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">${user.name.charAt(0).toUpperCase()}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">${user.name}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">@${user.username}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-black text-white">
                                    ${user.role ? user.role.nama : 'No Role'}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">${user.bidang ? user.bidang.nama : 'No Bidang'}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                    ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                                </span>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            }
            
            // Open modal
            const modal = document.getElementById('detailModal');
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            void modal.offsetWidth;
            requestAnimationFrame(() => {
                modal.classList.add('active');
                const content = modal.querySelector('.modal-content');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            });
        }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to load koorlap detail', 'error');
            }
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            const content = modal.querySelector('.modal-content');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'active');
                document.body.style.overflow = '';
            }, 250);
        }

        // Update event listener untuk ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
                closeDetailModal();
            }
        });

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('#createUserId').select2({
            placeholder: "Select User",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#createModal')
        });

        $('#editUserId').select2({
            placeholder: "Select User",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#editModal')
        });
    });

    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();
        const formData = new FormData(this);

        try {
            const response = await fetch('/koorlaps', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                closeCreateModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => location.reload());
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-create-${key}`);
                        if (errorElement) errorElement.textContent = data.errors[key][0];
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });

    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();
        const formData = new FormData(this);
        const id = document.getElementById('editKoorlapId').value;
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(`/koorlaps/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                closeEditModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => location.reload());
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-edit-${key}`);
                        if (errorElement) errorElement.textContent = data.errors[key][0];
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });

    async function deleteKoorlap(id) {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "This koorlap will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/koorlaps/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });

                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete koorlap!', 'error');
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush