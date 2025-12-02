@extends('layouts.app')

@section('title', 'History Kas Global')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">History Kas Global</h1>
            <p class="text-gray-600 mt-1">Semua transaksi kas masuk dan keluar</p>
        </div>
        
        @php
            $userRole = Auth::user()->role->nama ?? '';
            $canManageKas = in_array($userRole, ['superadmin', 'bendahara']);
        @endphp
        
        @if($canManageKas)
        <button onclick="openTambahKasModal()" 
                class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Kas</span>
        </button>
        @endif
    </div>

    <!-- Saldo Card -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                @if($year || $month)
                    <p class="text-sm font-medium text-green-100 mb-2">Saldo Periode</p>
                @else
                    <p class="text-sm font-medium text-green-100 mb-2">Saldo Kas Global</p>
                @endif
                <p class="text-5xl font-bold">
                    @if($saldoAkhir !== null)
                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                    @else
                        Rp {{ number_format($kasGlobal->saldo, 0, ',', '.') }}
                    @endif
                </p>
                @if($saldoAwal !== null && ($year || $month))
                    <p class="text-sm text-green-100 mt-3">
                        Saldo Awal: Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                    </p>
                @else
                    <p class="text-sm text-green-100 mt-3">
                        {{ $kasGlobal->saldo >= 0 ? '✓ Saldo Positif' : '⚠ Saldo Minus (Deficit)' }}
                    </p>
                @endif
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-6">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Transaksi -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalTransaksi }}</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Masuk -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Kas Masuk</p>
                    <p class="text-2xl font-bold text-green-600 mt-2">
                        Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Keluar -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Kas Keluar</p>
                    <p class="text-2xl font-bold text-red-600 mt-2">
                        Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-red-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-end">

                <!-- Filter Form -->
                <form method="GET" action="{{ route('kas.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 col-span-3">

                    <!-- Year Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                        <select name="year" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">Semua Tahun</option>
                            @foreach($availableYears as $availableYear)
                                <option value="{{ $availableYear }}" {{ $year == $availableYear ? 'selected' : '' }}>
                                    {{ $availableYear }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                        <select name="month" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">Semua Bulan</option>
                            @foreach($months as $monthNum => $monthName)
                                <option value="{{ $monthNum }}" {{ $month == $monthNum ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2 h-full items-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center space-x-2 w-full justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <span>Filter</span>
                        </button>

                        <a href="{{ route('kas.index') }}"
                            class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition flex items-center space-x-2 w-full justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Reset</span>
                        </a>
                    </div>
                </form>

                <!-- Export Button -->
                <div class="flex flex-col justify-end">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 invisible">Action</label>
                    <button onclick="exportExcel()"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition flex items-center space-x-2 w-full justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Export Excel</span>
                    </button>
                </div>
            </div>

            <!-- Active Filter Badge -->
            @if($year || $month)
            <div class="mt-4 flex items-center gap-2">
                <span class="text-sm text-gray-600">Menampilkan:</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                    @if($year && $month)
                        {{ $months[$month] }} {{ $year }}
                    @elseif($year)
                        Tahun {{ $year }}
                    @elseif($month)
                        Bulan {{ $months[$month] }}
                    @endif
                </span>
            </div>
            @endif
        </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Riwayat Transaksi</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <!-- ✅ TAMBAHKAN: Kolom Tipe -->
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Keterangan
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Saldo
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Dilakukan Oleh
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($histories as $history)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- Tanggal -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">
                                {{ $history->tanggal_transaksi->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $history->tanggal_transaksi->format('H:i') }}
                            </div>
                        </td>

                        <!-- Status (Masuk/Keluar) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($history->jenis === 'masuk')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                    </svg>
                                    Masuk
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                    </svg>
                                    Keluar
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $tipeLabel = 'Manual';
                                $tipeBadgeClass = 'bg-gray-100 text-gray-800';
                                
                                if ($history->referable_type === 'App\\Models\\Pencairan') {
                                    $tipeLabel = 'Program Kerja';
                                    $tipeBadgeClass = 'bg-indigo-100 text-indigo-800';
                                } elseif ($history->referable_type === 'App\\Models\\PencairanBudget') {
                                    $tipeLabel = 'Pengajuan Budget';
                                    $tipeBadgeClass = 'bg-emerald-100 text-emerald-800';
                                }
                            @endphp
                            
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $tipeBadgeClass }}">
                                @if($tipeLabel === 'Program Kerja')
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                @elseif($tipeLabel === 'Pengajuan Budget')
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                @endif
                                {{ $tipeLabel }}
                            </span>
                        </td>

                        <!-- Keterangan -->
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-md">
                                {{ Str::limit($history->keterangan, 60) }}
                            </div>
                            
                            <!-- ✅ UPDATE: Tampilkan Jenis Pengeluaran aja -->
                            @if($history->referable)
                                @php
                                    $jenisPengeluaran = null;
                                    
                                    // Cek referable type dan ambil jenis_pengeluaran
                                    if ($history->referable_type === 'App\\Models\\Pencairan' && $history->referable) {
                                        // Program Kerja
                                        $jenisPengeluaran = $history->referable->programKerja->jenis_pengeluaran ?? null;
                                    } elseif ($history->referable_type === 'App\\Models\\PencairanBudget' && $history->referable) {
                                        // Pengajuan Budget
                                        $jenisPengeluaran = $history->referable->pengajuanBudget->jenis_pengeluaran ?? null;
                                    }
                                @endphp
                                
                                @if($jenisPengeluaran)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $jenisPengeluaran }}
                                    </div>
                                @endif
                            @endif
                        </td>

                        <!-- Jumlah -->
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-bold {{ $history->jenis === 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $history->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($history->jumlah, 0, ',', '.') }}
                            </div>
                        </td>

                        <!-- Saldo Sesudah -->
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($history->saldo_sesudah, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                dari Rp {{ number_format($history->saldo_sebelum, 0, ',', '.') }}
                            </div>
                        </td>

                        <!-- Dilakukan Oleh -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-xs font-semibold text-gray-600">
                                        {{ substr($history->dilakukanOleh->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $history->dilakukanOleh->name ?? 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $history->dilakukanOleh->role->nama ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-600 mt-4 font-semibold">Belum ada transaksi</p>
                            <p class="text-gray-500 text-sm">
                                @if($year || $month)
                                    Tidak ada transaksi pada periode yang dipilih
                                @else
                                    Transaksi kas akan muncul di sini
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($histories->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Include Tambah Kas Modal -->
@if($canManageKas)
    @include('bendahara.tambah-kas-modal')
@endif

@endsection

@if($canManageKas)
@push('scripts')
<script src="{{ asset('js/tambah-kas.js') }}"></script>
@endpush
@endif

@push('scripts')
<script>
function exportExcel() {
    // Get current filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    const year = urlParams.get('year') || '';
    const month = urlParams.get('month') || '';
    
    // Build export URL with filters
    let exportUrl = '{{ route("kas.export") }}';
    const params = new URLSearchParams();
    
    if (year) params.append('year', year);
    if (month) params.append('month', month);
    
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }
    
    // Download file
    window.location.href = exportUrl;
}
</script>
@endpush