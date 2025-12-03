@extends('layouts.app')

@section('title', 'Approval Pengajuan Hutang - Ketua')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Approval Pengajuan Hutang</h1>
                <p class="text-gray-600 mt-1">Review dan approve pengajuan hutang yang telah dikonfirmasi bendahara</p>
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Menunggu -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Menunggu Approval</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $pengajuanHutang->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Nominal -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Total Nominal</p>
                        <p class="text-lg font-bold text-purple-900 mt-1">
                            Rp {{ number_format($pengajuanHutang->sum('jumlah'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Peminjam -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Peminjam</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">
                            {{ $pengajuanHutang->pluck('user_id')->unique()->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-start md:space-x-4 space-y-3 md:space-y-0">
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
                    placeholder="Cari pengajuan hutang..."
                    onkeyup="searchTable()"
                >
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keperluan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dikonfirmasi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="pengajuanHutangTableBody">
                    @forelse($pengajuanHutang as $index => $ph)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- No -->
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $pengajuanHutang->firstItem() + $index }}</td>
                            
                            <!-- Peminjam -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $ph->nama }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $ph->user->name }}</div>
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
                            
                            <!-- Dikonfirmasi Bendahara -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-600">
                                    {{ $ph->reviewedByBendahara->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $ph->reviewed_at_bendahara ? $ph->reviewed_at_bendahara->format('d M Y, H:i') : '-' }}
                                </div>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openDetailModalKetua({{ $ph->id }})"
                                            class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                            title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button onclick="openApproveModalKetua({{ $ph->id }}, '{{ $ph->nama }}')"
                                            class="bg-green-500 text-white p-2 rounded-lg hover:bg-green-600 transition"
                                            title="Setuju">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                    <button onclick="openRejectModalKetua({{ $ph->id }}, '{{ $ph->nama }}')"
                                            class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition"
                                            title="Tolak">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Tidak ada pengajuan hutang yang menunggu approval</p>
                                <p class="text-gray-500 text-sm">Semua pengajuan hutang sudah di-approve</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajuanHutang->hasPages())
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                {{ $pengajuanHutang->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Approve Modal -->
@include('ketua.hutang.approve-modal')

<!-- Reject Modal -->
@include('ketua.hutang.reject-modal')

<!-- Detail Modal -->
@include('ketua.hutang.detail')

@endsection

@push('scripts')
<script>
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

    let currentHutangId = null;
    let currentHutangName = '';

    function openApproveModalKetua(id, name) {
        currentHutangId = id;
        currentHutangName = name;
        
        document.getElementById('approveHutangNameKetua').textContent = name;
        document.getElementById('approveCatatanKetua').value = '';
        
        const modal = document.getElementById('approveModalKetua');
        document.body.style.overflow = 'hidden';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        void modal.offsetWidth;
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
    }

    function closeApproveModalKetua() {
        const modal = document.getElementById('approveModalKetua');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            currentHutangId = null;
            currentHutangName = '';
        }, 250);
    }

    function openRejectModalKetua(id, name) {
        currentHutangId = id;
        currentHutangName = name;
        
        document.getElementById('rejectHutangNameKetua').textContent = name;
        document.getElementById('rejectCatatanKetua').value = '';
        document.getElementById('error-reject-catatan-ketua').textContent = '';
        
        const modal = document.getElementById('rejectModalKetua');
        document.body.style.overflow = 'hidden';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        void modal.offsetWidth;
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
    }

    function closeRejectModalKetua() {
        const modal = document.getElementById('rejectModalKetua');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            currentHutangId = null;
            currentHutangName = '';
        }, 250);
    }

    document.getElementById('approveFormKetua').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        try {
            const response = await fetch(`/ketua/hutang/${currentHutangId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                closeApproveModalKetua();
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
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
        }
    });

    document.getElementById('rejectFormKetua').addEventListener('submit', async function(e) {
        e.preventDefault();
        document.getElementById('error-reject-catatan-ketua').textContent = '';

        const formData = new FormData(this);

        try {
            const response = await fetch(`/ketua/hutang/${currentHutangId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                closeRejectModalKetua();
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
                if (data.errors && data.errors.catatan) {
                    document.getElementById('error-reject-catatan-ketua').textContent = data.errors.catatan[0];
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeApproveModalKetua();
            closeRejectModalKetua();
        }
    });
</script>
@endpush