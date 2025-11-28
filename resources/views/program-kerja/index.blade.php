@extends('layouts.app')

@section('title', 'Program Kerja')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <!-- Title & Buttons -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Program Kerja</h1>
                @php
                    $userRole = Auth::user()->role->nama ?? '';
                @endphp
                @if(in_array($userRole, ['superadmin', 'sekretaris']))
                    <p class="text-gray-600 mt-1">
                        @if(isset($selectedBidangId) && $selectedBidangId !== 'all')
                            {{ $bidangs->find($selectedBidangId)->nama ?? 'Semua Bidang' }}
                        @else
                            Semua program kerja dari seluruh bidang
                        @endif
                    </p>
                @else
                    <p class="text-gray-600 mt-1">{{ Auth::user()->bidang->nama }} - Kelola program kerja bidang Anda</p>
                @endif
            </div>
            <div class="flex space-x-3">
                <!-- Button Add Program Kerja - Hanya untuk Admin dan Super Admin -->
                @if(in_array($userRole, ['admin', 'superadmin']))
                <button 
                    onclick="openCreateModal()"
                    class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Tambah Program Kerja</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Program -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Program</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $programKerjas->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Draft -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Draft</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ $programKerjas->where('status', 'draft')->count() }}
                        </p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Proses Approval -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Proses Approval</p>
                        <p class="text-2xl font-bold text-yellow-900 mt-1">
                            {{ $programKerjas->whereIn('status', ['menunggu_konfirmasi_bendahara', 'menunggu_approval_ketua'])->count() }}
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Disetujui -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Disetujui</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">
                            {{ $programKerjas->where('status', 'disetujui')->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between md:space-x-4 space-y-3 md:space-y-0">
            <!-- Filter Bidang (Hanya untuk Superadmin & Sekretaris) -->
            @if(in_array($userRole, ['superadmin', 'sekretaris']))
            <div class="w-full md:w-auto">
                <select id="filterBidang" onchange="filterByBidang()" class="w-full md:w-64 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="all" {{ (isset($selectedBidangId) && $selectedBidangId === 'all') ? 'selected' : '' }}>
                        📋 Semua Bidang
                    </option>
                    @foreach($bidangs as $bidang)
                        <option value="{{ $bidang->id }}" {{ (isset($selectedBidangId) && $selectedBidangId == $bidang->id) ? 'selected' : '' }}>
                            {{ $bidang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

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
                    placeholder="Cari program kerja..."
                    onkeyup="searchTable()"
                >
            </div>

            <!-- Filter Status -->
            <div class="w-full md:w-auto">
                <select id="filterStatus" onchange="filterByStatus()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="menunggu_konfirmasi_bendahara">Menunggu Bendahara</option>
                    <option value="menunggu_approval_ketua">Menunggu Ketua</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak_bendahara">Ditolak Bendahara</option>
                    <option value="ditolak_ketua">Ditolak Ketua</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="programKerjaTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        @if(in_array($userRole, ['superadmin', 'sekretaris']))
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Program</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Anggaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="programKerjaTableBody">
                    @forelse($programKerjas as $index => $pk)
                        <tr class="hover:bg-gray-50 transition" data-status="{{ $pk->status }}">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $programKerjas->firstItem() + $index }}</td>
                            @if(in_array($userRole, ['superadmin', 'sekretaris']))
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-black text-white">
                                    {{ $pk->bidang->nama }}
                                </span>
                            </td>
                            @endif
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $pk->nama }}</div>
                                @if($pk->submitted_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Diajukan: {{ $pk->submitted_at->format('d M Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($pk->anggaran, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pk->tahun }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Draft'],
                                        'menunggu_konfirmasi_bendahara' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu Bendahara'],
                                        'menunggu_approval_ketua' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Menunggu Ketua'],
                                        'disetujui' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Disetujui'],
                                        'ditolak_bendahara' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Ditolak Bendahara'],
                                        'ditolak_ketua' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Ditolak Ketua'],
                                    ];
                                    $config = $statusConfig[$pk->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => $pk->status];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Detail Button -->
                                    <a href="{{ route('program-kerja.show', $pk->id) }}"
                                       class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition">
                                        Detail
                                    </a>

                                    @if($pk->isDraft() && in_array($userRole, ['admin', 'superadmin']))
                                        <!-- Edit Button -->
                                        <button onclick="openEditModal({{ $pk->id }})"
                                                class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-orange-600 transition">
                                            Edit
                                        </button>

                                        <!-- Delete Button -->
                                        <button onclick="deleteProgram({{ $pk->id }})"
                                                class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                            Hapus
                                        </button>

                                        <!-- Submit Button -->
                                        <button onclick="submitProgram({{ $pk->id }})"
                                                class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 transition">
                                            Ajukan
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array($userRole, ['superadmin', 'sekretaris']) ? '7' : '6' }}" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada program kerja</p>
                                <p class="text-gray-500 text-sm">Klik "Tambah Program Kerja" untuk membuat</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Pagination -->
        @if($programKerjas->hasPages())
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                {{ $programKerjas->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Include Create Modal -->
@if(in_array($userRole, ['admin', 'superadmin']))
@include('program-kerja.create')
@include('program-kerja.edit')
@endif

@endsection

@push('scripts')
<script>
    // Filter by Bidang
    function filterByBidang() {
        const bidangId = document.getElementById('filterBidang').value;
        const url = new URL(window.location.href);
        
        if (bidangId === 'all') {
            url.searchParams.delete('bidang_id');
        } else {
            url.searchParams.set('bidang_id', bidangId);
        }
        
        window.location.href = url.toString();
    }

    // Search functionality
    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const tableBody = document.getElementById('programKerjaTableBody');
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
        const filterValue = document.getElementById('filterStatus').value.toLowerCase();
        const tableBody = document.getElementById('programKerjaTableBody');
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const status = row.getAttribute('data-status');

            if (filterValue === '' || status === filterValue) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    @if(in_array($userRole, ['admin', 'superadmin']))
    function openCreateModal() {
        const modal = document.getElementById('createModal');
        
        document.getElementById('createForm').reset();
        clearErrors();
        
        document.body.style.overflow = 'hidden';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        void modal.offsetWidth;
        
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
    }

    async function openEditModal(id) {
    try {
        const response = await fetch(`/program-kerja/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const pk = data.data;
            document.getElementById('editProgramId').value = pk.id;
            document.getElementById('editNama').value = pk.nama;
            document.getElementById('editAnggaran').value = pk.anggaran;
            document.getElementById('editTahun').value = pk.tahun;
            
            // Set bidang_id kalau superadmin
            const editBidangId = document.getElementById('editBidangId');
            if (editBidangId) {
                editBidangId.value = pk.bidang_id;
            }
            
            clearErrors();
            
            const modal = document.getElementById('editModal');
            
            document.body.style.overflow = 'hidden';
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            void modal.offsetWidth;
            
            requestAnimationFrame(() => {
                modal.classList.add('active');
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat data program kerja', 'error');
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
            
            document.body.style.overflow = '';
        }, 250);
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // Submit Create Form
    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);

        try {
            const response = await fetch('/program-kerja', {
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
                    title: 'Berhasil!',
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
            Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
        }
    });

    // Submit Edit Form
    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);
        const id = document.getElementById('editProgramId').value;
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(`/program-kerja/${id}`, {
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
                    title: 'Berhasil!',
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
            Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
        }
    });

    async function deleteProgram(id) {
        const result = await Swal.fire({
            title: 'Yakin hapus?',
            text: "Program kerja ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/program-kerja/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
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
                Swal.fire('Error!', 'Gagal menghapus program kerja!', 'error');
            }
        }
    }

    async function submitProgram(id) {
        const result = await Swal.fire({
            title: 'Ajukan Program Kerja?',
            text: "Program kerja akan diajukan ke bendahara untuk dikonfirmasi",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ajukan!',
            cancelButtonText: 'Batal'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/program-kerja/${id}/submit`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
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
                Swal.fire('Error!', 'Gagal mengajukan program kerja!', 'error');
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
    @endif
</script>
@endpush