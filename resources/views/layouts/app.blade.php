<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PUKsystem')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* ======================== SIDEBAR ======================== */
        .sidebar-bg {
            background: linear-gradient(175deg,
                #5bb8f5 0%,
                #7ecef7 25%,
                #a8dff9 55%,
                #c8ecfb 80%,
                #ddf3fd 100%
            );
        }

        .sidebar-transition {
            transition: all 0.4s cubic-bezier(0.25, 0.1, 0.25, 1);
        }

        .sidebar-logo-border {
            border-bottom: 1px solid rgba(255, 255, 255, 0.45);
        }

        /* ---- Menu items ---- */
        .menu-item {
            transition: all 0.18s ease;
            position: relative;
            overflow: hidden;
            color: rgba(12, 60, 100, 0.82);
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.45);
            color: #0c3c64;
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.65);
            color: #0c3c64;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(30, 100, 180, 0.12);
        }

        /* ---- Submenu items ---- */
        .submenu-item {
            color: rgba(12, 60, 100, 0.7) !important;
            transition: all 0.15s ease;
        }

        .submenu-item:hover {
            background: rgba(255, 255, 255, 0.4) !important;
            color: #0c3c64 !important;
        }

        .submenu-item.sub-active {
            background: rgba(255, 255, 255, 0.55) !important;
            color: #0a3058 !important;
            font-weight: 600;
        }

        /* ---- Nested parent button ---- */
        .submenu-parent-btn {
            color: rgba(12, 60, 100, 0.65);
            transition: all 0.15s ease;
        }
        .submenu-parent-btn:hover {
            background: rgba(255,255,255,0.35);
            color: #0c3c64;
        }

        /* ---- Divider ---- */
        .sidebar-divider {
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* ---- Scrollbar ---- */
        nav::-webkit-scrollbar { width: 3px; }
        nav::-webkit-scrollbar-track { background: transparent; }
        nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.35);
            border-radius: 4px;
        }

        /* ======================== HEADER ======================== */
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.97);
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        /* ======================== MODALS ======================== */
        .modal-fade-in {
            animation: modalFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.9) translateY(-20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        #createModal > div, #editModal > div {
            opacity: 0; transform: scale(0.9) translateY(-20px);
        }
        #createModal.active > div, #editModal.active > div {
            opacity: 1; transform: scale(1) translateY(0);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* ======================== MISC ======================== */
        .notification-pulse {
            animation: pulse-glow 2s infinite;
        }
        @keyframes pulse-glow {
            0%   { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
            70%  { box-shadow: 0 0 0 10px rgba(239,68,68,0); }
            100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
        }

        .logout-btn { transition: all 0.2s ease; }
        .logout-btn:hover {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        }
    </style>

    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { 'sans': ['Inter','system-ui','sans-serif'] } } }
        }
    </script>
