@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    // ✅ PREPARE DATA UNTUK CHARTS
    $allProgramKerjas = \App\Models\ProgramKerja::with(['bidang'])->get();
    $dicairkan = $allProgramKerjas->filter(fn($p) => $p->status === 'dicairkan');
    $currentMonth = now()->month;
    $currentYear = now()->year;
    
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
    
    // Deklarasi monthNames
    $monthNames = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];

    // Hitung Planning dan Actual untuk tahun sekarang
    $planning = array_fill(1, 12, 0);
    $actual = array_fill(1, 12, 0);
    $planningBudget = array_fill(1, 12, 0);
    $actualBudget = array_fill(1, 12, 0);

    foreach ($allProgramKerjas as $pk) {
        $pkDate = \Carbon\Carbon::parse($pk->tanggal);
        
        if ($pkDate->year == now()->year) {
            $month = $pkDate->month;
            
            if ($pk->status !== 'draft') {
                $planning[$month]++;
                $planningBudget[$month] += $pk->anggaran;
            }
            
            if ($pk->status === 'dicairkan') {
                $actual[$month]++;
                $actualBudget[$month] += $pk->anggaran;
            }
        }
    }

    // Siapkan data untuk semua tahun
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
                
                if ($pk->status !== 'draft') {
                    $planningYear[$month]++;
                    $planningBudgetYear[$month] += $pk->anggaran;
                }
                
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

