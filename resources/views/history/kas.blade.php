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

    <!-- 📊 Charts Section dengan Single Toggle Button -->
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-900">Analisis Pengeluaran</h2>
        <button onclick="toggleAllCharts()" 
                class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition text-gray-700 font-medium">
            <svg id="toggleIcon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <span id="toggleText">Sembunyikan Chart</span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Chart 1: Pengeluaran per Bidang -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden chart-card">
            <!-- Header -->
            <div class="px-6 py-4 ">
                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-600 rounded-lg p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Pengeluaran per Bidang</h3>
                        <p class="text-sm text-gray-600">
                            @if($year && $month)
                                {{ $months[$month] }} {{ $year }}
                            @elseif($year)
                                Tahun {{ $year }}
                            @else
                                Semua Periode
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Chart Content -->
            <div class="chart-content transition-all duration-300 ease-in-out overflow-hidden">
                <div class="px-6 py-6">
                    @if($pengeluaranPerBidang->count() > 0)
                        <div class="relative" style="height: 300px;">
                            <canvas id="chartBidang"></canvas>
                        </div>
                        
                        <!-- Legend/Summary -->
                        <div class="mt-6 space-y-2">
                            @foreach($pengeluaranPerBidang->take(5) as $bidang => $total)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ $bidang }}</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="text-gray-500 font-medium">Belum ada data pengeluaran</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Chart 2: Pengeluaran per Jenis -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden chart-card">
            <!-- Header -->
            <div class="px-6 py-4 ">
                <div class="flex items-center space-x-3">
                    <div class="bg-purple-600 rounded-lg p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Pengeluaran per Jenis</h3>
                        <p class="text-sm text-gray-600">
                            @if($year && $month)
                                {{ $months[$month] }} {{ $year }}
                            @elseif($year)
                                Tahun {{ $year }}
                            @else
                                Semua Periode
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Chart Content -->
            <div class="chart-content transition-all duration-300 ease-in-out overflow-hidden">
                <div class="px-6 py-6">
                    @if($pengeluaranPerJenis->count() > 0)
                        <div class="relative" style="height: 300px;">
                            <canvas id="chartJenis"></canvas>
                        </div>
                        
                        <!-- Legend/Summary -->
                        <div class="mt-6 space-y-2">
                            @foreach($pengeluaranPerJenis->take(5) as $jenis => $total)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ $jenis }}</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <p class="text-gray-500 font-medium">Belum ada data pengeluaran</p>
                        </div>
                    @endif
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Export Excel Function
function exportExcel() {
    const urlParams = new URLSearchParams(window.location.search);
    const year = urlParams.get('year') || '';
    const month = urlParams.get('month') || '';
    
    let exportUrl = '{{ route("kas.export") }}';
    const params = new URLSearchParams();
    
    if (year) params.append('year', year);
    if (month) params.append('month', month);
    
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }
    
    window.location.href = exportUrl;
}

// Global state untuk track chart visibility
let chartsExpanded = true;

// Toggle All Charts Function
function toggleAllCharts() {
    const chartContents = document.querySelectorAll('.chart-content');
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleText = document.getElementById('toggleText');
    
    chartsExpanded = !chartsExpanded;
    
    chartContents.forEach(content => {
        if (chartsExpanded) {
            // Expand
            content.style.maxHeight = content.scrollHeight + 'px';
            toggleIcon.style.transform = 'rotate(0deg)';
            toggleText.textContent = 'Sembunyikan Chart';
        } else {
            // Collapse
            content.style.maxHeight = '0px';
            toggleIcon.style.transform = 'rotate(-90deg)';
            toggleText.textContent = 'Tampilkan Chart';
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const chartContents = document.querySelectorAll('.chart-content');
    
    // Set initial expanded state
    chartContents.forEach(content => {
        content.style.maxHeight = content.scrollHeight + 'px';
    });
});

// Color palette
const colors = [
    'rgba(99, 102, 241, 0.8)',   // Indigo
    'rgba(16, 185, 129, 0.8)',   // Green
    'rgba(245, 158, 11, 0.8)',   // Orange
    'rgba(239, 68, 68, 0.8)',    // Red
    'rgba(168, 85, 247, 0.8)',   // Purple
    'rgba(59, 130, 246, 0.8)',   // Blue
    'rgba(236, 72, 153, 0.8)',   // Pink
    'rgba(14, 165, 233, 0.8)',   // Sky
    'rgba(34, 197, 94, 0.8)',    // Emerald
    'rgba(251, 146, 60, 0.8)',   // Orange
];

// Chart 1: Pengeluaran per Bidang
@if($pengeluaranPerBidang->count() > 0)
const ctxBidang = document.getElementById('chartBidang');
if (ctxBidang) {
    new Chart(ctxBidang, {
        type: 'pie',
        data: {
            labels: {!! json_encode($pengeluaranPerBidang->keys()) !!},
            datasets: [{
                data: {!! json_encode($pengeluaranPerBidang->values()) !!},
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 11
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}
@endif

// Chart 2: Pengeluaran per Jenis
@if($pengeluaranPerJenis->count() > 0)
const ctxJenis = document.getElementById('chartJenis');
if (ctxJenis) {
    new Chart(ctxJenis, {
        type: 'pie',
        data: {
            labels: {!! json_encode($pengeluaranPerJenis->keys()) !!},
            datasets: [{
                data: {!! json_encode($pengeluaranPerJenis->values()) !!},
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 11
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}
@endif
</script>
@endpush