</head>
<body class="bg-gray-50/50 font-sans antialiased"
      x-data="{
          sidebarOpen: window.innerWidth >= 1024,
          sidebarCollapsed: false
      }">

    <!-- ======================== SIDEBAR ======================== -->
    <div class="sidebar-bg fixed inset-y-0 left-0 z-50 shadow-xl sidebar-transition"
         :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'w-16' : 'w-64']"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         @click.away="window.innerWidth < 1024 && (sidebarOpen = false)">

        <!-- Logo -->
        <div class="flex items-center h-16 px-4 sidebar-logo-border">
            <div class="flex items-center space-x-3 min-w-0 flex-1">
                <div class="w-9 h-9 flex-shrink-0 bg-white/40 rounded-xl flex items-center justify-center border border-white/60 shadow-sm">
                    <img src="{{ asset('images/logostep.png') }}" alt="Logo" class="w-6 h-6 object-contain">
                </div>
                <span class="text-base font-bold text-blue-900/80 truncate sidebar-transition"
                      :class="sidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100'">
                    PUK STEP
                </span>
            </div>
        </div>

        <!-- Nav -->
        @php
            $userRole = Auth::user()->role->nama ?? '';
            $isSuperadmin  = $userRole === 'superadmin';
            $isAdmin       = $userRole === 'admin';
            $isBendahara   = $userRole === 'bendahara';
            $isSekretaris  = $userRole === 'sekretaris';
            $isKetua       = $userRole === 'ketua';

            $isBidang4     = auth()->user()->bidang_id == 4;
            $isKoorlap     = \App\Models\Koorlap::where('user_id', auth()->id())->exists();

            $showSuperadminMenu   = $isSuperadmin;
            $showProgramKerja     = in_array($userRole, ['superadmin','admin']);
            $showDanaSosial       = $isSuperadmin || $isKoorlap || auth()->user()->bidang_id == 4;
            $showEvents           = in_array($userRole, ['superadmin','admin']);
            $showSuratMasuk       = in_array($userRole, ['superadmin','sekretaris']);
            $showHutang           = $isSuperadmin || $isBendahara || ($isAdmin && $isBidang4);
            $showVerifikasi       = in_array($userRole, ['superadmin','bendahara','sekretaris','ketua']);
            $showHistory          = in_array($userRole, ['superadmin','bendahara','sekretaris','ketua','admin']);

            $hasMainMenuBlock     = $showProgramKerja || $showDanaSosial || $showEvents || $showSuratMasuk;
            $hasTransaksiBlock    = $showHutang || $showVerifikasi;
        @endphp

        <nav class="mt-3 px-2 space-y-0.5 overflow-y-auto" style="height: calc(100vh - 130px);">

            <!-- Dashboard — always visible -->
            <a href="{{ route('dashboard') }}"
               class="menu-item flex items-center px-3 py-2.5 rounded-xl group {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Dashboard' : ''">
                <div class="w-5 h-5 mr-3 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    Dashboard
                </span>
            </a>

            <!-- ====== SUPERADMIN BLOCK ====== -->
            @if($showSuperadminMenu)

                <div class="pt-2 pb-1 px-3" :class="sidebarCollapsed ? 'hidden' : ''">
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-blue-800/50">Master</span>
                </div>

                <a href="{{ route('users.index') }}"
                   class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('users.*') ? 'active' : '' }}"
                   :title="sidebarCollapsed ? 'Users' : ''">
                    <div class="w-5 h-5 mr-3 flex-shrink-0">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m0 0V9a3 3 0 00-6 0v4.341"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Users</span>
                </a>

                <a href="{{ route('roles.index') }}"
                   class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                   :title="sidebarCollapsed ? 'Roles' : ''">
                    <div class="w-5 h-5 mr-3 flex-shrink-0">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Roles</span>
                </a>

                <a href="{{ route('koorlaps.index') }}"
                   class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('koorlaps.*') ? 'active' : '' }}"
                   :title="sidebarCollapsed ? 'Koorlap' : ''">
                    <div class="w-5 h-5 mr-3 flex-shrink-0">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 104 0M9 5a2 2 0 014 0m-5 7l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Koorlap</span>
                </a>

                <a href="{{ route('bidangs.index') }}"
                   class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('bidangs.*') ? 'active' : '' }}"
                   :title="sidebarCollapsed ? 'Bidang' : ''">
                    <div class="w-5 h-5 mr-3 flex-shrink-0">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Bidang</span>
                </a>

            @endif

            <!-- ====== MAIN MENU BLOCK ====== -->
            @if($hasMainMenuBlock)

                @if($showSuperadminMenu)
                <div class="py-1.5 px-1">
                    <div class="border-t sidebar-divider"></div>
                </div>
                @endif

                <div class="pt-2 pb-1 px-3" :class="sidebarCollapsed ? 'hidden' : ''">
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-blue-800/50">Menu</span>
                </div>

                @if($showProgramKerja)
                    <a href="{{ route('program-kerja.index') }}"
                       class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('program-kerja.*') && !request()->routeIs('history.*') ? 'active' : '' }}"
                       :title="sidebarCollapsed ? 'Program Kerja' : ''">
                        <div class="w-5 h-5 mr-3 flex-shrink-0">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Program Kerja</span>
                    </a>

                    <a href="{{ route('pengajuan-budget.index') }}"
                       class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('pengajuan-budget.*') && !request()->routeIs('bendahara.pengajuan.*') && !request()->routeIs('ketua.pengajuan.*') && !request()->routeIs('history.*') ? 'active' : '' }}"
                       :title="sidebarCollapsed ? 'Pengajuan Budget' : ''">
                        <div class="w-5 h-5 mr-3 flex-shrink-0">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Pengajuan Budget</span>
                    </a>

                    <a href="{{ route('dispensasi.index') }}"
                       class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('dispensasi.*') && !request()->routeIs('sekretaris.dispensasi.*') && !request()->routeIs('ketua.dispensasi.*') ? 'active' : '' }}"
                       :title="sidebarCollapsed ? 'Dispensasi' : ''">
                        <div class="w-5 h-5 mr-3 flex-shrink-0">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Dispensasi</span>
                    </a>
                @endif

                @if($showDanaSosial)
                    <a href="{{ route('dana-sosial.index') }}"
                       class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('dana-sosial.*') && !request()->routeIs('dana-sosial.history*') ? 'active' : '' }}"
                       :title="sidebarCollapsed ? 'Dana Sosial' : ''">
                        <div class="w-5 h-5 mr-3 flex-shrink-0">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Dana Sosial</span>
                    </a>
                @endif

                @if($showEvents)
                    <a href="{{ route('events.index') }}"
                       class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('events.*') ? 'active' : '' }}"
                       :title="sidebarCollapsed ? 'Events' : ''">
                        <div class="w-5 h-5 mr-3 flex-shrink-0">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Events</span>
                    </a>
                @endif

                @if($showSuratMasuk)
                    <a href="{{ route('surat-masuk.index') }}"
                       class="menu-item flex items-center px-3 py-2.5 rounded-xl {{ request()->routeIs('surat-masuk.*') ? 'active' : '' }}"
                       :title="sidebarCollapsed ? 'Surat Masuk' : ''">
                        <div class="w-5 h-5 mr-3 flex-shrink-0">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Surat Masuk</span>
                    </a>
                @endif

            @endif

            <!-- ====== TRANSAKSI BLOCK ====== -->
            @if($hasTransaksiBlock)

                <div class="py-1.5 px-1">
                    <div class="border-t sidebar-divider"></div>
                </div>

                <div class="pt-2 pb-1 px-3" :class="sidebarCollapsed ? 'hidden' : ''">
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-blue-800/50">Transaksi</span>
                </div>

                @if($showHutang)
                <div x-data="{ open: {{ request()->routeIs('pengajuan-hutang.*') || request()->routeIs('list-hutang') || request()->routeIs('bendahara.hutang.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl {{ (request()->routeIs('pengajuan-hutang.*') || request()->routeIs('list-hutang') || request()->routeIs('bendahara.hutang.*')) && !request()->routeIs('history.*') ? 'active' : '' }}"
                            :title="sidebarCollapsed ? 'Hutang' : ''">
                        <div class="flex items-center flex-1">
                            <div class="w-5 h-5 mr-3 flex-shrink-0">
                                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Hutang</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200 opacity-50 flex-shrink-0"
                             :class="[sidebarCollapsed ? 'hidden' : '', open ? 'rotate-180' : '']"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="ml-8 mt-0.5 space-y-0.5"
                         :class="sidebarCollapsed ? 'hidden' : ''">

                        @if($isSuperadmin || ($isAdmin && $isBidang4))
                        <a href="{{ route('pengajuan-hutang.index') }}"
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('pengajuan-hutang.*') && !request()->routeIs('bendahara.hutang.*') ? 'sub-active' : '' }}">
                            <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Pengajuan Hutang
                        </a>
                        @endif

                        @if($isBendahara)
                        <a href="{{ route('bendahara.hutang.index') }}"
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('bendahara.hutang.*') ? 'sub-active' : '' }}">
                            <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Verifikasi Hutang
                        </a>
                        @endif

                        @if($isSuperadmin || $isBendahara)
                        <a href="{{ route('list-hutang') }}"
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('list-hutang') ? 'sub-active' : '' }}">
                            <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Hutang Aktif
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                @if($showVerifikasi)
                <div x-data="{ open: {{ request()->routeIs('bendahara.*') || request()->routeIs('ketua.*') || request()->routeIs('sekretaris.dispensasi.*') || request()->routeIs('pencairan.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl {{ request()->routeIs('bendahara.*') || request()->routeIs('ketua.*') || request()->routeIs('sekretaris.dispensasi.*') || request()->routeIs('pencairan.*') ? 'active' : '' }}"
                            :title="sidebarCollapsed ? 'Verifikasi' : ''">
                        <div class="flex items-center flex-1">
                            <div class="w-5 h-5 mr-3 flex-shrink-0">
                                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Verifikasi</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200 opacity-50 flex-shrink-0"
                             :class="[sidebarCollapsed ? 'hidden' : '', open ? 'rotate-180' : '']"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="ml-8 mt-0.5 space-y-0.5"
                         :class="sidebarCollapsed ? 'hidden' : ''">

                        @if($isBendahara)
                            <a href="{{ route('bendahara.pengajuan.index') }}"
                               class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('bendahara.pengajuan.*') ? 'sub-active' : '' }}">
                                <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Pengajuan Budget
                            </a>
                            <a href="{{ route('pencairan.index') }}"
                               class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('pencairan.*') ? 'sub-active' : '' }}">
                                <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Pencairan Dana
                            </a>
                        @endif

                        @if($isSekretaris)
                            <a href="{{ route('sekretaris.dispensasi.index') }}"
                               class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('sekretaris.dispensasi.*') ? 'sub-active' : '' }}">
                                <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Dispensasi
                            </a>
                        @endif

                        @if($isKetua)
                            <a href="{{ route('ketua.pengajuan.index') }}"
                               class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('ketua.pengajuan.*') ? 'sub-active' : '' }}">
                                <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Pengajuan Budget
                            </a>
                            <a href="{{ route('ketua.hutang.index') }}"
                               class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('ketua.hutang.*') ? 'sub-active' : '' }}">
                                <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Pengajuan Hutang
                            </a>
                            <a href="{{ route('ketua.dispensasi.index') }}"
                               class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('ketua.dispensasi.*') ? 'sub-active' : '' }}">
                                <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Dispensasi
                            </a>
                        @endif

                        @if($isSuperadmin)
                            <div x-data="{ open: {{ request()->routeIs('bendahara.*') || request()->routeIs('pencairan.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                        class="submenu-parent-btn flex items-center justify-between w-full px-3 py-2 text-xs rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700/40 flex-shrink-0"></span>
                                        <span>Bendahara</span>
                                    </div>
                                    <svg class="w-3 h-3 transform transition" :class="open ? 'rotate-90' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse class="ml-4 mt-0.5 space-y-0.5">
                                    <a href="{{ route('bendahara.pengajuan.index') }}"
                                       class="submenu-item flex px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('bendahara.pengajuan.*') ? 'sub-active' : '' }}">Pengajuan Budget</a>
                                    <a href="{{ route('pencairan.index') }}"
                                       class="submenu-item flex px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('pencairan.*') ? 'sub-active' : '' }}">Pencairan Dana</a>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('sekretaris.dispensasi.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                        class="submenu-parent-btn flex items-center justify-between w-full px-3 py-2 text-xs rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700/40 flex-shrink-0"></span>
                                        <span>Sekretaris</span>
                                    </div>
                                    <svg class="w-3 h-3 transform transition" :class="open ? 'rotate-90' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse class="ml-4 mt-0.5 space-y-0.5">
                                    <a href="{{ route('sekretaris.dispensasi.index') }}"
                                       class="submenu-item flex px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('sekretaris.dispensasi.*') ? 'sub-active' : '' }}">Dispensasi</a>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('ketua.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                        class="submenu-parent-btn flex items-center justify-between w-full px-3 py-2 text-xs rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700/40 flex-shrink-0"></span>
                                        <span>Ketua</span>
                                    </div>
                                    <svg class="w-3 h-3 transform transition" :class="open ? 'rotate-90' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse class="ml-4 mt-0.5 space-y-0.5">
                                    <a href="{{ route('ketua.pengajuan.index') }}"
                                       class="submenu-item flex px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('ketua.pengajuan.*') ? 'sub-active' : '' }}">Pengajuan Budget</a>
                                    <a href="{{ route('ketua.hutang.index') }}"
                                       class="submenu-item flex px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('ketua.hutang.*') ? 'sub-active' : '' }}">Pengajuan Hutang</a>
                                    <a href="{{ route('ketua.dispensasi.index') }}"
                                       class="submenu-item flex px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('ketua.dispensasi.*') ? 'sub-active' : '' }}">Dispensasi</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

            @endif

            <!-- ====== HISTORY BLOCK ====== -->
            @if($showHistory)

                <div class="py-1.5 px-1">
                    <div class="border-t sidebar-divider"></div>
                </div>

                <div class="pt-2 pb-1 px-3" :class="sidebarCollapsed ? 'hidden' : ''">
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-blue-800/50">Riwayat</span>
                </div>

                <div x-data="{ open: {{ request()->routeIs('history.*') || request()->routeIs('kas.*') || request()->routeIs('dana-sosial.history*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl {{ request()->routeIs('history.*') || request()->routeIs('kas.*') || request()->routeIs('dana-sosial.history*') ? 'active' : '' }}"
                            :title="sidebarCollapsed ? 'History' : ''">
                        <div class="flex items-center flex-1">
                            <div class="w-5 h-5 mr-3 flex-shrink-0">
                                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">History</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200 opacity-50 flex-shrink-0"
                             :class="[sidebarCollapsed ? 'hidden' : '', open ? 'rotate-180' : '']"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="ml-8 mt-0.5 space-y-0.5"
                         :class="sidebarCollapsed ? 'hidden' : ''">

                        <a href="{{ route('history.program-kerja') }}"
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('history.program-kerja*') ? 'sub-active' : '' }}">
                            <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Program Kerja
                        </a>
                        <a href="{{ route('history.pengajuan-budget') }}"
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('history.pengajuan-budget*') ? 'sub-active' : '' }}">
                            <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pengajuan Budget
                        </a>
                        <a href="{{ route('history.hutang') }}"
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('history.hutang') ? 'sub-active' : '' }}">
                            <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Hutang Lunas
                        </a>
                        <a href="{{ route('kas.index') }}"
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('kas.*') ? 'sub-active' : '' }}">
                            <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Kas
                        </a>
                        <a href="{{ route('dana-sosial.history') }}"
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('dana-sosial.history*') ? 'sub-active' : '' }}">
                            <svg class="w-3.5 h-3.5 mr-2 opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Dana Sosial
                        </a>
                    </div>
                </div>
            @endif

            <!-- Settings always last -->
            <div class="py-1.5 px-1">
                <div class="border-t sidebar-divider"></div>
            </div>

            <a href="#"
               class="menu-item flex items-center px-3 py-2.5 rounded-xl"
               :title="sidebarCollapsed ? 'Settings' : ''">
                <div class="w-5 h-5 mr-3 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium sidebar-transition" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Settings</span>
            </a>

        </nav>

        <!-- Bottom user strip -->
        <div class="absolute bottom-0 left-0 right-0 border-t border-white/30 bg-white/15 px-3 py-2.5"
             :class="sidebarCollapsed ? 'hidden' : ''">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-white/50 border border-white/70 flex items-center justify-center text-blue-900/70 text-xs font-bold flex-shrink-0 shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-blue-900/75 truncate leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-blue-800/50 truncate">{{ Auth::user()->role->nama ?? 'User' }}</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen && window.innerWidth < 1024"
         class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 lg:hidden"
         @click="sidebarOpen = false"
         x-transition.opacity></div>

    <!-- ======================== MAIN CONTENT ======================== -->
    <div class="transition-all duration-400 ease-in-out" :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64'">

        <!-- Header -->
        <header class="glass-effect shadow-sm border-b border-gray-200/60 sticky top-0 z-30">
            <div class="flex items-center justify-between px-6 h-16">

                <button @click="if(window.innerWidth >= 1024) { sidebarCollapsed = !sidebarCollapsed } else { sidebarOpen = !sidebarOpen }"
                        class="p-2 -ml-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex items-center space-x-3" x-data="{ openProfile: false }">

                    <button class="relative p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors hover-lift">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-red-500 rounded-full notification-pulse"></span>
                    </button>

                    <div class="relative">
                        <div @click="openProfile = !openProfile"
                             class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-400 to-blue-600
                                    flex items-center justify-center text-white font-semibold text-sm
                                    shadow-md hover-lift cursor-pointer select-none">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>

                        <div x-show="openProfile"
                             x-transition.opacity
                             @click.outside="openProfile = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                            <div class="px-4 py-2">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->role->nama ?? 'User' }}</p>
                            </div>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="logout-btn w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <main class="p-6 min-h-screen">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>