@extends('layouts.app')

@section('title', 'Pengajuan Budget')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <!-- Title & Buttons -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Pengajuan Budget</h1>
                @php
                    $userRole = Auth::user()->role->nama ?? '';
                @endphp
                @if(in_array($userRole, ['superadmin', 'sekretaris']))
                    <p class="text-gray-600 mt-1">
                        @if(isset($selectedBidangId) && $selectedBidangId !== 'all')
                            {{ $bidangs->find($selectedBidangId)->nama ?? 'Semua Bidang' }}
                        @else
                            Semua pengajuan budget dari seluruh bidang
                        @endif
                    </p>
                @else
                    <p class="text-gray-600 mt-1">{{ Auth::user()->bidang->nama }} - Kelola pengajuan budget bidang Anda</p>
                @endif
            </div>
            <div class="flex space-x-3">
                @if(in_array($userRole, ['admin', 'superadmin']))
                <button 
                    onclick="openCreateModal()"
                    class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Tambah Pengajuan Budget</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Pengajuan</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $allPengajuanBudgets->count() }}</p>
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
                            {{ $allPengajuanBudgets->where('status', 'draft')->count() }}
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
                            {{ $allPengajuanBudgets->whereIn('status', ['menunggu_konfirmasi_bendahara', 'menunggu_approval_ketua', 'menunggu_pencairan'])->count() }}
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Dicairkan -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Dicairkan</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">
                            {{ $allPengajuanBudgets->where('status', 'dicairkan')->count() }}
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
                    placeholder="Cari pengajuan budget..."
                    onkeyup="searchTable()"
                >
            </div>

            <!-- Filter Jenis -->
            <div class="w-full md:w-auto">
                <select id="filterJenis" onchange="filterByJenis()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">Semua Jenis</option>
                    <option value="program_kerja">Program Kerja</option>
                    <option value="pengajuan_budget">Pengajuan Budget</option>
                </select>
            </div>

            <div class="w-full md:w-auto">
                <select id="filterStatus" onchange="filterByStatus()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="menunggu_konfirmasi_bendahara">Menunggu Bendahara</option>
                    <option value="menunggu_approval_ketua">Menunggu Ketua</option>
                    <option value="menunggu_pencairan">Menunggu Pencairan</option>
                    <option value="dicairkan">Dicairkan</option>
                    <option value="ditolak_bendahara">Ditolak Bendahara</option>
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

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="pengajuanBudgetTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        @if(in_array($userRole, ['superadmin', 'sekretaris']))
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Pengajuan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Pengeluaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Anggaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="pengajuanBudgetTableBody">
                    @forelse($pengajuanBudgets as $index => $pb)
                        <tr class="hover:bg-gray-50 transition" data-status="{{ $pb->status }}" data-jenis="{{ $pb->jenis }}">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $pengajuanBudgets->firstItem() + $index }}
                            </td>
                            
                            @if(in_array($userRole, ['superadmin', 'sekretaris']))
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium text-dark">
                                    {{ $pb->bidang->nama }}
                                </span>
                            </td>
                            @endif

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pb->getJenisBadgeClass() }}">
                                    {{ $pb->jenis_label }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $pb->nama }}</div>
                                @if($pb->submitted_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Diajukan: {{ $pb->submitted_at->format('d M Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pb->jenis_pengeluaran)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pb->getJenisPengeluaranBadgeClass() }}">
                                        {{ $pb->jenis_pengeluaran }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($pb->anggaran, 0, ',', '.') }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pb->tanggal ? $pb->tanggal->format('d M Y') : '-' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pb->getStatusBadgeClass() }}">
                                    {{ $pb->status_label }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openDetailModal({{ $pb->id }})"
                                        class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                        title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    @if($pb->isDraft() && in_array($userRole, ['admin', 'superadmin']))
                                        <button onclick="openEditModal({{ $pb->id }})"
                                                class="bg-orange-500 text-white p-2 rounded-lg hover:bg-orange-600 transition"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button onclick="deletePengajuan({{ $pb->id }})"
                                                class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>

                                        <button onclick="submitPengajuan({{ $pb->id }})"
                                                class="bg-green-500 text-white p-2 rounded-lg hover:bg-green-600 transition"
                                                title="Ajukan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array($userRole, ['superadmin', 'sekretaris']) ? '9' : '8' }}" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada pengajuan budget</p>
                                <p class="text-gray-500 text-sm">Klik "Tambah Pengajuan Budget" untuk membuat</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajuanBudgets->hasPages() || $pengajuanBudgets->count() > 0)
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="text-sm text-gray-600 mb-3 md:mb-0">
                        @if($perPage === 'all')
                            Menampilkan <span class="font-semibold">{{ $pengajuanBudgets->total() }}</span> data
                        @else
                            Menampilkan <span class="font-semibold">{{ $pengajuanBudgets->firstItem() }}</span> 
                            sampai <span class="font-semibold">{{ $pengajuanBudgets->lastItem() }}</span> 
                            dari <span class="font-semibold">{{ $pengajuanBudgets->total() }}</span> data
                        @endif
                    </div>
                    
                    @if($perPage !== 'all')
                        {{ $pengajuanBudgets->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@if(in_array($userRole, ['admin', 'superadmin']))
@include('pengajuan-budget.create')
@include('pengajuan-budget.edit')
@include('pengajuan-budget.detail')
@endif

@endsection

@push('scripts')
<script>
    function changePerPage() {
        const perPage = document.getElementById('perPageSelect').value;
        const url = new URL(window.location.href);
        url.searchParams.set('perPage', perPage);
        
        @if(in_array($userRole, ['superadmin', 'sekretaris']))
        const bidangId = document.getElementById('filterBidang').value;
        if (bidangId !== 'all') {
            url.searchParams.set('bidang_id', bidangId);
        }
        @endif
        
        window.location.href = url.toString();
    }

    function filterByBidang() {
        const bidangId = document.getElementById('filterBidang').value;
        const url = new URL(window.location.href);
        
        if (bidangId === 'all') {
            url.searchParams.delete('bidang_id');
        } else {
            url.searchParams.set('bidang_id', bidangId);
        }
        
        const perPage = document.getElementById('perPageSelect').value;
        url.searchParams.set('perPage', perPage);
        
        window.location.href = url.toString();
    }

    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const tableBody = document.getElementById('pengajuanBudgetTableBody');
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

    function filterByJenis() {
        const filterValue = document.getElementById('filterJenis').value.toLowerCase();
        const tableBody = document.getElementById('pengajuanBudgetTableBody');
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const jenis = row.getAttribute('data-jenis');

            if (filterValue === '' || jenis === filterValue) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    function filterByStatus() {
        const filterValue = document.getElementById('filterStatus').value.toLowerCase();
        const tableBody = document.getElementById('pengajuanBudgetTableBody');
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
            const response = await fetch(`/pengajuan-budget/${id}`);
            const data = await response.json();
            
            if (data.success) {
                const pb = data.data;
                document.getElementById('editPengajuanId').value = pb.id;
                document.getElementById('editJenis').value = pb.jenis;
                document.getElementById('editNama').value = pb.nama;
                document.getElementById('editAnggaran').value = pb.anggaran;
                document.getElementById('editJenisPengeluaran').value = pb.jenis_pengeluaran;
                document.getElementById('editTahun').value = pb.tahun;
                document.getElementById('editTanggal').value = pb.tanggal;
                
                const editBidangId = document.getElementById('editBidangId');
                if (editBidangId) {
                    editBidangId.value = pb.bidang_id;
                }
                
                // Handle program kerja dropdown
                const wrapper = document.getElementById('editProgramKerjaWrapper');
                const programKerjaSelect = document.getElementById('editProgramKerjaId');
                
                if (pb.jenis === 'program_kerja') {
                    wrapper.classList.remove('hidden');
                    programKerjaSelect.setAttribute('required', 'required');
                    
                    // Update dropdown dengan data yang sudah ada
                    @if($userRole === 'superadmin')
                        updateProgramKerjaDropdown('edit', pb.bidang_id, pb.program_kerja_id);
                    @else
                        updateProgramKerjaDropdown('edit', {{ Auth::user()->bidang_id ?? 'null' }}, pb.program_kerja_id);
                    @endif
                } else {
                    wrapper.classList.add('hidden');
                    programKerjaSelect.removeAttribute('required');
                    programKerjaSelect.value = '';
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
            Swal.fire('Error!', 'Gagal memuat data pengajuan budget', 'error');
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
            const response = await fetch('/pengajuan-budget', {
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

    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);
        const id = document.getElementById('editPengajuanId').value;
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(`/pengajuan-budget/${id}`, {
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

    async function deletePengajuan(id) {
        const result = await Swal.fire({
            title: 'Yakin hapus?',
            text: "Pengajuan budget ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/pengajuan-budget/${id}`, {
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
                Swal.fire('Error!', 'Gagal menghapus pengajuan budget!', 'error');
            }
        }
    }

    async function submitPengajuan(id) {
        const result = await Swal.fire({
            title: 'Ajukan Pengajuan Budget?',
            text: "Pengajuan budget akan diajukan ke bendahara untuk dikonfirmasi",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ajukan!',
            cancelButtonText: 'Batal'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/pengajuan-budget/${id}/submit`, {
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
                Swal.fire('Error!', 'Gagal mengajukan pengajuan budget!', 'error');
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });

    // ========================================
    // Program Kerja Dropdown Functions
    // ========================================
    
    // Data program kerja dari controller
    const allProgramKerjas = @json($allProgramKerjas);

    // Filter program kerja berdasarkan bidang (atau semua jika superadmin)
    function getProgramKerjaByBidang(bidangId) {
        @if($userRole === 'superadmin')
            // Superadmin: jika tidak pilih bidang, tampilkan semua
            if (!bidangId) {
                return allProgramKerjas;
            }
            return allProgramKerjas.filter(pk => pk.bidang_id == bidangId);
        @else
            // Non-superadmin: filter by bidang
            if (!bidangId) return [];
            return allProgramKerjas.filter(pk => pk.bidang_id == bidangId);
        @endif
    }

    // Update dropdown program kerja
    function updateProgramKerjaDropdown(mode = 'create', bidangId = null, selectedId = null) {
        const select = document.getElementById(`${mode}ProgramKerjaId`);
        select.innerHTML = '<option value="">-- Pilih Program Kerja --</option>';
        
        const filteredData = getProgramKerjaByBidang(bidangId);
        
        filteredData.forEach(pk => {
            const option = document.createElement('option');
            option.value = pk.id;
            // Tampilkan nama bidang juga untuk superadmin
            @if($userRole === 'superadmin')
                const bidangNama = pk.bidang ? pk.bidang.nama : '';
                option.textContent = `[${bidangNama}] ${pk.nama} - Rp ${Number(pk.anggaran).toLocaleString('id-ID')}`;
            @else
                option.textContent = `${pk.nama} - Rp ${Number(pk.anggaran).toLocaleString('id-ID')}`;
            @endif
            option.dataset.nama = pk.nama;
            option.dataset.anggaran = pk.anggaran;
            option.dataset.jenisPengeluaran = pk.jenis_pengeluaran;
            option.dataset.tahun = pk.tahun;
            option.dataset.tanggal = pk.tanggal;
            option.dataset.bidangId = pk.bidang_id;
            
            if (selectedId && pk.id == selectedId) {
                option.selected = true;
            }
            
            select.appendChild(option);
        });
    }

    // Handle perubahan bidang (untuk superadmin)
    function onBidangChange(mode = 'create') {
        const bidangId = document.getElementById(`${mode}BidangId`).value;
        const jenis = document.getElementById(`${mode}Jenis`).value;
        
        if (jenis === 'program_kerja') {
            updateProgramKerjaDropdown(mode, bidangId);
        }
    }

    // Handle perubahan jenis
    function onJenisChange(mode = 'create') {
        const jenis = document.getElementById(`${mode}Jenis`).value;
        const wrapper = document.getElementById(`${mode}ProgramKerjaWrapper`);
        const programKerjaSelect = document.getElementById(`${mode}ProgramKerjaId`);
        
        if (jenis === 'program_kerja') {
            wrapper.classList.remove('hidden');
            programKerjaSelect.setAttribute('required', 'required');
            
            // Get bidang id
            @if($userRole === 'superadmin')
                const bidangIdEl = document.getElementById(`${mode}BidangId`);
                const bidangId = bidangIdEl ? bidangIdEl.value : null;
            @else
                const bidangId = {{ Auth::user()->bidang_id ?? 'null' }};
            @endif
            
            updateProgramKerjaDropdown(mode, bidangId);
        } else {
            wrapper.classList.add('hidden');
            programKerjaSelect.removeAttribute('required');
            programKerjaSelect.value = '';
        }
    }

    // Handle perubahan program kerja - auto fill form
    function onProgramKerjaChange(mode = 'create') {
        const select = document.getElementById(`${mode}ProgramKerjaId`);
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            // Auto fill form fields
            document.getElementById(`${mode}Nama`).value = selectedOption.dataset.nama || '';
            document.getElementById(`${mode}Anggaran`).value = selectedOption.dataset.anggaran || '';
            document.getElementById(`${mode}JenisPengeluaran`).value = selectedOption.dataset.jenisPengeluaran || '';
            document.getElementById(`${mode}Tahun`).value = selectedOption.dataset.tahun || '';
            document.getElementById(`${mode}Tanggal`).value = selectedOption.dataset.tanggal || '';
            
            // Auto fill bidang juga untuk superadmin
            @if($userRole === 'superadmin')
                const bidangIdEl = document.getElementById(`${mode}BidangId`);
                if (bidangIdEl && selectedOption.dataset.bidangId) {
                    bidangIdEl.value = selectedOption.dataset.bidangId;
                }
            @endif
        }
    }
    @endif
</script>
@endpush