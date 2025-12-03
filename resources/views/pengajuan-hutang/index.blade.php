@extends('layouts.app')

@section('title', 'Pengajuan Hutang')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <!-- Title & Buttons -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Pengajuan Hutang</h1>
                @php
                    $userRole = Auth::user()->role->nama ?? '';
                    $userBidangId = Auth::user()->bidang_id;
                @endphp
                <p class="text-gray-600 mt-1">Kelola pengajuan hutang karyawan</p>
            </div>
            <div class="flex space-x-3">
                <!-- Button Add Pengajuan Hutang -->
                @if(in_array($userRole, ['superadmin']) || ($userRole === 'admin' && $userBidangId == 4))
                <button 
                    onclick="openCreateModal()"
                    class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Tambah Pengajuan Hutang</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Pengajuan -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Pengajuan</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $allPengajuanHutang->count() }}</p>
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
                            {{ $allPengajuanHutang->where('status', 'draft')->count() }}
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
                            {{ $allPengajuanHutang->whereIn('status', ['menunggu_konfirmasi_bendahara', 'menunggu_approval_ketua', 'menunggu_pencairan'])->count() }}
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
                            {{ $allPengajuanHutang->where('status', 'dicairkan')->count() }}
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
            <!-- Search Box -->
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
                    placeholder="Cari pengajuan hutang..."
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
                    <option value="menunggu_pencairan">Menunggu Pencairan</option>
                    <option value="dicairkan">Dicairkan</option>
                    <option value="lunas">Lunas</option>
                    <option value="ditolak_bendahara">Ditolak Bendahara</option>
                    <option value="ditolak_ketua">Ditolak Ketua</option>
                </select>
            </div>

            <!-- Show Per Page -->
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
            <table class="w-full" id="pengajuanHutangTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Peminjam</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sisa Hutang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keperluan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="pengajuanHutangTableBody">
                    @forelse($pengajuanHutang as $index => $ph)
                        <tr class="hover:bg-gray-50 transition" data-status="{{ $ph->status }}">
                            <!-- No -->
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $pengajuanHutang->firstItem() + $index }}
                            </td>
                            
                            <!-- Peminjam -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $ph->nama }}</div>
                                @if($ph->submitted_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Diajukan: {{ $ph->submitted_at->format('d M Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Bidang -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium text-dark">
                                    {{ $ph->bidang->nama }}
                                </span>
                            </td>
                            
                            <!-- Jumlah -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($ph->jumlah, 0, ',', '.') }}
                                </div>
                            </td>
                            
                            <!-- Sisa Hutang -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-red-600">
                                    Rp {{ number_format($ph->sisa_hutang, 0, ',', '.') }}
                                </div>
                                @if($ph->status === 'dicairkan' && $ph->sisa_hutang > 0)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ number_format($ph->persen_lunas, 1) }}% lunas
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Keperluan -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 truncate max-w-xs" title="{{ $ph->keperluan }}">
                                    {{ Str::limit($ph->keperluan, 50) }}
                                </div>
                            </td>
                            
                            <!-- Tanggal -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ph->tanggal ? $ph->tanggal->format('d M Y') : '-' }}
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Draft'],
                                        'menunggu_konfirmasi_bendahara' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu Bendahara'],
                                        'menunggu_approval_ketua' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Menunggu Ketua'],
                                        'menunggu_pencairan' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Menunggu Pencairan'],
                                        'dicairkan' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Dicairkan'],
                                        'lunas' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Lunas'],
                                        'ditolak_bendahara' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Ditolak Bendahara'],
                                        'ditolak_ketua' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Ditolak Ketua'],
                                    ];
                                    $config = $statusConfig[$ph->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => $ph->status];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Detail Button -->
                                    <button onclick="openDetailModal({{ $ph->id }})"
                                        class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                        title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    @if($ph->isDraft())
                                        <!-- Edit Button -->
                                        <button onclick="openEditModal({{ $ph->id }})"
                                                class="bg-orange-500 text-white p-2 rounded-lg hover:bg-orange-600 transition"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <button onclick="deleteHutang({{ $ph->id }})"
                                                class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>

                                        <!-- Submit Button -->
                                        <button onclick="submitHutang({{ $ph->id }})"
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
                            <td colspan="9" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada pengajuan hutang</p>
                                <p class="text-gray-500 text-sm">Klik "Tambah Pengajuan Hutang" untuk membuat</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Pagination -->
        @if($pengajuanHutang->hasPages() || $pengajuanHutang->count() > 0)
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="text-sm text-gray-600 mb-3 md:mb-0">
                        @if($perPage === 'all')
                            Menampilkan <span class="font-semibold">{{ $pengajuanHutang->total() }}</span> data
                        @else
                            Menampilkan <span class="font-semibold">{{ $pengajuanHutang->firstItem() }}</span> 
                            sampai <span class="font-semibold">{{ $pengajuanHutang->lastItem() }}</span> 
                            dari <span class="font-semibold">{{ $pengajuanHutang->total() }}</span> data
                        @endif
                    </div>
                    
                    @if($perPage !== 'all')
                        {{ $pengajuanHutang->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Include Modals -->
@include('pengajuan-hutang.create')
@include('pengajuan-hutang.edit')
@include('pengajuan-hutang.detail')

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
        const tableBody = document.getElementById('pengajuanHutangTableBody');
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
        const tableBody = document.getElementById('pengajuanHutangTableBody');
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

    async function deleteHutang(id) {
        const result = await Swal.fire({
            title: 'Yakin hapus?',
            text: "Pengajuan hutang ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/pengajuan-hutang/${id}`, {
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
                Swal.fire('Error!', 'Gagal menghapus pengajuan hutang!', 'error');
            }
        }
    }

    async function submitHutang(id) {
        const result = await Swal.fire({
            title: 'Ajukan Pengajuan Hutang?',
            text: "Pengajuan hutang akan diajukan ke bendahara untuk dikonfirmasi",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ajukan!',
            cancelButtonText: 'Batal'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/pengajuan-hutang/${id}/submit`, {
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
                Swal.fire('Error!', 'Gagal mengajukan pengajuan hutang!', 'error');
            }
        }
    }
</script>
@endpush