<div class="space-y-6">
    
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Welcome back, {{ Auth::user()->name }}!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\User::count() }}</p>
                    <p class="text-sm text-green-600 mt-2">
                        <span class="font-semibold">{{ \App\Models\User::where('status', 'active')->count() }}</span> Active
                    </p>
                </div>
                <div class="bg-black rounded-lg p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Bidang -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bidang</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Bidang::count() }}</p>
                    <p class="text-sm text-gray-500 mt-2">Departments</p>
                </div>
                <div class="bg-black rounded-lg p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Kas Global - PALING KANAN -->
        @php
            $kasGlobal = \App\Models\Kas::getGlobal();
            $userRole = Auth::user()->role->nama ?? '';
            $canManageKas = in_array($userRole, ['superadmin', 'bendahara']);
        @endphp
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition {{ $canManageKas ? 'cursor-pointer' : '' }}"
             @if($canManageKas) onclick="openTambahKasModal()" @endif>
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-600">Kas Global</p>
                        @if($canManageKas)
                        <button onclick="event.stopPropagation(); openTambahKasModal()" class="bg-green-100 text-green-700 rounded-full p-1 hover:bg-green-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                    <p class="text-2xl font-bold {{ $kasGlobal->saldo >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                        Rp {{ number_format($kasGlobal->saldo, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        {{ $kasGlobal->saldo >= 0 ? 'Available' : 'Deficit' }}
                    </p>
                </div>
                <div class="bg-green-600 rounded-lg p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MY DEBT SECTION (Hidden by Default) ===== -->
    @php
        $myDebt = \App\Models\PengajuanHutang::where('user_id', auth()->id())
            ->where('status', 'dicairkan')
            ->get();
        $myTotalHutang = $myDebt->sum('jumlah');
        $myTotalSisa = $myDebt->sum('sisa_hutang');
        $myTotalTerbayar = $myTotalHutang - $myTotalSisa;
    @endphp

    @if($myDebt->count() > 0)
    <div class="bg-gradient-to-r from-red-50 to-red-50 border border-red-200 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Icon Wallet -->
                <div class="bg-red-500 rounded-lg p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>

                <!-- Info -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Hutang Saya</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Anda memiliki <span class="font-semibold text-red-700">{{ $myDebt->count() }} hutang aktif</span>
                    </p>
                </div>
            </div>

            <!-- Toggle Button (Eye Icon) -->
            <button onclick="toggleMyDebt()" 
                    class="bg-white hover:bg-gray-100 text-gray-700 p-3 rounded-lg transition shadow-sm"
                    title="Tampilkan/Sembunyikan">
                <svg id="eyeIconOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg id="eyeIconClosed" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
            </button>
        </div>

        <!-- Detail Section (Hidden by Default) -->
        <div id="myDebtDetail" class="hidden mt-6 space-y-4">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Total Hutang -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-xs text-gray-600 font-semibold uppercase">Total Hutang</p>
                    <p class="text-2xl font-bold text-red-600 mt-2">
                        Rp {{ number_format($myTotalHutang, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Total Terbayar -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-xs text-gray-600 font-semibold uppercase">Terbayar</p>
                    <p class="text-2xl font-bold text-green-600 mt-2">
                        Rp {{ number_format($myTotalTerbayar, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Sisa Hutang -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-xs text-gray-600 font-semibold uppercase">Sisa Hutang</p>
                    <p class="text-2xl font-bold text-orange-600 mt-2">
                        Rp {{ number_format($myTotalSisa, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- List Hutang -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 mb-3">Detail Hutang</h4>
                <div class="space-y-3">
                    @foreach($myDebt as $debt)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $debt->nama }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $debt->tanggal->format('d M Y') }}</p>
                                
                                <!-- Progress Bar -->
                                <div class="flex items-center space-x-2 mt-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $debt->persen_lunas }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700">{{ number_format($debt->persen_lunas, 0) }}%</span>
                                </div>
                            </div>

                            <div class="text-right ml-4">
                                <p class="text-sm font-bold text-red-600">
                                    Rp {{ number_format($debt->sisa_hutang, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500">sisa</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- ===== END MY DEBT SECTION ===== -->

    <!-- ===== GANTT CHART ===== -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
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

        <!-- VIEW BULANAN -->
        <div id="monthlyView">
            @php
                $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            @endphp
            <p id="monthLabel" class="text-sm text-gray-600 mb-3">📅 {{ $bulanIndo[now()->month] }} {{ now()->year }}</p>
            
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
                                        <div class="rounded h-6 cursor-pointer transition-all duration-200 transform hover:scale-105"
                                            style="background-color: {{ $colorMap[$program->bidang->nama] ?? '#6b7280' }};">
                                        </div>
                                        
                                        <!-- Tooltip on Hover -->
                                        <div class="tooltip-monthly absolute left-1/2 -translate-x-1/2 top-full mt-2 z-50 hidden group-hover:block w-64 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-3 pointer-events-none">
                                            <div class="font-bold mb-1">{{ $program->nama }}</div>
                                            <div class="text-gray-300">📁 Bidang: {{ $program->bidang->nama }}</div>
                                            <div class="text-gray-300">💰 Anggaran: Rp {{ number_format($program->anggaran, 0, ',', '.') }}</div>
                                            <div class="text-gray-300">📅 Tanggal: {{ \Carbon\Carbon::parse($program->tanggal)->format('d M Y') }}</div>
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
                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $month)
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
                                    <div class="rounded h-8 cursor-pointer transition-all duration-200 transform hover:scale-105"
                                        style="background-color: {{ $colorMap[$program->bidang->nama] ?? '#6b7280' }};">
                                    </div>
                                    
                                    <!-- Tooltip on Hover -->
                                    <div class="tooltip-yearly absolute left-1/2 -translate-x-1/2 top-full mt-2 z-50 hidden group-hover:block w-64 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-3 pointer-events-none">
                                        <div class="font-bold mb-1">{{ $program->nama }}</div>
                                        <div class="text-gray-300">📁 Bidang: {{ $program->bidang->nama }}</div>
                                        <div class="text-gray-300">💰 Anggaran: Rp {{ number_format($program->anggaran, 0, ',', '.') }}</div>
                                        <div class="text-gray-300">📅 Tanggal: {{ \Carbon\Carbon::parse($program->tanggal)->format('d M Y') }}</div>
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

    <!-- ===== USER EVENT ATTENDANCE HISTORY ===== -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Riwayat Kehadiran Event Saya</h2>
                <p class="text-sm text-gray-600 mt-1">Daftar event yang pernah Anda ikuti</p>
            </div>
            <p class="text-sm text-gray-700">
                Total Kehadiran Acara: <span class="font-semibold">{{ auth()->user()->eventAttendances()->count() }}</span>
            </p>
        </div>

        @php
            $myAttendances = auth()->user()->eventAttendances()
                ->with('event')
                ->orderBy('waktu_hadir', 'desc')
                ->paginate(5); // ✅ 5 per page dengan pagination
        @endphp

        @if($myAttendances->count() > 0)
            <div class="space-y-3">
                @foreach($myAttendances as $attendance)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-4">
                            <!-- Icon Event -->
                            <div class="bg-blue-500 rounded-lg p-3 flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>

                            <!-- Event Info -->
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $attendance->event->nama_event }}</h3>
                                <div class="flex items-center space-x-4 mt-1 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $attendance->event->waktu_pelaksanaan->format('d M Y') }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $attendance->event->tempat_pelaksanaan }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Waktu Hadir -->
                        <div class="text-right">
                            <div class="flex items-center text-green-600">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-semibold">Hadir</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $attendance->waktu_hadir->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- ✅ Pagination Links -->
            @if($myAttendances->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $myAttendances->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="mt-4 text-gray-600 font-semibold">Belum Ada Kehadiran Event</p>
                <p class="text-sm text-gray-500 mt-1">Anda belum pernah mengikuti event apapun</p>
            </div>
        @endif
    </div>
    <!-- ===== END USER EVENT ATTENDANCE HISTORY ===== -->

    <!-- ===== BAR CHART ===== -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
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
    <!-- ===== END BAR CHART ===== -->

    <!-- Two Column Layout (existing content) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- ... existing Recent Users and Roles Overview ... -->
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

<!-- ✅ TAMBAHKAN: Chart.js & Scripts untuk Gantt & Bar Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@push('scripts')
<script>
// Data program kerja dari PHP
const programKerjas = @json($dicairkan->values());

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
    
    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    document.getElementById('monthLabel').textContent = `📅 ${monthNames[month]} ${year}`;
    
    const daysInMonth = new Date(year, month, 0).getDate();
    
    const monthlyData = {};
    programKerjas.forEach(pk => {
        const date = new Date(pk.tanggal);
        if (date.getMonth() + 1 === month && date.getFullYear() === year) {
            const day = date.getDate();
            if (!monthlyData[day]) monthlyData[day] = [];
            monthlyData[day].push(pk);
        }
    });
    
    let headerHtml = '';
    for (let d = 1; d <= daysInMonth; d++) {
        headerHtml += `<div class="text-xs text-center text-gray-500 font-semibold p-2 bg-gray-50 rounded">${d}</div>`;
    }
    
    let bodyHtml = '';
    for (let d = 1; d <= daysInMonth; d++) {
        bodyHtml += `<div class="min-h-[60px] bg-gray-50 rounded p-1 space-y-1">`;
        
        if (monthlyData[d]) {
            monthlyData[d].forEach(program => {
                const tanggal = new Date(program.tanggal);
                const formattedDate = tanggal.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                const formattedAnggaran = new Intl.NumberFormat('id-ID').format(program.anggaran);
                
                const bidangNama = program.bidang.nama;
                const badgeColor = colorMap[bidangNama] || '#6b7280';
                
                bodyHtml += `
                    <div class="group relative">
                        <div class="rounded h-6 cursor-pointer transition-all duration-200 transform hover:scale-105"
                             style="background-color: ${badgeColor};">
                        </div>
                        <div class="tooltip-monthly absolute left-1/2 -translate-x-1/2 top-full mt-2 z-50 hidden group-hover:block w-64 bg-gray-900 text-white text-xs rounded-lg shadow-xl p-3 pointer-events-none">
                            <div class="font-bold mb-1">${program.nama}</div>
                            <div class="text-gray-300">📁 Bidang: ${program.bidang.nama}</div>
                            <div class="text-gray-300">💰 Anggaran: Rp ${formattedAnggaran}</div>
                            <div class="text-gray-300">📅 Tanggal: ${formattedDate}</div>
                            <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 transform rotate-45"></div>
                        </div>
                    </div>
                `;
            });
        }
        
        bodyHtml += `</div>`;
    }
    
    const header = document.getElementById('monthlyHeader');
    const body = document.getElementById('monthlyBody');
    
    header.style.gridTemplateColumns = `repeat(${daysInMonth}, 1fr)`;
    header.innerHTML = headerHtml;
    
    body.style.gridTemplateColumns = `repeat(${daysInMonth}, 1fr)`;
    body.innerHTML = bodyHtml;
    
    const emptyMsg = document.getElementById('monthlyEmpty');
    if (Object.keys(monthlyData).length === 0) {
        if (!emptyMsg) {
            body.insertAdjacentHTML('afterend', '<p class="text-sm text-gray-500 text-center py-8" id="monthlyEmpty">Tidak ada program yang dilaksanakan bulan ini</p>');
        }
    } else {
        if (emptyMsg) emptyMsg.remove();
    }
    
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

function adjustTooltipPosition() {
    document.querySelectorAll('.tooltip-monthly, .tooltip-yearly').forEach(tooltip => {
        tooltip.parentElement.addEventListener('mouseenter', function() {
            setTimeout(() => {
                const rect = tooltip.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                
                tooltip.classList.remove('left-auto', 'right-0', 'left-0', 'left-1/2', '-translate-x-1/2');
                
                if (rect.right > viewportWidth - 10) {
                    tooltip.classList.add('left-auto', 'right-0');
                    const arrow = tooltip.querySelector('.absolute.w-2');
                    if (arrow) {
                        arrow.classList.remove('left-1/2', '-translate-x-1/2');
                        arrow.classList.add('right-4');
                    }
                } else if (rect.left < 10) {
                    tooltip.classList.add('left-0');
                    const arrow = tooltip.querySelector('.absolute.w-2');
                    if (arrow) {
                        arrow.classList.remove('left-1/2', '-translate-x-1/2');
                        arrow.classList.add('left-4');
                    }
                } else {
                    tooltip.classList.add('left-1/2', '-translate-x-1/2');
                }
            }, 10);
        });
    });
}

// BAR CHART
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("monthlyBarChart").getContext("2d");
    
    const chartDataByYear = @json($chartDataByYear);
    const monthNames = @json(array_values($monthNames));
    const currentYear = {{ now()->year }};
    
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
    
    window.updateChart = function() {
        const selectedYear = parseInt(document.getElementById('selectChartYear').value);
        
        if (chartDataByYear[selectedYear]) {
            monthlyBarChart.data.datasets[0].data = chartDataByYear[selectedYear].planning;
            monthlyBarChart.data.datasets[1].data = chartDataByYear[selectedYear].actual;
            monthlyBarChart.update('active');
        }
    };
});

function toggleMyDebt() {
    const detail = document.getElementById('myDebtDetail');
    const eyeOpen = document.getElementById('eyeIconOpen');
    const eyeClosed = document.getElementById('eyeIconClosed');
    
    if (detail.classList.contains('hidden')) {
        // Show
        detail.classList.remove('hidden');
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        // Hide
        detail.classList.add('hidden');
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}
</script>
@endpush