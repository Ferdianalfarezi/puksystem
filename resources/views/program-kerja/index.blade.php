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
                            <p class="text-2xl font-bold text-blue-900 mt-1">{{ $allProgramKerjas->count() }}</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Draft - tetap pakai $allProgramKerjas -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Draft</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $allProgramKerjas->where('status', 'draft')->count() }}
                            </p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Proses Approval - tetap pakai $allProgramKerjas -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Proses Approval</p>
                            <p class="text-2xl font-bold text-yellow-900 mt-1">
                                {{ $allProgramKerjas->whereIn('status', ['menunggu_konfirmasi_bendahara', 'menunggu_approval_ketua', 'menunggu_pencairan'])->count() }}
                            </p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Dicairkan - tetap pakai $allProgramKerjas -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Dicairkan</p>
                            <p class="text-2xl font-bold text-green-900 mt-1">
                                {{ $allProgramKerjas->where('status', 'dicairkan')->count() }}
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
    
    <!-- ===== GANTT CHART KHUSUS STATUS DICAIRKAN ===== -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        
        <!-- Header dengan Toggle View -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <h2 class="text-lg font-bold text-gray-900">Kalender Program Kerja (Disetujui)</h2>
            
            <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                <!-- Month Selector (Hanya untuk View Bulanan) -->
                <div id="monthSelector" class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600 font-medium">Bulan:</label>
                    <select id="selectMonth" onchange="changeMonth()" 
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                    <select id="selectYear" onchange="changeMonth()" 
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                        @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                
                <!-- Toggle Bulanan/Tahunan -->
                <div class="flex items-center space-x-2">
                    <button id="btnMonthly" onclick="switchView('monthly')" 
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition bg-black text-white">
                        Bulanan
                    </button>
                    <button id="btnYearly" onclick="switchView('yearly')" 
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Tahunan
                    </button>
                </div>
            </div>
        </div>

        @php
            $dicairkan = $allProgramKerjas->filter(fn($p) => $p->status === 'dicairkan');
            $currentMonth = now()->month;
            $currentYear = now()->year;
        @endphp

        @php
            $colorMap = [
                'Bidang Organisasi'       => '#3b82f6',
                'Bidang Pendidikan'       => '#6366f1',
                'Bidang Hubungan Industrial' => '#10b981',
                'Bidang Sosial Ekonomi'   => '#f59e0b',
                'Bidang Upah dan Bonus'   => '#ef4444',
                'Bidang Umum'             => '#8b5cf6',
                'Ketua'                   => '#14b8a6',
                'Bendahara'               => '#ec4899',
                'Sekretaris'              => '#a3e635',
            ];
        @endphp

        @php
            // ✅ Deklarasi monthNames di awal
            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];

            // Hitung Planning dan Actual untuk tahun sekarang (untuk backward compatibility)
            $planning = array_fill(1, 12, 0);
            $actual = array_fill(1, 12, 0);
            $planningBudget = array_fill(1, 12, 0);
            $actualBudget = array_fill(1, 12, 0);

            foreach ($allProgramKerjas as $pk) {
                $pkDate = \Carbon\Carbon::parse($pk->tanggal);
                
                // Hanya hitung untuk tahun sekarang
                if ($pkDate->year == now()->year) {
                    $month = $pkDate->month;
                    
                    // Planning: semua data KECUALI draft
                    if ($pk->status !== 'draft') {
                        $planning[$month]++;
                        $planningBudget[$month] += $pk->anggaran;
                    }
                    
                    // Actual: hanya yang dicairkan
                    if ($pk->status === 'dicairkan') {
                        $actual[$month]++;
                        $actualBudget[$month] += $pk->anggaran;
                    }
                }
            }

            // ✅ Siapkan data untuk semua tahun (untuk chart dengan dropdown tahun)
            $yearRange = range(now()->year - 2, now()->year + 2);
            $chartDataByYear = [];
            
            foreach ($yearRange as $year) {
                $planningYear = array_fill(1, 12, 0);
                $actualYear = array_fill(1, 12, 0);
                $planningBudgetYear = array_fill(1, 12, 0);
                $actualBudgetYear = array_fill(1, 12, 0);
                
                foreach ($allProgramKerjas as $pk) {
                    $pkYear = \Carbon\Carbon::parse($pk->tanggal)->year;
                    
                    if ($pkYear == $year) {
                        $month = \Carbon\Carbon::parse($pk->tanggal)->month;
                        
                        // Planning: semua data KECUALI draft
                        if ($pk->status !== 'draft') {
                            $planningYear[$month]++;
                            $planningBudgetYear[$month] += $pk->anggaran;
                        }
                        
                        // Actual: hanya yang dicairkan
                        if ($pk->status === 'dicairkan') {
                            $actualYear[$month]++;
                            $actualBudgetYear[$month] += $pk->anggaran;
                        }
                    }
                }
                
                $chartDataByYear[$year] = [
                    'planning' => array_values($planningYear),
                    'actual' => array_values($actualYear),
                    'planningBudget' => array_values($planningBudgetYear),
                    'actualBudget' => array_values($actualBudgetYear),
                ];
            }
        @endphp

        <!-- VIEW BULANAN -->
        <div id="monthlyView">
            @php
                $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            @endphp
            <p id="monthLabel" class="text-sm text-gray-600 mb-3">📅 {{ $bulanIndo[now()->month] }} {{ now()->year }}</p>
            
            <!-- Container untuk grid yang akan di-update -->
            <div id="monthlyGridContainer">
                @php
                    $daysInMonth = now()->daysInMonth;
                @endphp
                
                <!-- Header Kalender Bulanan -->
                <div class="grid gap-1" id="monthlyHeader" style="grid-template-columns: repeat({{ $daysInMonth }}, 1fr);">
                    @for($d=1; $d <= $daysInMonth; $d++)
                        <div class="text-xs text-center text-gray-500 font-semibold p-2 bg-gray-50 rounded">{{ $d }}</div>
                    @endfor
                </div>
                        
                <!-- Body Gantt Bulanan -->
                <div class="mt-2 grid gap-1" id="monthlyBody" style="grid-template-columns: repeat({{ $daysInMonth }}, 1fr);">
                    @php
                        $monthlyData = [];
                        foreach($dicairkan as $pk) {
                            $tanggal = \Carbon\Carbon::parse($pk->tanggal);
                            if ($tanggal->month == $currentMonth && $tanggal->year == $currentYear) {
                                $day = $tanggal->day;
                                if (!isset($monthlyData[$day])) {
                                    $monthlyData[$day] = [];
                                }
                                $monthlyData[$day][] = $pk;
                            }
                        }
                    @endphp
                    
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        <div class="min-h-[60px] bg-gray-50 rounded p-1 space-y-1">
                            @if(isset($monthlyData[$d]))
                                @foreach($monthlyData[$d] as $program)
                                    <div class="group relative">
                                        <div onclick="scrollToProgram({{ $program->id }})"
                                            class="rounded h-6 cursor-pointer transition-all duration-200 transform hover:scale-105"
                                            style="background-color: {{ $colorMap[$program->bidang->nama] ?? '#6b7280' }};">
                                        </div>
                                        
                                        <!-- Tooltip on Hover -->
                                        <div class="tooltip-monthly absolute left-1/2 -translate-x-1/2 top-full mt-2 z-50 hidden group-hover:block w-64 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-3 pointer-events-none">
                                            <div class="font-bold mb-1">{{ $program->nama }}</div>
                                            <div class="text-gray-300">📁 Bidang: {{ $program->bidang->nama }}</div>
                                            <div class="text-gray-300">💰 Anggaran: Rp {{ number_format($program->anggaran, 0, ',', '.') }}</div>
                                            <div class="text-gray-300">📅 Tanggal: {{ \Carbon\Carbon::parse($program->tanggal)->format('d M Y') }}</div>
                                            <div class="text-gray-400 text-[10px] mt-2 italic">💡 Klik untuk lihat detail</div>
                                            <!-- Arrow -->
                                            <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 transform rotate-45"></div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endfor
                </div>

                @if(count($monthlyData) == 0)
                    <p class="text-sm text-gray-500 text-center py-8" id="monthlyEmpty">Tidak ada program yang dilaksanakan bulan ini</p>
                @endif
            </div>
        </div>

        <!-- VIEW TAHUNAN -->
        <div id="yearlyView" class="hidden">
            <p class="text-sm text-gray-600 mb-3">📅 Tahun {{ $currentYear }}</p>
            
            <!-- Header Kalender Tahunan -->
            <div class="grid gap-2" style="grid-template-columns: repeat(12, 1fr);">
                @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $month)
                    <div class="text-xs text-center text-gray-700 font-bold p-2 bg-gray-50 rounded">{{ $month }}</div>
                @endforeach
            </div>

            <!-- Body Gantt Tahunan -->
            <div class="mt-2 grid gap-2" style="grid-template-columns: repeat(12, 1fr);">
                @php
                    $yearlyData = [];
                    foreach($dicairkan as $pk) {
                        $tanggal = \Carbon\Carbon::parse($pk->tanggal);
                        if ($tanggal->year == $currentYear) {
                            $month = $tanggal->month;
                            if (!isset($yearlyData[$month])) {
                                $yearlyData[$month] = [];
                            }
                            $yearlyData[$month][] = $pk;
                        }
                    }
                @endphp
                
                @for($m = 1; $m <= 12; $m++)
                    <div class="min-h-[100px] bg-gray-50 rounded p-2 space-y-2">
                        @if(isset($yearlyData[$m]))
                            @foreach($yearlyData[$m] as $program)
                                <div class="group relative">
                                    <div onclick="scrollToProgram({{ $program->id }})"
                                        class="rounded h-8 cursor-pointer transition-all duration-200 transform hover:scale-105"
                                        style="background-color: {{ $colorMap[$program->bidang->nama] ?? '#6b7280' }};">
                                    </div>
                                    
                                    <!-- Tooltip on Hover -->
                                    <div class="tooltip-yearly absolute left-1/2 -translate-x-1/2 top-full mt-2 z-50 hidden group-hover:block w-64 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-3 pointer-events-none">
                                        <div class="font-bold mb-1">{{ $program->nama }}</div>
                                        <div class="text-gray-300">📁 Bidang: {{ $program->bidang->nama }}</div>
                                        <div class="text-gray-300">💰 Anggaran: Rp {{ number_format($program->anggaran, 0, ',', '.') }}</div>
                                        <div class="text-gray-300">📅 Tanggal: {{ \Carbon\Carbon::parse($program->tanggal)->format('d M Y') }}</div>
                                        <div class="text-gray-400 text-[10px] mt-2 italic">💡 Klik untuk lihat detail</div>
                                        <!-- Arrow -->
                                        <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 transform rotate-45"></div>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if(count($yearlyData[$m]) > 1)
                                <div class="text-[10px] text-gray-500 text-center mt-1">
                                    +{{ count($yearlyData[$m]) }} program
                                </div>
                            @endif
                        @endif
                    </div>
                @endfor
            </div>

            @if(count($yearlyData) == 0)
                <p class="text-sm text-gray-500 text-center py-8">Tidak ada program yang dicairkan tahun ini</p>
            @endif
        </div>

    </div>
    <!-- ===== END GANTT ===== -->

    <!-- ===== MONTHLY BAR CHART ===== -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <!-- ✅ TAMBAHKAN: Header dengan Year Selector -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <h2 class="text-lg font-bold text-gray-900">Grafik Perbandingan Planning vs Actual Program Kerja</h2>
            
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-600 font-medium">Tahun:</label>
                <select id="selectChartYear" onchange="updateChart()" 
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
        
        <canvas id="monthlyBarChart" height="50"></canvas>
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
                <option value="menunggu_pencairan">Menunggu Pencairan</option>
                <option value="dicairkan">Dicairkan</option>
                <option value="ditolak_bendahara">Ditolak Bendahara</option>
                <option value="ditolak_ketua">Ditolak Ketua</option>
            </select>
        </div>

        <!-- ✅ TAMBAHKAN: Show Per Page -->
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="programKerjaTableBody">
                    @forelse($programKerjas as $index => $pk)
                        <tr class="hover:bg-gray-50 transition" data-status="{{ $pk->status }}">
                            <!-- ✅ GANTI: Tetap pakai $programKerjas untuk nomor urut -->
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $programKerjas->firstItem() + $index }}
                            </td>
                            
                            @if(in_array($userRole, ['superadmin', 'sekretaris']))
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-s font-medium text-dark">
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
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pk->tanggal ? $pk->tanggal->format('d M Y') : '-' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Draft'],
                                        'menunggu_konfirmasi_bendahara' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu Bendahara'],
                                        'menunggu_approval_ketua' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Menunggu Ketua'],
                                        'menunggu_pencairan' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Menunggu Pencairan'],
                                        'dicairkan' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Dicairkan'],
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
                                    <button onclick="openDetailModal({{ $pk->id }})"
                                        class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                        title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                    @if($pk->isDraft() && in_array($userRole, ['admin', 'superadmin']))
                                        <!-- Edit Button -->
                                        <button onclick="openEditModal({{ $pk->id }})"
                                                class="bg-orange-500 text-white p-2 rounded-lg hover:bg-orange-600 transition"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <button onclick="deleteProgram({{ $pk->id }})"
                                                class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>

                                        <!-- Submit Button -->
                                        <button onclick="submitProgram({{ $pk->id }})"
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
                            <td colspan="{{ in_array($userRole, ['superadmin', 'sekretaris']) ? '8' : '7' }}" class="px-6 py-16 text-center">
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
        @if($programKerjas->hasPages() || $programKerjas->count() > 0)
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <!-- ✅ TAMBAHKAN: Showing Info -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="text-sm text-gray-600 mb-3 md:mb-0">
                        @if($perPage === 'all')
                            Menampilkan <span class="font-semibold">{{ $programKerjas->total() }}</span> data
                        @else
                            Menampilkan <span class="font-semibold">{{ $programKerjas->firstItem() }}</span> 
                            sampai <span class="font-semibold">{{ $programKerjas->lastItem() }}</span> 
                            dari <span class="font-semibold">{{ $programKerjas->total() }}</span> data
                        @endif
                    </div>
                    
                    <!-- Pagination Links -->
                    @if($perPage !== 'all')
                        {{ $programKerjas->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Include Create Modal -->
@if(in_array($userRole, ['admin', 'superadmin']))
@include('program-kerja.create')
@include('program-kerja.edit')
@include('program-kerja.detail')
@endif

@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@push('scripts')
<script>

    function changePerPage() {
        const perPage = document.getElementById('perPageSelect').value;
        const url = new URL(window.location.href);
        
        url.searchParams.set('perPage', perPage);
        
        // Keep existing filters
        @if(in_array($userRole, ['superadmin', 'sekretaris']))
        const bidangId = document.getElementById('filterBidang').value;
        if (bidangId !== 'all') {
            url.searchParams.set('bidang_id', bidangId);
        }
        @endif
        
        window.location.href = url.toString();
    }
    // Filter by Bidang
    function filterByBidang() {
        const bidangId = document.getElementById('filterBidang').value;
        const url = new URL(window.location.href);
        
        if (bidangId === 'all') {
            url.searchParams.delete('bidang_id');
        } else {
            url.searchParams.set('bidang_id', bidangId);
        }
        
        // ✅ PRESERVE perPage saat ganti bidang
        const perPage = document.getElementById('perPageSelect').value;
        url.searchParams.set('perPage', perPage);
        
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
            document.getElementById('editTanggal').value = pk.tanggal;  // ✅ TAMBAHKAN
            
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
    
    // Data program kerja dari PHP
const programKerjas = @json($dicairkan->values());

// ✅ TAMBAHKAN: Color map untuk bidang (sama seperti di PHP)
const colorMap = {
    'Bidang Organisasi': '#3b82f6',
    'Bidang Pendidikan': '#6366f1',
    'Bidang Hubungan Industrial': '#10b981',
    'Bidang Sosial Ekonomi': '#f59e0b',
    'Bidang Upah dan Bonus': '#ef4444',
    'Bidang Umum': '#8b5cf6',
    'Ketua': '#14b8a6',
    'Bendahara': '#ec4899',
    'Sekretaris': '#a3e635',
};

// Set default month & year
document.addEventListener('DOMContentLoaded', function() {
    const currentMonth = {{ now()->month }};
    const currentYear = {{ now()->year }};
    
    document.getElementById('selectMonth').value = currentMonth;
    document.getElementById('selectYear').value = currentYear;
    
    adjustTooltipPosition();
});

function changeMonth() {
    const month = parseInt(document.getElementById('selectMonth').value);
    const year = parseInt(document.getElementById('selectYear').value);
    
    // Update label
    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    document.getElementById('monthLabel').textContent = `📅 ${monthNames[month]} ${year}`;
    
    // Get days in selected month
    const daysInMonth = new Date(year, month, 0).getDate();
    
    // Filter data untuk bulan yang dipilih
    const monthlyData = {};
    programKerjas.forEach(pk => {
        const date = new Date(pk.tanggal);
        if (date.getMonth() + 1 === month && date.getFullYear() === year) {
            const day = date.getDate();
            if (!monthlyData[day]) monthlyData[day] = [];
            monthlyData[day].push(pk);
        }
    });
    
    // Build header
    let headerHtml = '';
    for (let d = 1; d <= daysInMonth; d++) {
        headerHtml += `<div class="text-xs text-center text-gray-500 font-semibold p-2 bg-gray-50 rounded">${d}</div>`;
    }
    
    // Build body
    let bodyHtml = '';
    for (let d = 1; d <= daysInMonth; d++) {
        bodyHtml += `<div class="min-h-[60px] bg-gray-50 rounded p-1 space-y-1">`;
        
        if (monthlyData[d]) {
            monthlyData[d].forEach(program => {
                const tanggal = new Date(program.tanggal);
                const formattedDate = tanggal.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                const formattedAnggaran = new Intl.NumberFormat('id-ID').format(program.anggaran);
                
                // ✅ FIX: Gunakan colorMap berdasarkan bidang
                const bidangNama = program.bidang.nama;
                const badgeColor = colorMap[bidangNama] || '#6b7280'; // default gray jika tidak ada
                
                bodyHtml += `
                    <div class="group relative">
                        <div onclick="scrollToProgram(${program.id})" 
                             class="rounded h-6 cursor-pointer transition-all duration-200 transform hover:scale-105"
                             style="background-color: ${badgeColor};">
                        </div>
                        <div class="tooltip-monthly absolute left-1/2 -translate-x-1/2 top-full mt-2 z-50 hidden group-hover:block w-64 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-3 pointer-events-none">
                            <div class="font-bold mb-1">${program.nama}</div>
                            <div class="text-gray-300">📁 Bidang: ${program.bidang.nama}</div>
                            <div class="text-gray-300">💰 Anggaran: Rp ${formattedAnggaran}</div>
                            <div class="text-gray-300">📅 Tanggal: ${formattedDate}</div>
                            <div class="text-gray-400 text-[10px] mt-2 italic">💡 Klik untuk lihat detail</div>
                            <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 transform rotate-45"></div>
                        </div>
                    </div>
                `;
            });
        }
        
        bodyHtml += `</div>`;
    }
    
    // Update grid
    const header = document.getElementById('monthlyHeader');
    const body = document.getElementById('monthlyBody');
    
    header.style.gridTemplateColumns = `repeat(${daysInMonth}, 1fr)`;
    header.innerHTML = headerHtml;
    
    body.style.gridTemplateColumns = `repeat(${daysInMonth}, 1fr)`;
    body.innerHTML = bodyHtml;
    
    // Check if empty
    const emptyMsg = document.getElementById('monthlyEmpty');
    if (Object.keys(monthlyData).length === 0) {
        if (!emptyMsg) {
            body.insertAdjacentHTML('afterend', '<p class="text-sm text-gray-500 text-center py-8" id="monthlyEmpty">Tidak ada program yang dilaksanakan bulan ini</p>');
        }
    } else {
        if (emptyMsg) emptyMsg.remove();
    }
    
    // Re-apply tooltip adjustment
    setTimeout(() => adjustTooltipPosition(), 100);
}

function switchView(view) {
    const monthlyView = document.getElementById('monthlyView');
    const yearlyView = document.getElementById('yearlyView');
    const monthSelector = document.getElementById('monthSelector');
    const btnMonthly = document.getElementById('btnMonthly');
    const btnYearly = document.getElementById('btnYearly');

    if (view === 'monthly') {
        monthlyView.classList.remove('hidden');
        yearlyView.classList.add('hidden');
        monthSelector.classList.remove('hidden');
        
        btnMonthly.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
        btnMonthly.classList.add('bg-black', 'text-white');
        
        btnYearly.classList.remove('bg-black', 'text-white');
        btnYearly.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
    } else {
        monthlyView.classList.add('hidden');
        yearlyView.classList.remove('hidden');
        monthSelector.classList.add('hidden');
        
        btnYearly.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
        btnYearly.classList.add('bg-black', 'text-white');
        
        btnMonthly.classList.remove('bg-black', 'text-white');
        btnMonthly.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
    }
}

// Auto-adjust tooltip position
function adjustTooltipPosition() {
    document.querySelectorAll('.tooltip-monthly, .tooltip-yearly').forEach(tooltip => {
        tooltip.parentElement.addEventListener('mouseenter', function() {
            setTimeout(() => {
                const rect = tooltip.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                
                // Reset classes
                tooltip.classList.remove('left-auto', 'right-0', 'left-0', 'left-1/2', '-translate-x-1/2');
                
                // Check if tooltip goes beyond right edge
                if (rect.right > viewportWidth - 10) {
                    tooltip.classList.add('left-auto', 'right-0');
                    const arrow = tooltip.querySelector('.absolute.w-2');
                    if (arrow) {
                        arrow.classList.remove('left-1/2', '-translate-x-1/2');
                        arrow.classList.add('right-4');
                    }
                }
                // Check if tooltip goes beyond left edge
                else if (rect.left < 10) {
                    tooltip.classList.add('left-0');
                    const arrow = tooltip.querySelector('.absolute.w-2');
                    if (arrow) {
                        arrow.classList.remove('left-1/2', '-translate-x-1/2');
                        arrow.classList.add('left-4');
                    }
                }
                // Default center position
                else {
                    tooltip.classList.add('left-1/2', '-translate-x-1/2');
                }
            }, 10);
        });
    });
}

// Scroll to program row in table dan buka detail modal
function scrollToProgram(programId) {
    const table = document.getElementById('programKerjaTable');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach((row, index) => {
        // ✅ FIX: Cari button detail dengan onclick yang mengandung programId
        const detailButton = row.querySelector(`button[onclick*="openDetailModal(${programId})"]`);
        
        if (detailButton) {
            // Highlight row sementara
            row.classList.add('bg-blue-100', 'ring-2', 'ring-blue-400');
            
            // Smooth scroll ke row
            row.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            // Pulse animation
            row.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                row.style.transform = 'scale(1.02)';
            }, 100);
            
            setTimeout(() => {
                row.style.transform = 'scale(1)';
            }, 400);
            
            // Remove highlight setelah 2 detik
            setTimeout(() => {
                row.classList.remove('bg-blue-100', 'ring-2', 'ring-blue-400');
            }, 2000);
            
            // ✅ AUTO OPEN DETAIL MODAL setelah scroll selesai
            setTimeout(() => {
                openDetailModal(programId);
            }, 800);
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("monthlyBarChart").getContext("2d");
    
    // ✅ Data dari PHP untuk semua tahun
    const chartDataByYear = @json($chartDataByYear);
    const monthNames = @json(array_values($monthNames));
    const currentYear = {{ now()->year }};
    
    // ✅ Initialize chart
    let monthlyBarChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: monthNames,
            datasets: [
                {
                    label: "Planning",
                    data: chartDataByYear[currentYear].planning,
                    backgroundColor: "rgba(150,150,150,0.6)",
                    borderRadius: 6,
                },
                {
                    label: "Actual (Dicairkan)",
                    data: chartDataByYear[currentYear].actual,
                    backgroundColor: "rgba(16,185,129,0.8)",
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const datasetLabel = context.dataset.label || '';
                            const count = context.parsed.y;
                            const selectedYear = parseInt(document.getElementById('selectChartYear').value);
                            
                            const planningBudgets = chartDataByYear[selectedYear].planningBudget;
                            const actualBudgets = chartDataByYear[selectedYear].actualBudget;
                            
                            let budget = 0;
                            if (context.datasetIndex === 0) {
                                budget = planningBudgets[context.dataIndex];
                            } else {
                                budget = actualBudgets[context.dataIndex];
                            }
                            
                            const formattedBudget = new Intl.NumberFormat('id-ID').format(budget);
                            
                            return [
                                `${datasetLabel}: ${count} program`,
                                `Total Anggaran: Rp ${formattedBudget}`
                            ];
                        },
                        title: function(context) {
                            return `Bulan ${context[0].label}`;
                        }
                    },
                    displayColors: true,
                    padding: 12,
                    bodySpacing: 4,
                    bodyFont: {
                        size: 13
                    },
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            }
        }
    });
    
    // ✅ TAMBAHKAN: Function untuk update chart saat ganti tahun
    window.updateChart = function() {
        const selectedYear = parseInt(document.getElementById('selectChartYear').value);
        
        if (chartDataByYear[selectedYear]) {
            monthlyBarChart.data.datasets[0].data = chartDataByYear[selectedYear].planning;
            monthlyBarChart.data.datasets[1].data = chartDataByYear[selectedYear].actual;
            monthlyBarChart.update('active'); // smooth animation
        }
    };
});

</script>
@endpush