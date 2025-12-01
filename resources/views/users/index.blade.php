@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <!-- Title & Buttons -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Users</h1>
                <p class="text-gray-600 mt-1">Manage system users and their access</p>
            </div>
            <div class="flex space-x-3">
                <!-- Button Import Excel -->
                <button 
                    onclick="openImportModal()"
                    class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span>Import Excel</span>
                </button>
                
                <!-- Button Add User -->
                <button 
                    onclick="openCreateModal()"
                    class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Add User</span>
                </button>
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Users -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Users</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $users->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Active Users</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">{{ $users->where('status', 'active')->count() }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Inactive Users -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">Inactive Users</p>
                        <p class="text-2xl font-bold text-red-900 mt-1">{{ $users->where('status', 'not active')->count() }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Super Admins -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Admins</p>
                        <p class="text-2xl font-bold text-yellow-900 mt-1">{{ $users->whereIn('role.nama', ['Super Admin', 'Admin'])->count() }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-start md:space-x-4 space-y-3 md:space-y-0">
            <!-- Search Box -->
            <div class="w-full md:w-1/2 lg:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchInput"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Search users..."
                    onkeyup="searchTable()"
                >
            </div>

            <!-- Status Filter -->
            <div class="flex-shrink-0">
                <select 
                    id="statusFilter" 
                    onchange="filterByStatus()"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="not active">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="usersTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="usersTableBody">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-black flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $user->username }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-black text-white">
                                    {{ $user->role->nama ?? 'No Role' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $user->bidang->nama ?? 'No Bidang' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openEditModal({{ $user->id }})"
                                            class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-orange-600 transition">
                                        Edit
                                    </button>
                                    @if($user->id != Auth::id())
                                    <button onclick="deleteUser({{ $user->id }})"
                                            class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No users found</p>
                                <p class="text-gray-500 text-sm">Click "Add User" to create one</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Pagination -->
        @if($users->hasPages())
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Include Create Modal -->
@include('users.create')

<!-- Include Edit Modal -->
@include('users.edit')

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-250">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-250 scale-95 opacity-0 modal-content">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Import Users from Excel</h3>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="importForm" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <!-- Download Template Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-blue-900">Download template terlebih dahulu</p>
                            <p class="text-xs text-blue-700 mt-1">Gunakan template Excel untuk format yang benar</p>
                            <a href="{{ route('users.template') }}" class="inline-flex items-center mt-2 text-xs font-medium text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download Template
                            </a>
                        </div>
                    </div>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Upload File Excel <span class="text-red-500">*</span>
                    </label>
                    <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-500 transition">
                        <input 
                            type="file" 
                            name="file" 
                            id="excelFile"
                            accept=".xlsx,.xls,.csv"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            onchange="handleFileSelect(event)"
                        >
                        <div id="fileUploadContent">
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600 font-medium">
                                Click to upload or drag and drop
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                Excel files only (XLSX, XLS, CSV)
                            </p>
                        </div>
                        <div id="fileNameDisplay" class="hidden">
                            <svg class="w-12 h-12 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-900 font-medium" id="fileName"></p>
                            <button type="button" onclick="clearFile()" class="mt-2 text-xs text-red-600 hover:text-red-800">
                                Remove file
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-red-600 mt-1 error-message" id="error-import-file"></p>
                </div>

                <!-- Import Progress -->
                <div id="importProgress" class="hidden">
                    <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div id="progressBar" class="bg-green-600 h-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p class="text-xs text-gray-600 text-center mt-1">Importing data...</p>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button 
                    type="button" 
                    onclick="closeImportModal()"
                    class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Import Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Search functionality
    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const tableBody = document.getElementById('usersTableBody');
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().includes(searchInput)) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }
    }

    // Filter by status
    function filterByStatus() {
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const tableBody = document.getElementById('usersTableBody');
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const statusCell = row.cells[5]; // Status column
            
            if (statusFilter === '' || statusCell.textContent.toLowerCase().includes(statusFilter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    function openCreateModal() {
        const modal = document.getElementById('createModal');
        
        document.getElementById('createForm').reset();
        clearErrors();
        
        // Reset Select2
        $('#createRoleId').val('').trigger('change');
        $('#createBidangId').val('').trigger('change');
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Force reflow
        void modal.offsetWidth;
        
        // Trigger animation
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
    }

    async function openEditModal(id) {
        try {
            const response = await fetch(`/users/${id}`);
            const data = await response.json();
            
            if (data.success) {
                const user = data.data;
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editName').value = user.name;
                document.getElementById('editUsername').value = user.username;
                document.getElementById('editStatus').value = user.status;
                
                // Set Select2 values
                $('#editRoleId').val(user.role_id).trigger('change');
                $('#editBidangId').val(user.bidang_id).trigger('change');
                
                clearErrors();
                
                const modal = document.getElementById('editModal');
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Force reflow
                void modal.offsetWidth;
                
                // Trigger animation
                requestAnimationFrame(() => {
                    modal.classList.add('active');
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Failed to load user data', 'error');
        }
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        
        // Remove active class for fade out
        modal.classList.remove('active');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('createForm').reset();
            clearErrors();
            
            // Reset Select2
            $('#createRoleId').val('').trigger('change');
            $('#createBidangId').val('').trigger('change');
            
            // Restore body scroll
            document.body.style.overflow = '';
        }, 250);
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        
        // Remove active class for fade out
        modal.classList.remove('active');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('editForm').reset();
            clearErrors();
            
            // Reset Select2
            $('#editRoleId').val('').trigger('change');
            $('#editBidangId').val('').trigger('change');
            
            // Restore body scroll
            document.body.style.overflow = '';
        }, 250);
    }

    // Import Modal Functions
    function openImportModal() {
        const modal = document.getElementById('importModal');
        
        document.getElementById('importForm').reset();
        clearFile();
        clearErrors();
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Force reflow
        void modal.offsetWidth;
        
        // Trigger animation
        requestAnimationFrame(() => {
            modal.classList.add('active');
            const content = modal.querySelector('.modal-content');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        const content = modal.querySelector('.modal-content');
        
        // Remove active class for fade out
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex', 'active');
            document.getElementById('importForm').reset();
            clearFile();
            clearErrors();
            hideProgress();
            
            // Restore body scroll
            document.body.style.overflow = '';
        }, 250);
    }

    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('fileUploadContent').classList.add('hidden');
            document.getElementById('fileNameDisplay').classList.remove('hidden');
            document.getElementById('fileName').textContent = file.name;
        }
    }

    function clearFile() {
        document.getElementById('excelFile').value = '';
        document.getElementById('fileUploadContent').classList.remove('hidden');
        document.getElementById('fileNameDisplay').classList.add('hidden');
        document.getElementById('fileName').textContent = '';
    }

    function showProgress() {
        document.getElementById('importProgress').classList.remove('hidden');
        let progress = 0;
        const progressBar = document.getElementById('progressBar');
        
        const interval = setInterval(() => {
            progress += 10;
            progressBar.style.width = progress + '%';
            
            if (progress >= 90) {
                clearInterval(interval);
            }
        }, 200);
        
        return interval;
    }

    function hideProgress() {
        document.getElementById('importProgress').classList.add('hidden');
        document.getElementById('progressBar').style.width = '0%';
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // Initialize Select2
    document.addEventListener('DOMContentLoaded', function() {
        initializeSelect2();
    });

    function initializeSelect2() {
        // Create modal
        $('#createRoleId').select2({
            placeholder: "Select Role",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#createModal')
        });

        $('#createBidangId').select2({
            placeholder: "Select Bidang", 
            allowClear: false,
            width: '100%',
            dropdownParent: $('#createModal')
        });

        // Edit modal
        $('#editRoleId').select2({
            placeholder: "Select Role",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#editModal')
        });

        $('#editBidangId').select2({
            placeholder: "Select Bidang",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#editModal')
        });
    }

    // Submit Create Form
    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);

        try {
            const response = await fetch('/users', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
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
                }).then(() => {
                    location.reload();
                });
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-create-${key}`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[key][0];
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });

    // Submit Edit Form
    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);
        const id = document.getElementById('editUserId').value;
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(`/users/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
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
                }).then(() => {
                    location.reload();
                });
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-edit-${key}`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[key][0];
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });

    // Submit Import Form
    document.getElementById('importForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const fileInput = document.getElementById('excelFile');
        if (!fileInput.files[0]) {
            document.getElementById('error-import-file').textContent = 'Please select a file';
            return;
        }

        const formData = new FormData(this);
        const progressInterval = showProgress();

        try {
            const response = await fetch('/users/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            clearInterval(progressInterval);
            document.getElementById('progressBar').style.width = '100%';

            const data = await response.json();

            if (data.success) {
                hideProgress();
                closeImportModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                hideProgress();
                if (data.errors) {
                    let errorMessage = '';
                    if (Array.isArray(data.errors)) {
                        errorMessage = data.errors.join('\n');
                    } else {
                        errorMessage = data.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Failed!',
                        html: '<pre style="text-align: left; font-size: 12px;">' + errorMessage + '</pre>',
                        width: '600px'
                    });
                }
            }
        } catch (error) {
            clearInterval(progressInterval);
            hideProgress();
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });

    async function deleteUser(id) {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "This user will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete user!', 'error');
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeImportModal();
        }
    });
</script>
@endpush