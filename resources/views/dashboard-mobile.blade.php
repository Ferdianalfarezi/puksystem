<!-- dashboard.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard - Sistem Manajemen Kas</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Font Awesome untuk ikon tambahan -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Custom styles jika diperlukan */
        .transition-all {
            transition: all 0.3s ease;
        }
        
        /* Tooltip styles */
        .tooltip-monthly, .tooltip-yearly {
            transition: opacity 0.2s;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Modal backdrop */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        /* Card hover effects */
        .hover-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

@php
    // Authentikasi manual (sesuaikan dengan sistem login Anda)
    $user = Auth::user();
    
    if (!$user) {
        header('Location: /login');
        exit;
    }
    
    $kasGlobal = \App\Models\Kas::getGlobal();
    $userRole = $user->role->nama ?? '';
    $canManageKas = in_array($userRole, ['superadmin', 'bendahara']);

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

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-30">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo dan Nama Aplikasi -->
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 rounded-lg p-2">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">PUK SYSTEM</h1>
                       
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700 hidden sm:block">Halo, {{ $user->name }}</span>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 bg-gray-100 p-2 rounded-lg hover:bg-gray-200 transition">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 hidden group-hover:block border border-gray-200">
                            <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Pengaturan
                            </a>
                            <hr class="my-1 border-gray-200">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-20 pb-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="space-y-4 sm:space-y-6">
            
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">Welcome back, {{ $user->name }}!</p>
                </div>
            </div>

           

            <!-- ===== MY DEBT SECTION ===== -->
            @php
                $myDebt = \App\Models\PengajuanHutang::where('user_id', auth()->id())
                    ->where('status', 'dicairkan')
                    ->get();
                $myTotalHutang = $myDebt->sum('jumlah');
                $myTotalSisa = $myDebt->sum('sisa_hutang');
                $myTotalTerbayar = $myTotalHutang - $myTotalSisa;
            @endphp

            @if($myDebt->count() > 0)
            <div class="bg-gradient-to-r from-red-50 to-red-50 border border-red-200 rounded-xl p-4 sm:p-6 hover-card">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <!-- Icon Wallet -->
                        <div class="bg-red-500 rounded-lg p-2 sm:p-3 flex-shrink-0">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>

                        <!-- Info -->
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900">Hutang Saya</h3>
                            <p class="text-xs sm:text-sm text-gray-600 mt-1 truncate">
                                Anda memiliki <span class="font-semibold text-red-700">{{ $myDebt->count() }} hutang aktif</span>
                            </p>
                        </div>
                    </div>

                    <!-- Toggle Button -->
                    <button onclick="toggleMyDebt()" 
                            class="bg-white hover:bg-gray-100 text-gray-700 p-2 sm:p-3 rounded-lg transition shadow-sm self-end sm:self-center">
                        <svg id="eyeIconOpen" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="eyeIconClosed" class="w-5 h-5 sm:w-6 sm:h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>

                <!-- Detail Section -->
                <div id="myDebtDetail" class="hidden mt-4 sm:mt-6 space-y-3 sm:space-y-4">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Total Hutang -->
                        <div class="bg-white rounded-lg p-3 sm:p-4 shadow-sm">
                            <p class="text-xs text-gray-600 font-semibold uppercase">Total Hutang</p>
                            <p class="text-lg sm:text-2xl font-bold text-red-600 mt-1 break-words">
                                Rp {{ number_format($myTotalHutang, 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Total Terbayar -->
                        <div class="bg-white rounded-lg p-3 sm:p-4 shadow-sm">
                            <p class="text-xs text-gray-600 font-semibold uppercase">Terbayar</p>
                            <p class="text-lg sm:text-2xl font-bold text-green-600 mt-1 break-words">
                                Rp {{ number_format($myTotalTerbayar, 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Sisa Hutang -->
                        <div class="bg-white rounded-lg p-3 sm:p-4 shadow-sm">
                            <p class="text-xs text-gray-600 font-semibold uppercase">Sisa Hutang</p>
                            <p class="text-lg sm:text-2xl font-bold text-orange-600 mt-1 break-words">
                                Rp {{ number_format($myTotalSisa, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <!-- List Hutang -->
                    <div class="bg-white rounded-lg p-3 sm:p-4 shadow-sm">
                        <h4 class="text-xs sm:text-sm font-bold text-gray-900 mb-2 sm:mb-3">Detail Hutang</h4>
                        <div class="space-y-2 sm:space-y-3">
                            @foreach($myDebt as $debt)
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-2 sm:p-3 bg-gray-50 rounded-lg gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ $debt->nama }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $debt->tanggal->format('d M Y') }}</p>
                                        
                                        <!-- Progress Bar -->
                                        <div class="flex items-center space-x-2 mt-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $debt->persen_lunas }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-700">{{ number_format($debt->persen_lunas, 0) }}%</span>
                                        </div>
                                    </div>

                                    <div class="sm:text-right ml-0 sm:ml-4">
                                        <p class="text-sm sm:text-base font-bold text-red-600 break-words">
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

            

            <!-- ===== GANTT CHART ===== -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 overflow-x-auto">
                <!-- Header dengan Toggle View -->
                <div class="flex flex-col space-y-3 mb-4">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Kalender Program Kerja</h2>
                    
                    <div class="flex flex-col space-y-2">
                        <!-- Month Selector -->
                        <div id="monthSelectorDashboard" class="flex flex-wrap items-center gap-2">
                            <label class="text-xs sm:text-sm text-gray-600 font-medium">Bulan:</label>
                            <select id="selectMonthDashboard" onchange="changeMonthDashboard()" 
                                    class="flex-1 min-w-[120px] px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
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
                            <select id="selectYearDashboard" onchange="changeMonthDashboard()" 
                                    class="flex-1 min-w-[100px] px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                                @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <!-- Toggle Bulanan/Tahunan -->
                        <div class="flex items-center space-x-2">
                            <button id="btnMonthlyDashboard" onclick="switchViewDashboard('monthly')" 
                                    class="flex-1 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-lg transition bg-black text-white">
                                Bulanan
                            </button>
                            <button id="btnYearlyDashboard" onclick="switchViewDashboard('yearly')" 
                                    class="flex-1 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-lg transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                                Tahunan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- VIEW BULANAN -->
                <div id="monthlyViewDashboard" class="overflow-x-auto">
                    @php
                        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    @endphp
                    <p id="monthLabelDashboard" class="text-xs sm:text-sm text-gray-600 mb-3">📅 {{ $bulanIndo[now()->month] }} {{ now()->year }}</p>
                    
                    <div id="monthlyGridContainerDashboard" class="min-w-[800px]">
                        @php
                            $daysInMonth = now()->daysInMonth;
                        @endphp
                        
                        <!-- Header Kalender Bulanan -->
                        <div class="grid gap-1" id="monthlyHeaderDashboard" style="grid-template-columns: repeat({{ $daysInMonth }}, 1fr);">
                            @for($d=1; $d <= $daysInMonth; $d++)
                                <div class="text-[10px] sm:text-xs text-center text-gray-500 font-semibold p-1 sm:p-2 bg-gray-50 rounded">{{ $d }}</div>
                            @endfor
                        </div>
                                
                        <!-- Body Gantt Bulanan -->
                        <div class="mt-2 grid gap-1" id="monthlyBodyDashboard" style="grid-template-columns: repeat({{ $daysInMonth }}, 1fr);">
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
                                <div class="min-h-[40px] sm:min-h-[60px] bg-gray-50 rounded p-0.5 sm:p-1 space-y-0.5 sm:space-y-1">
                                    @if(isset($monthlyData[$d]))
                                        @foreach($monthlyData[$d] as $program)
                                            <div class="group relative">
                                                <div onclick="openProgramDetailDashboard({{ $program->id }})"
                                                    class="rounded h-4 sm:h-6 cursor-pointer transition-all duration-200 transform hover:scale-105 opacity-100"
                                                    style="background-color: {{ $colorMap[$program->bidang->nama] ?? '#6b7280' }};">
                                                </div>
                                                
                                                <!-- Tooltip -->
                                                <div class="tooltip-monthly absolute left-1/2 -translate-x-1/2 top-full mt-2 z-50 hidden group-hover:block w-48 sm:w-64 bg-gray-900 text-white text-[10px] sm:text-xs rounded-lg shadow-xl p-2 sm:p-3 pointer-events-none">
                                                    <div class="font-bold mb-1 truncate">{{ $program->nama }}</div>
                                                    <div class="text-gray-300">📁 Bidang: {{ $program->bidang->nama }}</div>
                                                    <div class="text-gray-300">💰 Rp {{ number_format($program->anggaran, 0, ',', '.') }}</div>
                                                    <div class="text-gray-300">📅 {{ \Carbon\Carbon::parse($program->tanggal)->format('d M Y') }}</div>
                                                    <div class="text-gray-400 text-[8px] sm:text-[10px] mt-2 italic">💡 Klik untuk lihat detail</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endfor
                        </div>

                        @if(count($monthlyData) == 0)
                            <p class="text-xs sm:text-sm text-gray-500 text-center py-4 sm:py-8" id="monthlyEmptyDashboard">Tidak ada program yang dilaksanakan bulan ini</p>
                        @endif
                    </div>
                </div>

                <!-- VIEW TAHUNAN -->
                <div id="yearlyViewDashboard" class="hidden overflow-x-auto">
                    <p class="text-xs sm:text-sm text-gray-600 mb-3">📅 Tahun {{ $currentYear }}</p>
                    
                    <!-- Header Kalender Tahunan -->
                    <div class="grid gap-1 sm:gap-2 min-w-[900px]" style="grid-template-columns: repeat(12, 1fr);">
                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $month)
                            <div class="text-[10px] sm:text-xs text-center text-gray-700 font-bold p-1 sm:p-2 bg-gray-50 rounded">{{ $month }}</div>
                        @endforeach
                    </div>

                    <!-- Body Gantt Tahunan -->
                    <div class="mt-2 grid gap-1 sm:gap-2 min-w-[900px]" style="grid-template-columns: repeat(12, 1fr);">
                        @php
                            $yearlyData = [];
                            $monthlyBudgets = [];
                            
                            foreach($dicairkan as $pk) {
                                $tanggal = \Carbon\Carbon::parse($pk->tanggal);
                                if ($tanggal->year == $currentYear) {
                                    $month = $tanggal->month;
                                    if (!isset($yearlyData[$month])) {
                                        $yearlyData[$month] = [];
                                        $monthlyBudgets[$month] = 0;
                                    }
                                    $yearlyData[$month][] = $pk;
                                    $monthlyBudgets[$month] += $pk->anggaran;
                                }
                            }
                        @endphp
                        
                        @for($m = 1; $m <= 12; $m++)
                            <div class="min-h-[80px] sm:min-h-[100px] bg-gray-50 rounded p-1 sm:p-2 space-y-1 sm:space-y-2">
                                @if(isset($yearlyData[$m]))
                                    @foreach($yearlyData[$m] as $program)
                                        <div class="group relative">
                                            <div onclick="openProgramDetailDashboard({{ $program->id }})"
                                                class="rounded h-5 sm:h-8 cursor-pointer transition-all duration-200 transform hover:scale-105 opacity-100"
                                                style="background-color: {{ $colorMap[$program->bidang->nama] ?? '#6b7280' }};">
                                            </div>
                                            
                                            <!-- Tooltip -->
                                            <div class="tooltip-yearly absolute left-1/2 -translate-x-1/2 top-full mt-2 z-50 hidden group-hover:block w-48 sm:w-64 bg-gray-900 text-white text-[10px] sm:text-xs rounded-lg shadow-xl p-2 sm:p-3 pointer-events-none">
                                                <div class="font-bold mb-1 truncate">{{ $program->nama }}</div>
                                                <div class="text-gray-300">📁 Bidang: {{ $program->bidang->nama }}</div>
                                                <div class="text-gray-300">💰 Rp {{ number_format($program->anggaran, 0, ',', '.') }}</div>
                                                <div class="text-gray-300">📅 {{ \Carbon\Carbon::parse($program->tanggal)->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if(count($yearlyData[$m]) > 1)
                                        <div class="text-[8px] sm:text-[10px] text-gray-500 text-center mt-1">
                                            {{ count($yearlyData[$m]) }} program
                                        </div>
                                    @endif
                                    
                                    <!-- Total Budget -->
                                    <div class="mt-1 sm:mt-2 pt-1 sm:pt-2 border-t border-gray-300">
                                        <div class="text-[8px] sm:text-[10px] font-semibold text-gray-600 text-center">Total:</div>
                                        <div class="text-[9px] sm:text-xs font-bold text-green-700 text-center truncate">
                                            Rp {{ number_format($monthlyBudgets[$m] ?? 0, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>

                    @if(count($yearlyData) == 0)
                        <p class="text-xs sm:text-sm text-gray-500 text-center py-4 sm:py-8">Tidak ada program yang dicairkan tahun ini</p>
                    @endif
                </div>

                <!-- Legend -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500 mb-2">Keterangan Bidang:</p>
                    <div class="flex flex-wrap gap-2 sm:gap-3">
                        @foreach($colorMap as $bidang => $color)
                        <div class="flex items-center space-x-1 sm:space-x-2">
                            <div class="w-3 h-3 sm:w-4 sm:h-4 rounded" style="background-color: {{ $color }}"></div>
                            <span class="text-[10px] sm:text-xs text-gray-600 truncate max-w-[80px] sm:max-w-none">{{ $bidang }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ===== USER EVENT ATTENDANCE HISTORY ===== -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4 sm:mb-6">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">Riwayat Kehadiran Event</h2>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Daftar event yang pernah Anda ikuti</p>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-700">
                        Total: <span class="font-semibold">{{ auth()->user()->eventAttendances()->count() }}</span>
                    </p>
                </div>

                @php
                    $myAttendances = auth()->user()->eventAttendances()
                        ->with('event')
                        ->orderBy('waktu_hadir', 'desc')
                        ->paginate(5);
                @endphp

                @if($myAttendances->count() > 0)
                    <div class="space-y-2 sm:space-y-3">
                        @foreach($myAttendances as $attendance)
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition gap-3">
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <!-- Icon Event -->
                                    <div class="bg-blue-500 rounded-lg p-2 sm:p-3 flex-shrink-0">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>

                                    <!-- Event Info -->
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ $attendance->event->nama_event }}</h3>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 mt-1 text-xs text-gray-600">
                                            <div class="flex items-center">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="truncate">{{ $attendance->event->waktu_pelaksanaan->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="truncate">{{ $attendance->event->tempat_pelaksanaan }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Waktu Hadir -->
                                <div class="sm:text-right ml-12 sm:ml-4">
                                    <div class="flex items-center sm:justify-end text-green-600">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-xs sm:text-sm font-semibold">Hadir</span>
                                    </div>
                                    <p class="text-[10px] sm:text-xs text-gray-500 mt-1">
                                        {{ $attendance->waktu_hadir->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($myAttendances->hasPages())
                        <div class="mt-4 sm:mt-6 flex justify-center">
                            {{ $myAttendances->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 sm:py-12">
                        <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-3 sm:mt-4 text-sm sm:text-base text-gray-600 font-semibold">Belum Ada Kehadiran Event</p>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Anda belum pernah mengikuti event apapun</p>
                    </div>
                @endif
            </div>

            <!-- ===== BAR CHART ===== -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Grafik Planning vs Actual</h2>
                    
                    <div class="flex items-center space-x-2">
                        <label class="text-xs sm:text-sm text-gray-600 font-medium">Tahun:</label>
                        <select id="selectChartYear" onchange="updateChart()" 
                                class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                            @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                
                <div class="w-full overflow-x-auto">
                    <canvas id="monthlyBarChart" class="min-w-[600px] sm:min-w-full" height="50"></canvas>
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL DETAIL PROGRAM KERJA -->
    <div id="programDetailModal" class="hidden fixed inset-0 modal-backdrop z-50 flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto mx-3 sm:mx-0">
            <div class="p-4 sm:p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-2xl font-bold text-gray-900">Detail Program Kerja</h3>
                    <button onclick="closeProgramDetailModal()" class="text-gray-400 hover:text-gray-600 transition p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="space-y-3 sm:space-y-4">
                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                        <label class="text-xs sm:text-sm font-semibold text-gray-600">Nama Program</label>
                        <p id="detailNamaProgram" class="text-base sm:text-lg font-bold text-gray-900 mt-1 break-words"></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="text-xs sm:text-sm font-semibold text-gray-600">Bidang</label>
                            <p id="detailBidangProgram" class="text-sm sm:text-base text-gray-900 mt-1 break-words"></p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="text-xs sm:text-sm font-semibold text-gray-600">Jenis Pengeluaran</label>
                            <p id="detailJenisProgram" class="text-sm sm:text-base text-gray-900 mt-1 break-words"></p>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-3 sm:p-4 border border-green-200">
                        <label class="text-xs sm:text-sm font-semibold text-green-700">Anggaran</label>
                        <p id="detailAnggaranProgram" class="text-lg sm:text-2xl font-bold text-green-600 mt-1 break-words"></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="text-xs sm:text-sm font-semibold text-gray-600">Tanggal</label>
                            <p id="detailTanggalProgram" class="text-sm sm:text-base text-gray-900 mt-1 break-words"></p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="text-xs sm:text-sm font-semibold text-gray-600">Tahun</label>
                            <p id="detailTahunProgram" class="text-sm sm:text-base text-gray-900 mt-1 break-words"></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                        <label class="text-xs sm:text-sm font-semibold text-gray-600">Status</label>
                        <p id="detailStatusProgram" class="text-sm sm:text-base mt-1"></p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-4 sm:mt-6 flex justify-end">
                    <button onclick="closeProgramDetailModal()" 
                            class="w-full sm:w-auto bg-gray-600 text-white px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg hover:bg-gray-700 transition text-sm sm:text-base">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH KAS (Untuk Bendahara) -->
    @if($canManageKas)
    <div id="tambahKasModal" class="hidden fixed inset-0 modal-backdrop z-50 flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-3 sm:mx-0">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Tambah Kas Global</h3>
                    <button onclick="closeTambahKasModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="formTambahKas" onsubmit="submitTambahKas(event)">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kas</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" name="jumlah" id="jumlahKas" required
                                       class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" id="keteranganKas" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Masukkan keterangan..."></textarea>
                        </div>
                        
                        <div class="flex space-x-2 pt-4">
                            <button type="submit" 
                                    class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                                Simpan
                            </button>
                            <button type="button" onclick="closeTambahKasModal()"
                                    class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition font-semibold">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Scripts -->
    <script>
        // Data program kerja dari PHP
        const programKerjasDashboard = @json($dicairkan->values());

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
            
            const selectMonth = document.getElementById('selectMonthDashboard');
            const selectYear = document.getElementById('selectYearDashboard');
            
            if (selectMonth) selectMonth.value = currentMonth;
            if (selectYear) selectYear.value = currentYear;
            
            adjustTooltipPositionDashboard();
            
            // Handle responsive tooltips
            window.addEventListener('resize', function() {
                adjustTooltipPositionDashboard();
            });
        });

        function changeMonthDashboard() {
            const month = parseInt(document.getElementById('selectMonthDashboard').value);
            const year = parseInt(document.getElementById('selectYearDashboard').value);
            
            const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            document.getElementById('monthLabelDashboard').textContent = `📅 ${monthNames[month]} ${year}`;
            
            const daysInMonth = new Date(year, month, 0).getDate();
            
            const monthlyData = {};
            programKerjasDashboard.forEach(pk => {
                const date = new Date(pk.tanggal);
                if (date.getMonth() + 1 === month && date.getFullYear() === year) {
                    const day = date.getDate();
                    if (!monthlyData[day]) monthlyData[day] = [];
                    monthlyData[day].push(pk);
                }
            });
            
            let headerHtml = '';
            for (let d = 1; d <= daysInMonth; d++) {
                headerHtml += `<div class="text-[10px] sm:text-xs text-center text-gray-500 font-semibold p-1 sm:p-2 bg-gray-50 rounded">${d}</div>`;
            }
            
            let bodyHtml = '';
            for (let d = 1; d <= daysInMonth; d++) {
                bodyHtml += `<div class="min-h-[40px] sm:min-h-[60px] bg-gray-50 rounded p-0.5 sm:p-1 space-y-0.5 sm:space-y-1">`;
                
                if (monthlyData[d]) {
                    monthlyData[d].forEach(program => {
                        const tanggal = new Date(program.tanggal);
                        const formattedDate = tanggal.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                        const formattedAnggaran = new Intl.NumberFormat('id-ID').format(program.anggaran);
                        
                        const bidangNama = program.bidang.nama;
                        const badgeColor = colorMap[bidangNama] || '#6b7280';
                        
                        bodyHtml += `
                            <div class="group relative">
                                <div onclick="openProgramDetailDashboard(${program.id})" 
                                     class="rounded h-4 sm:h-6 cursor-pointer transition-all duration-200 transform hover:scale-105 opacity-100"
                                     style="background-color: ${badgeColor};">
                                </div>
                                <div class="tooltip-monthly absolute left-1/2 -translate-x-1/2 top-full mt-2 z-50 hidden group-hover:block w-48 sm:w-64 bg-gray-900 text-white text-[10px] sm:text-xs rounded-lg shadow-xl p-2 sm:p-3 pointer-events-none">
                                    <div class="font-bold mb-1 truncate">${program.nama}</div>
                                    <div class="text-gray-300">📁 Bidang: ${program.bidang.nama}</div>
                                    <div class="text-gray-300">💰 Rp ${formattedAnggaran}</div>
                                    <div class="text-gray-300">📅 ${formattedDate}</div>
                                    <div class="text-gray-300">📊 Jenis: ${program.jenis_pengeluaran || '-'}</div>
                                    <div class="text-gray-400 text-[8px] sm:text-[10px] mt-2 italic">💡 Klik untuk lihat detail</div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                bodyHtml += `</div>`;
            }
            
            const header = document.getElementById('monthlyHeaderDashboard');
            const body = document.getElementById('monthlyBodyDashboard');
            
            header.style.gridTemplateColumns = `repeat(${daysInMonth}, 1fr)`;
            header.innerHTML = headerHtml;
            
            body.style.gridTemplateColumns = `repeat(${daysInMonth}, 1fr)`;
            body.innerHTML = bodyHtml;
            
            const emptyMsg = document.getElementById('monthlyEmptyDashboard');
            if (Object.keys(monthlyData).length === 0) {
                if (!emptyMsg) {
                    body.insertAdjacentHTML('afterend', '<p class="text-xs sm:text-sm text-gray-500 text-center py-4 sm:py-8" id="monthlyEmptyDashboard">Tidak ada program yang dilaksanakan bulan ini</p>');
                }
            } else {
                if (emptyMsg) emptyMsg.remove();
            }
            
            setTimeout(() => adjustTooltipPositionDashboard(), 100);
        }

        function switchViewDashboard(view) {
            const monthlyView = document.getElementById('monthlyViewDashboard');
            const yearlyView = document.getElementById('yearlyViewDashboard');
            const monthSelector = document.getElementById('monthSelectorDashboard');
            const btnMonthly = document.getElementById('btnMonthlyDashboard');
            const btnYearly = document.getElementById('btnYearlyDashboard');

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

        function adjustTooltipPositionDashboard() {
            document.querySelectorAll('.tooltip-monthly, .tooltip-yearly').forEach(tooltip => {
                tooltip.parentElement.addEventListener('mouseenter', function() {
                    setTimeout(() => {
                        const rect = tooltip.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        
                        tooltip.classList.remove('left-auto', 'right-0', 'left-0', 'left-1/2', '-translate-x-1/2');
                        
                        if (rect.right > viewportWidth - 10) {
                            tooltip.classList.add('left-auto', 'right-0');
                            const arrow = tooltip.querySelector('.absolute');
                            if (arrow) {
                                arrow.classList.remove('left-1/2', '-translate-x-1/2');
                                arrow.classList.add('right-4');
                            }
                        } else if (rect.left < 10) {
                            tooltip.classList.add('left-0');
                            const arrow = tooltip.querySelector('.absolute');
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

        // Fungsi untuk buka modal detail
        async function openProgramDetailDashboard(programId) {
            try {
                const response = await fetch(`/program-kerja/${programId}/detail`);
                const data = await response.json();
                
                if (data.success) {
                    const pk = data.data;
                    
                    document.getElementById('detailNamaProgram').textContent = pk.nama;
                    document.getElementById('detailBidangProgram').textContent = pk.bidang.nama;
                    document.getElementById('detailJenisProgram').textContent = pk.jenis_pengeluaran || '-';
                    document.getElementById('detailAnggaranProgram').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(pk.anggaran)}`;
                    document.getElementById('detailTanggalProgram').textContent = new Date(pk.tanggal).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    document.getElementById('detailTahunProgram').textContent = pk.tahun || '-';
                    
                    // Status dengan badge
                    const statusEl = document.getElementById('detailStatusProgram');
                    statusEl.innerHTML = `<span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-medium ${getStatusBadgeClass(pk.status)}">${pk.status_label}</span>`;
                    
                    // Show modal
                    const modal = document.getElementById('programDetailModal');
                    document.body.style.overflow = 'hidden';
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal memuat detail program kerja');
            }
        }

        function closeProgramDetailModal() {
            const modal = document.getElementById('programDetailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        function getStatusBadgeClass(status) {
            const badges = {
                'draft': 'bg-gray-100 text-gray-800',
                'diajukan': 'bg-blue-100 text-blue-800',
                'disetujui': 'bg-yellow-100 text-yellow-800',
                'dicairkan': 'bg-green-100 text-green-800',
                'ditolak': 'bg-red-100 text-red-800'
            };
            return badges[status] || 'bg-gray-100 text-gray-800';
        }

        // Close modal when clicking outside
        document.getElementById('programDetailModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeProgramDetailModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProgramDetailModal();
            }
        });

        // BAR CHART
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById("monthlyBarChart").getContext("2d");
            
            const chartDataByYear = @json($chartDataByYear);
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
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
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { 
                                stepSize: 1,
                                font: {
                                    size: window.innerWidth < 640 ? 10 : 12
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: window.innerWidth < 640 ? 10 : 12
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: window.innerWidth < 640 ? 10 : 12
                                },
                                boxWidth: window.innerWidth < 640 ? 12 : 15
                            }
                        },
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
                                        `Total: Rp ${formattedBudget}`
                                    ];
                                },
                                title: function(context) {
                                    return `Bulan ${context[0].label}`;
                                }
                            },
                            bodyFont: {
                                size: window.innerWidth < 640 ? 10 : 13
                            },
                            titleFont: {
                                size: window.innerWidth < 640 ? 11 : 14
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

            // Update chart font size on window resize
            window.addEventListener('resize', function() {
                monthlyBarChart.options.scales.y.ticks.font.size = window.innerWidth < 640 ? 10 : 12;
                monthlyBarChart.options.scales.x.ticks.font.size = window.innerWidth < 640 ? 10 : 12;
                monthlyBarChart.options.plugins.legend.labels.font.size = window.innerWidth < 640 ? 10 : 12;
                monthlyBarChart.options.plugins.legend.labels.boxWidth = window.innerWidth < 640 ? 12 : 15;
                monthlyBarChart.update();
            });
        });

        function toggleMyDebt() {
            const detail = document.getElementById('myDebtDetail');
            const eyeOpen = document.getElementById('eyeIconOpen');
            const eyeClosed = document.getElementById('eyeIconClosed');
            
            if (detail.classList.contains('hidden')) {
                detail.classList.remove('hidden');
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                detail.classList.add('hidden');
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        // Fungsi untuk modal tambah kas
        function openTambahKasModal() {
            document.getElementById('tambahKasModal').classList.remove('hidden');
            document.getElementById('tambahKasModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeTambahKasModal() {
            document.getElementById('tambahKasModal').classList.add('hidden');
            document.getElementById('tambahKasModal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        async function submitTambahKas(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('/kas/tambah', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested--With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Kas berhasil ditambahkan');
                    closeTambahKasModal();
                    window.location.reload();
                } else {
                    alert('Gagal menambahkan kas: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menambahkan kas');
            }
        }

        // Click outside untuk menutup dropdown user
        document.addEventListener('click', function(e) {
            const userMenu = document.querySelector('.group');
            if (userMenu && !userMenu.contains(e.target)) {
                const dropdown = userMenu.querySelector('.group-hover\\:block');
                if (dropdown) {
                    dropdown.classList.add('hidden');
                }
            }
        });
    </script>
</body>
</html>