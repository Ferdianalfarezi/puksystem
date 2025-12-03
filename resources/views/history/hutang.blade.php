@extends('layouts.app')

@section('title', 'History Hutang Lunas')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <!-- Title & Info -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">History Hutang Lunas</h1>
                <p class="text-gray-600 mt-1">Riwayat hutang yang telah lunas dibayar</p>
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Lunas -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Hutang Lunas</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">{{ $hutangLunas->total() }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Nominal Hutang -->
            @php
                $totalHutang = $hutangLunas->sum('jumlah');
            @endphp
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="w-full">
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Hutang</p>
                        <p class="text-lg font-bold text-blue-900 mt-1">
                            Rp {{ number_format($totalHutang, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Pembayaran -->
            @php
                $totalPembayaran = 0;
                foreach($hutangLunas as $hl) {
                    $totalPembayaran += $hl->pembayaran->count();
                }
            @endphp
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="w-full">
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Total Transaksi</p>
                        <p class="text-2xl font-bold text-purple-900 mt-1">
                            {{ $totalPembayaran }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Peminjam Unik -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="w-full">
                        <p class="text-xs font-semibold text-orange-600 uppercase tracking-wider">Peminjam</p>
                        <p class="text-2xl font-bold text-orange-900 mt-1">
                            {{ $hutangLunas->pluck('user_id')->unique()->count() }}
                        </p>
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
                    placeholder="Cari hutang lunas..."
                    onkeyup="searchTable()"
                >
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
            <table class="w-full" id="hutangTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Peminjam</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Hutang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Cicilan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Peminjaman</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keperluan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="hutangTableBody">
                    @forelse($hutangLunas as $index => $hl)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- No -->
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $hutangLunas->firstItem() + $index }}
                            </td>
                            
                            <!-- Peminjam -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $hl->nama }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $hl->user->name }}</div>
                            </td>
                            
                            <!-- Bidang -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $hl->bidang->nama }}
                                </span>
                            </td>
                            
                            <!-- Jumlah Hutang -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($hl->jumlah, 0, ',', '.') }}
                                </div>
                            </td>
                            
                            <!-- Total Cicilan -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        {{ $hl->pembayaran->count() }}x Cicilan
                                    </span>
                                </div>
                            </td>
                            
                            <!-- Tanggal Peminjaman -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $hl->tanggal ? $hl->tanggal->format('d M Y') : '-' }}
                            </td>
                            
                            <!-- Keperluan -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 truncate max-w-xs" title="{{ $hl->keperluan }}">
                                    {{ Str::limit($hl->keperluan, 50) }}
                                </div>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center">
                                    <button onclick="openDetailHistoryModal({{ $hl->id }})"
                                        class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                        title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada hutang yang lunas</p>
                                <p class="text-gray-500 text-sm">Hutang yang sudah lunas akan tampil di sini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Pagination -->
        @if($hutangLunas->hasPages() || $hutangLunas->count() > 0)
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="text-sm text-gray-600 mb-3 md:mb-0">
                        @if($perPage === 'all')
                            Menampilkan <span class="font-semibold">{{ $hutangLunas->total() }}</span> data
                        @else
                            Menampilkan <span class="font-semibold">{{ $hutangLunas->firstItem() }}</span> 
                            sampai <span class="font-semibold">{{ $hutangLunas->lastItem() }}</span> 
                            dari <span class="font-semibold">{{ $hutangLunas->total() }}</span> data
                        @endif
                    </div>
                    
                    @if($perPage !== 'all')
                        {{ $hutangLunas->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Include Detail Modal -->
@include('history.hutang-detail')

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
        const tableBody = document.getElementById('hutangTableBody');
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
</script>
@endpush