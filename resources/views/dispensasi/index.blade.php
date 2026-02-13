@extends('layouts.app')

@section('title', 'Dispensasi Aksi')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dispensasi Aksi</h1>
                @php
                    $userRole = Auth::user()->role->nama ?? '';
                @endphp
                @if($userRole === 'admin')
                    <p class="text-gray-600 mt-1">{{ Auth::user()->bidang->nama }} - Kelola dispensasi bidang Anda</p>
                @else
                    <p class="text-gray-600 mt-1">Kelola dispensasi untuk peserta aksi</p>
                @endif
            </div>
            @if(in_array($userRole, ['admin', 'superadmin']))
            <button 
                onclick="openCreateModal()"
                class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Tambah Dispensasi</span>
            </button>
            @endif
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Dispensasi</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $dispensasis->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Draft</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ $dispensasis->where('status', 'draft')->count() }}
                        </p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Proses Approval</p>
                        <p class="text-2xl font-bold text-yellow-900 mt-1">
                            {{ $dispensasis->whereIn('status', ['menunggu_approval_sekretaris', 'menunggu_approval_ketua'])->count() }}
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Disetujui</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">
                            {{ $dispensasis->where('status', 'approved')->count() }}
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
            <div class="w-full md:flex-1 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchInput"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Cari nama aksi atau tempat aksi..."
                    onkeyup="searchTable()"
                >
            </div>

            <div class="w-full md:w-auto">
                <select id="filterStatus" onchange="filterByStatus()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="menunggu_approval_sekretaris">Menunggu Sekretaris</option>
                    <option value="menunggu_approval_ketua">Menunggu Ketua</option>
                    <option value="approved">Disetujui</option>
                    <option value="ditolak_sekretaris">Ditolak Sekretaris</option>
                    <option value="ditolak_ketua">Ditolak Ketua</option>
                </select>
            </div>

            <div class="w-full md:w-auto">
                <select id="perPageSelect" onchange="changePerPage()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20 / halaman</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 / halaman</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 / halaman</option>
                    <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="dispensasiTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        @if($userRole === 'superadmin')
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Aksi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tempat & Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Peserta</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="dispensasiTableBody">
                    @forelse($dispensasis as $index => $dispensasi)
                        <tr class="hover:bg-gray-50 transition" data-status="{{ $dispensasi->status }}">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $dispensasis->firstItem() + $index }}
                            </td>
                            
                            @if($userRole === 'superadmin')
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium text-dark">
                                    {{ $dispensasi->bidang->nama }}
                                </span>
                            </td>
                            @endif
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $dispensasi->pengajuanBudget->nama_aksi }}</div>
                                <div class="text-xs text-gray-500 mt-1">Jam: {{ $dispensasi->pengajuanBudget->jam_aksi }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">📍 {{ $dispensasi->pengajuanBudget->tempat_aksi }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $dispensasi->pengajuanBudget->tanggal ? $dispensasi->pengajuanBudget->tanggal->format('d M Y') : '-' }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ count($dispensasi->user_ids ?? []) }} orang
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dispensasi->getStatusBadgeClass() }}">
                                    {{ $dispensasi->status_label }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Tombol Detail - selalu tampil -->
                                    <button onclick="openDetailModal({{ $dispensasi->id }})"
                                        class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                        title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    @if(in_array($userRole, ['admin', 'superadmin']))
                                        <!-- Tombol Edit - bisa edit kapan aja selama admin/superadmin -->
                                        <button onclick="openEditModal({{ $dispensasi->id }})"
                                            class="bg-orange-500 text-white p-2 rounded-lg hover:bg-orange-600 transition"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Tombol Hapus - cuma bisa hapus kalo draft -->
                                        @if($dispensasi->isDraft())
                                            <button onclick="deleteDispensasi({{ $dispensasi->id }})"
                                                class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif

                                        <!-- Tombol Submit - cuma bisa submit kalo masih draft -->
                                        @if($dispensasi->isDraft())
                                            <button onclick="submitDispensasi({{ $dispensasi->id }})"
                                                class="bg-green-500 text-white p-2 rounded-lg hover:bg-green-600 transition"
                                                title="Ajukan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        @endif
                                    @endif

                                    <!-- Tombol Cetak - cuma muncul kalo udah approved -->
                                    <!-- Tombol Cetak - cuma muncul kalo udah approved -->
@if($dispensasi->isApproved())
    <button onclick="openPrintPreview({{ $dispensasi->id }})"
        class="bg-purple-500 text-white p-2 rounded-lg hover:bg-purple-600 transition"
        title="Cetak">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
    </button>
@endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $userRole === 'superadmin' ? '7' : '6' }}" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada data dispensasi</p>
                                <p class="text-gray-500 text-sm">Klik "Tambah Dispensasi" untuk membuat</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dispensasis->hasPages() || $dispensasis->count() > 0)
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="text-sm text-gray-600 mb-3 md:mb-0">
                        @if($perPage === 'all')
                            Menampilkan <span class="font-semibold">{{ $dispensasis->total() }}</span> data
                        @else
                            Menampilkan <span class="font-semibold">{{ $dispensasis->firstItem() }}</span> 
                            sampai <span class="font-semibold">{{ $dispensasis->lastItem() }}</span> 
                            dari <span class="font-semibold">{{ $dispensasis->total() }}</span> data
                        @endif
                    </div>
                    
                    @if($perPage !== 'all')
                        {{ $dispensasis->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@if(in_array($userRole, ['admin', 'superadmin']))
@include('dispensasi.create')
@include('dispensasi.edit')
@endif
@include('dispensasi.detail')
@include('dispensasi.print-preview') 

@endsection
@push('scripts')
<script>
function changePerPage() {
    const perPage = document.getElementById('perPageSelect').value;
    const url = new URL(window.location.href);
    url.searchParams.set('perPage', perPage);
    window.location.href = url.toString();
}

function searchTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const tableBody = document.getElementById('dispensasiTableBody');
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

function filterByStatus() {
    const filterValue = document.getElementById('filterStatus').value.toLowerCase();
    const tableBody = document.getElementById('dispensasiTableBody');
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
    
    // Reset Choices.js
    if (window.createUserChoices) {
        window.createUserChoices.removeActiveItems();
    }
    
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
        const response = await fetch(`/dispensasi/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const disp = data.data;
            
            // Set form values
            document.getElementById('editDispensasiId').value = disp.id;
            document.getElementById('editPengajuanBudgetId').value = disp.pengajuan_budget.id;
            document.getElementById('editKeterangan').value = disp.keterangan || '';
            
            // Clear container dulu
            const container = document.getElementById('editPesertaContainer');
            container.innerHTML = '';
            
            // Populate peserta rows dari data yang ada
            if (disp.users && disp.users.length > 0) {
                disp.users.forEach(user => {
                    const userText = `${user.name} - ${user.nik || 'No NIK'} (${user.bidang_nama})`;
                    addEditPesertaRow(user.id, userText);
                });
            } else {
                // Jika tidak ada user, tambah 1 row kosong
                addEditPesertaRow();
            }
            
            clearErrors();
            
            // Show modal
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
        Swal.fire('Error!', 'Gagal memuat data', 'error');
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

document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();

    const formData = new FormData(this);

    try {
        const response = await fetch('/dispensasi', {
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
            } else {
                Swal.fire('Error!', data.message || 'Terjadi kesalahan!', 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
    }
});

document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();

    const formData = new FormData(this);
    const id = document.getElementById('editDispensasiId').value;
    formData.append('_method', 'PUT');

    try {
        const response = await fetch(`/dispensasi/${id}`, {
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
            } else {
                Swal.fire('Error!', data.message || 'Terjadi kesalahan!', 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
    }
});

async function deleteDispensasi(id) {
    const result = await Swal.fire({
        title: 'Yakin hapus?',
        text: "Data dispensasi ini akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/dispensasi/${id}`, {
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
            Swal.fire('Error!', 'Gagal menghapus data!', 'error');
        }
    }
}

async function submitDispensasi(id) {
    const result = await Swal.fire({
        title: 'Ajukan Dispensasi?',
        text: "Dispensasi akan diajukan ke sekretaris untuk di-review",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ajukan!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/dispensasi/${id}/submit`, {
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
            Swal.fire('Error!', 'Gagal mengajukan dispensasi!', 'error');
        }
    }
}

// Initialize Choices.js untuk multi-select
document.addEventListener('DOMContentLoaded', function() {
    const createUserSelect = document.getElementById('createUserIds');
    
    if (createUserSelect) {
        window.createUserChoices = new Choices(createUserSelect, {
            removeItemButton: true,
            searchEnabled: true,
            searchPlaceholderValue: 'Cari nama atau NIK...',
            noResultsText: 'Tidak ada hasil',
            itemSelectText: 'Tekan untuk pilih',
            maxItemCount: -1,
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Pilih peserta...',
        });
    }
    
    
});
@endif

async function openDetailModal(id) {
    try {
        const response = await fetch(`/dispensasi/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const disp = data.data;
            
            // Fill modal dengan data - CEK DULU ELEMENT-NYA ADA
            const elements = {
                detailNamaAksi: document.getElementById('detailNamaAksi'),
                detailTempatAksi: document.getElementById('detailTempatAksi'),
                detailTanggalAksi: document.getElementById('detailTanggalAksi'),
                detailJamAksi: document.getElementById('detailJamAksi'),
                detailBidang: document.getElementById('detailBidang'),
                detailKeterangan: document.getElementById('detailKeterangan'),
                detailCreatedAt: document.getElementById('detailCreatedAt'),
                detailLampiran: document.getElementById('detailLampiran'),
                detailUserList: document.getElementById('detailUserList'),
            };
            
            // Set values only if elements exist
            if (elements.detailNamaAksi) elements.detailNamaAksi.textContent = disp.pengajuan_budget.nama_aksi || '-';
            if (elements.detailTempatAksi) elements.detailTempatAksi.textContent = disp.pengajuan_budget.tempat_aksi || '-';
            if (elements.detailTanggalAksi) elements.detailTanggalAksi.textContent = disp.pengajuan_budget.tanggal || '-';
            if (elements.detailJamAksi) elements.detailJamAksi.textContent = disp.pengajuan_budget.jam_aksi || '-';
            if (elements.detailBidang) elements.detailBidang.textContent = disp.bidang_nama || '-';
            if (elements.detailKeterangan) elements.detailKeterangan.textContent = disp.keterangan || '-';
            if (elements.detailCreatedAt) elements.detailCreatedAt.textContent = disp.created_at_formatted || '-';
            
            // Lampiran
            if (elements.detailLampiran) {
                if (disp.pengajuan_budget.lampiran_url) {
                    elements.detailLampiran.innerHTML = `
                        <a href="${disp.pengajuan_budget.lampiran_url}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            Lihat Lampiran
                        </a>
                    `;
                } else {
                    elements.detailLampiran.textContent = '-';
                }
            }
            
            // User list
            if (elements.detailUserList) {
                elements.detailUserList.innerHTML = '';
                if (disp.users && disp.users.length > 0) {
                    disp.users.forEach(user => {
                        const li = document.createElement('li');
                        li.className = 'flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg';
                        li.innerHTML = `
                            <div>
                                <p class="font-medium text-gray-900">${user.name}</p>
                                <p class="text-xs text-gray-500">NIK: ${user.nik || '-'} | ${user.bidang_nama}</p>
                            </div>
                        `;
                        elements.detailUserList.appendChild(li);
                    });
                } else {
                    elements.detailUserList.innerHTML = '<li class="text-sm text-gray-500 text-center py-4">Tidak ada peserta</li>';
                }
            }
            
            // Show modal
            const modal = document.getElementById('detailModal');
            if (modal) {
                document.body.style.overflow = 'hidden';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                void modal.offsetWidth;
                requestAnimationFrame(() => {
                    modal.classList.add('active');
                });
            }
        } else {
            Swal.fire('Error!', data.message || 'Gagal memuat detail', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat detail', 'error');
    }
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }, 250);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        @if(in_array($userRole, ['admin', 'superadmin']))
        closeCreateModal();
        closeEditModal();
        @endif
        closeDetailModal();
        closePrintPreviewModal();
    }
});

// Print Preview Functions
function openPrintPreview(id) {
    const modal = document.getElementById('printPreviewModal');
    const iframe = document.getElementById('printPreviewIframe');
    
    // Load halaman print yang sudah ada ke iframe
    iframe.src = `/dispensasi/${id}/print`;
    
    // Show modal
    document.body.style.overflow = 'hidden';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        modal.querySelector('.scale-95').classList.remove('scale-95');
        modal.querySelector('.bg-white').classList.add('scale-100');
    }, 10);
}

function closePrintPreviewModal() {
    const modal = document.getElementById('printPreviewModal');
    const iframe = document.getElementById('printPreviewIframe');
    
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    
    const modalContent = modal.querySelector('.bg-white');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        iframe.src = '';
    }, 300);
}

function printFromIframe() {
    const iframe = document.getElementById('printPreviewIframe');
    iframe.contentWindow.print();
}
</script>
@endpush