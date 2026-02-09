<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PUKsystem')</title>

    <!-- Fonts & Tailwind -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        
        .sidebar-transition {
            transition: all 0.4s cubic-bezier(0.25, 0.1, 0.25, 1);
        }
        
        .menu-item {
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        
        .menu-item:hover::before {
            left: 100%;
        }
        
        .menu-item:hover {
            background: rgba(243, 244, 246, 0.6);
            transform: translateX(2px);
        }
        
        .menu-item.active {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #000000;
            font-weight: 500;
        }
        
        .collapsed-sidebar {
            width: 4rem;
        }
        
        .sidebar-text {
            transition: opacity 0.3s ease;
        }
        
        .logo-text {
            transition: all 0.3s ease;
        }
        
        .notification-pulse {
            animation: pulse-glow 2s infinite;
        }
        
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .card-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Modal Animation */
        .modal-fade-in {
            animation: modalFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .modal-fade-out {
            animation: modalFadeOut 0.25s cubic-bezier(0.4, 0, 1, 1) forwards;
        }
        
        @keyframes modalFadeOut {
            from {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
            to {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }
        }
        
        #createModal > div,
        #editModal > div {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        
        #createModal.active > div,
        #editModal.active > div {
            opacity: 1;
            transform: scale(1) translateY(0);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* User Profile Card Animation */
        .user-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .user-card:hover {
            background: rgba(249, 250, 251, 0.8);
        }

        .logout-slide-enter {
            animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .logout-slide-leave {
            animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
                max-height: 0;
            }
            to {
                opacity: 1;
                transform: translateY(0);
                max-height: 100px;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 1;
                transform: translateY(0);
                max-height: 100px;
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
                max-height: 0;
            }
        }

        .logout-btn {
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
        }

        .logout-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px -1px rgba(239, 68, 68, 0.2);
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50/50 font-sans antialiased" 
      x-data="{ 
          sidebarOpen: window.innerWidth >= 1024,
          sidebarCollapsed: false,
          toggleCollapse() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
          }
      }">

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 glass-effect shadow-xl sidebar-transition"
         :class="[
             sidebarOpen ? 'translate-x-0' : '-translate-x-full',
             sidebarCollapsed ? 'w-16' : 'w-64'
         ]"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         @click.away="window.innerWidth < 1024 && (sidebarOpen = false)">

        <!-- Logo Section -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200/60">
            <div class="flex items-center space-x-3 min-w-0 flex-1">
                <img src="{{ asset('images/logostep.png') }}" 
                     alt="PUKsystem Logo"
                     class="w-10 h-10 max-w-none object-contain">
                <span class="text-lg font-semibold text-gray-800 me-2 truncate logo-text"
                      :class="sidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100'">
                    Gudang MDD
                </span>
            </div>
        </div>  

        <!-- Menu Navigation -->
        @php
            $userRole = Auth::user()->role->nama ?? '';
        @endphp
        
        <nav class="mt-6 px-3 space-y-1 overflow-y-auto" style="height: calc(100vh - 180px);">
            <a href="{{ route('dashboard') }}" 
               class="menu-item flex items-center px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Dashboard' : ''">
                <div class="w-5 h-5 mr-3 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    Dashboard
                </span>
            </a>

            <!-- Divider -->
            <div class="py-2">
                <div class="border-t border-gray-200/60"></div>
            </div>


            @if(in_array($userRole, ['superadmin']))
                <a href="{{ route('users.index') }}" 
                class="menu-item flex items-center px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('users.*') ? 'active' : '' }}"
                :title="sidebarCollapsed ? 'Users' : ''">
                    <div class="w-5 h-5 mr-3 flex-shrink-0">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m0 0V9a3 3 0 00-6 0v4.341"/>
                        </svg>
                    </div>
                    <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Users
                    </span>
                </a>

                <a href="{{ route('roles.index') }}" 
                class="menu-item flex items-center px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                :title="sidebarCollapsed ? 'Roles' : ''">
                    <div class="w-5 h-5 mr-3 flex-shrink-0">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Roles
                    </span>
                </a>

                <a href="{{ route('bidangs.index') }}" 
                class="menu-item flex items-center px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('bidangs.*') ? 'active' : '' }}"
                :title="sidebarCollapsed ? 'Bidang' : ''">
                    <div class="w-5 h-5 mr-3 flex-shrink-0">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/>
                        </svg>
                    </div>
                    <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Bidang
                    </span>
                </a>
            @endif

            <!-- Divider -->
            <div class="py-2">
                <div class="border-t border-gray-200/60"></div>
            </div>

            <!-- Menu Program Kerja (Standalone - untuk superadmin & admin) -->
            @if(in_array($userRole, ['superadmin', 'admin']))
            <a href="{{ route('program-kerja.index') }}" 
               class="menu-item flex items-center px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('program-kerja.*') && !request()->routeIs('bendahara.*') && !request()->routeIs('ketua.*') && !request()->routeIs('pencairan.*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Program Kerja' : ''">
                <div class="w-5 h-5 mr-3 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    Program Kerja
                </span>
            </a>

            <!-- Menu Pengajuan Budget (Standalone - untuk superadmin & admin) -->
            <a href="{{ route('pengajuan-budget.index') }}" 
               class="menu-item flex items-center px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('pengajuan-budget.*') && !request()->routeIs('bendahara.pengajuan.*') && !request()->routeIs('ketua.pengajuan.*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Pengajuan Budget' : ''">
                <div class="w-5 h-5 mr-3 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    Pengajuan Budget
                </span>
            </a>
            @endif

            

            <!-- ✅ Menu Events (untuk superadmin & admin) -->
            @if(in_array($userRole, ['superadmin', 'admin']))
            <a href="{{ route('events.index') }}" 
            class="menu-item flex items-center px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('events.*') ? 'active' : '' }}"
            :title="sidebarCollapsed ? 'Events' : ''">
                <div class="w-5 h-5 mr-3 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    Events
                </span>
            </a>
            @endif

            <!-- ✅ Menu Surat Masuk (khusus sekretaris) -->
           @if(in_array($userRole, ['superadmin', 'sekretaris']))
            <a href="{{ route('surat-masuk.index') }}" 
            class="menu-item flex items-center px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('surat-masuk.*') ? 'active' : '' }}"
            :title="sidebarCollapsed ? 'Surat Masuk' : ''">
                <div class="w-5 h-5 mr-3 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    Surat Masuk
                </span>
            </a>
            @endif

            <!-- Divider -->
            <div class="py-2">
                <div class="border-t border-gray-200/60"></div>
            </div>

            <!-- ✅ Menu Hutang (Dropdown Group - untuk superadmin, admin bidang 4, bendahara) -->
            @php
                $isBidang4 = auth()->user()->bidang_id == 4;
                $canAccessHutang = $userRole === 'superadmin' || $userRole === 'bendahara' || ($userRole === 'admin' && $isBidang4);
            @endphp
            
            @if($canAccessHutang)
            <div x-data="{ open: {{ request()->routeIs('pengajuan-hutang.*') || request()->routeIs('list-hutang') || request()->routeIs('bendahara.hutang.*') ? 'true' : 'false' }} }">
                <!-- Parent Menu - Hutang -->
                <button @click="open = !open" 
                        class="menu-item w-full flex items-center justify-between px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('pengajuan-hutang.*') || request()->routeIs('list-hutang') || request()->routeIs('bendahara.hutang.*') ? 'active' : '' }}"
                        :title="sidebarCollapsed ? 'Hutang' : ''">
                    <div class="flex items-center flex-1">
                        <div class="w-5 h-5 mr-3 flex-shrink-0">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                            Hutang
                        </span>
                    </div>
                    <!-- Dropdown Arrow -->
                    <svg class="w-4 h-4 transition-transform duration-200 sidebar-text" 
                        :class="[
                            sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100',
                            open ? 'transform rotate-180' : ''
                        ]" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Submenu Dropdown -->
                <div x-show="open" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-8 mt-1 space-y-1 sidebar-text"
                    :class="sidebarCollapsed ? 'hidden' : ''">
                    
                    <!-- Submenu: Pengajuan Hutang (untuk superadmin & admin bidang 4) -->
                    @if($userRole === 'superadmin' || ($userRole === 'admin' && $isBidang4))
                    <a href="{{ route('pengajuan-hutang.index') }}" 
                       class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('pengajuan-hutang.*') && !request()->routeIs('bendahara.hutang.*') && !request()->routeIs('ketua.hutang.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Pengajuan Hutang</span>
                    </a>
                    @endif

                    <!-- ✅ Submenu: Verifikasi Hutang (untuk bendahara) -->
                    @if($userRole === 'bendahara')
                    <a href="{{ route('bendahara.hutang.index') }}" 
                       class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('bendahara.hutang.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Verifikasi Hutang</span>
                    </a>
                    @endif

                    <!-- ✅ Submenu: Hutang Aktif (untuk superadmin & bendahara) -->
                    @if($userRole === 'superadmin' || $userRole === 'bendahara')
                    <a href="{{ route('list-hutang') }}" 
                       class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('list-hutang') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span>Hutang Aktif</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Menu Group: Verifikasi -->
            @php
                $allowedRoles = ['superadmin', 'bendahara', 'sekretaris', 'ketua'];
            @endphp

            @if(in_array($userRole, $allowedRoles))
            <div x-data="{ open: {{ request()->routeIs('bendahara.*') || request()->routeIs('ketua.*') || request()->routeIs('pencairan.*') ? 'true' : 'false' }} }">
                <!-- Parent Menu -->
                <button @click="open = !open" 
                        class="menu-item w-full flex items-center justify-between px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('bendahara.*') || request()->routeIs('ketua.*') || request()->routeIs('pencairan.*') ? 'active' : '' }}"
                        :title="sidebarCollapsed ? 'Verifikasi' : ''">
                    <div class="flex items-center flex-1">
                        <div class="w-5 h-5 mr-3 flex-shrink-0">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                            Verifikasi
                        </span>
                    </div>
                    <!-- Dropdown Arrow -->
                    <svg class="w-4 h-4 transition-transform duration-200 sidebar-text" 
                        :class="[
                            sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100',
                            open ? 'transform rotate-180' : ''
                        ]" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Submenu -->
                <div x-show="open" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-8 mt-1 space-y-1 sidebar-text"
                    :class="sidebarCollapsed ? 'hidden' : ''">

                    @if($userRole === 'bendahara')
                        
                        <!-- Submenu untuk Bendahara - Pengajuan Budget -->
                        <a href="{{ route('bendahara.pengajuan.index') }}" 
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('bendahara.pengajuan.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Pengajuan Budget</span>
                        </a>

                        <!-- Submenu Pencairan untuk Bendahara -->
                        <a href="{{ route('pencairan.index') }}" 
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('pencairan.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Pencairan Dana</span>
                        </a>
                    @endif

                    @if($userRole === 'ketua')
                        <!-- Submenu untuk Ketua - Pengajuan Budget -->
                        <a href="{{ route('ketua.pengajuan.index') }}" 
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('ketua.pengajuan.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Pengajuan Budget</span>
                        </a>

                        <!-- Submenu Ketua - Pengajuan Hutang -->
                        <a href="{{ route('ketua.hutang.index') }}" 
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('ketua.hutang.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Pengajuan Hutang</span>
                        </a>
                    @endif

                   @if($userRole === 'superadmin')

                        <!-- Group Verifikasi -->
                        <div class="mt-2">
                            <!-- Parent Bendahara -->
                            <div x-data="{ open: {{ request()->routeIs('bendahara.*') || request()->routeIs('pencairan.*') ? 'true' : 'false' }} }">

                                <!-- Tombol Parent -->
                                <button @click="open = !open"
                                    class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg 
                                        text-gray-700 hover:bg-gray-100 transition">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="12" cy="12" r="6" />
                                        </svg>

                                        <span>Bendahara</span>
                                    </div>

                                    <!-- Icon panah -->
                                    <svg class="w-4 h-4 transform transition"
                                        :class="open ? 'rotate-90' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                <!-- Submenu Bendahara -->
                                <div x-show="open" x-collapse class="ml-6 mt-1 space-y-1">

                                    

                                    <a href="{{ route('bendahara.pengajuan.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-lg
                                            hover:bg-gray-100 text-gray-600 transition
                                            {{ request()->routeIs('bendahara.pengajuan.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                                        Pengajuan Budget
                                    </a>

                                    <a href="{{ route('pencairan.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-lg
                                            hover:bg-gray-100 text-gray-600 transition
                                            {{ request()->routeIs('pencairan.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                                        Pencairan
                                    </a>

                                </div>
                            </div>


                            <!-- Parent Ketua -->
                            <div class="mt-2" x-data="{ open: {{ request()->routeIs('ketua.*') ? 'true' : 'false' }} }">

                                <button @click="open = !open"
                                    class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg 
                                        text-gray-700 hover:bg-gray-100 transition">

                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="12" cy="12" r="6" />
                                        </svg>

                                        <span>Ketua</span>
                                    </div>

                                    <svg class="w-4 h-4 transform transition"
                                        :class="open ? 'rotate-90' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                <!-- Submenu Ketua -->
                                <div x-show="open" x-collapse class="ml-6 mt-1 space-y-1">                    
                                    <a href="{{ route('ketua.pengajuan.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-lg
                                            hover:bg-gray-100 text-gray-600 transition
                                            {{ request()->routeIs('ketua.pengajuan.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                                        Pengajuan Budget
                                    </a>

                                    <a href="{{ route('ketua.hutang.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-lg
                                            hover:bg-gray-100 text-gray-600 transition
                                            {{ request()->routeIs('ketua.hutang.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                                        Pengajuan Hutang
                                    </a>

                                </div>
                            </div>

                        </div>
                    @endif

                    @if($userRole === 'sekretaris')
                        <!-- Submenu untuk Sekretaris (hanya lihat, tidak bisa approval) -->
                        <a href="{{ route('program-kerja.index') }}" 
                           class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('program-kerja.index') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Semua Program Kerja</span>
                        </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Menu History (Dropdown untuk Semua Role termasuk admin bidang) -->
            @if(in_array($userRole, ['superadmin', 'bendahara', 'sekretaris', 'ketua', 'admin']))
            <div x-data="{ open: {{ request()->routeIs('history.*') || request()->routeIs('kas.*') ? 'true' : 'false' }} }">
                <!-- Parent Menu - History -->
                <button @click="open = !open" 
                        class="menu-item flex items-center justify-between w-full px-3 py-3 rounded-xl text-gray-700 group {{ request()->routeIs('history.*') || request()->routeIs('kas.*') ? 'active' : '' }}"
                        :title="sidebarCollapsed ? 'History' : ''">
                    <div class="flex items-center flex-1">
                        <div class="w-5 h-5 mr-3 flex-shrink-0">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                            History
                        </span>
                    </div>
                    <!-- Chevron Icon -->
                    <svg class="w-4 h-4 transition-transform sidebar-text" 
                        :class="{ 'rotate-180': open, 'opacity-0 w-0 overflow-hidden': sidebarCollapsed }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Submenu Dropdown -->
                <div x-show="open" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-8 mt-1 space-y-1 sidebar-text"
                    :class="sidebarCollapsed ? 'hidden' : ''">
                    
                    <!-- Submenu: Program Kerja -->
                    <a href="{{ route('history.program-kerja') }}" 
                    class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('history.program-kerja') || request()->routeIs('history.program-kerja.show') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Program Kerja
                    </a>

                    <!-- Submenu Pengajuan Budget -->
                    <a href="{{ route('history.pengajuan-budget') }}" 
                    class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('history.pengajuan-budget') || request()->routeIs('history.pengajuan-budget.show') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pengajuan Budget
                    </a>

                    <!-- Submenu Hutang Lunas -->
                    <a href="{{ route('history.hutang') }}" 
                    class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('history.hutang') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Hutang Lunas
                    </a>

                    <!-- Submenu: Kas -->
                    <a href="{{ route('kas.index') }}" 
                    class="submenu-item flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->routeIs('kas.*') ? 'bg-gray-100 text-gray-900 font-semibold' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Kas
                    </a>
                </div>
            </div>
            @endif

            <!-- Divider -->
            <div class="py-2">
                <div class="border-t border-gray-200/60"></div>
            </div>

            <!-- Settings -->
            <a href="#" 
               class="menu-item flex items-center px-3 py-3 rounded-xl text-gray-700 group"
               :title="sidebarCollapsed ? 'Settings' : ''">
                <div class="w-5 h-5 mr-3 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="sidebar-text font-medium" :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    Settings
                </span>
            </a>
        </nav>
    </div>

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen && window.innerWidth < 1024"
         class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 lg:hidden"
         @click="sidebarOpen = false"
         x-transition.opacity></div>

    <!-- Main Content -->
    <div class="transition-all duration-400 ease-in-out"
        :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64'">
        
        <!-- Header -->
        <header class="glass-effect shadow-sm border-b border-gray-200/60 sticky top-0 z-30">
            <div class="flex items-center justify-between px-6 h-16">
                <div class="flex items-center space-x-4">
                    <!-- Hamburger Button -->
                    <button @click="if(window.innerWidth >= 1024) { sidebarCollapsed = !sidebarCollapsed } else { sidebarOpen = !sidebarOpen }"
                            class="p-2 -ml-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-3" x-data="{ openProfile: false }">

                    <!-- Notification -->
                    <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors duration-200 hover-lift">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full notification-pulse"></span>
                    </button>

                    <!-- Profile + Dropdown -->
                    <div class="relative">

                        <!-- Profile Button -->
                        <div @click="openProfile = !openProfile"
                            class="w-9 h-9 bg-gradient-to-br from-gray-700 to-gray-900 rounded-xl 
                                    flex items-center justify-center text-white font-semibold shadow-md 
                                    hover-lift cursor-pointer select-none">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>

                        <!-- Dropdown -->
                        <div x-show="openProfile"
                            x-transition.opacity
                            @click.outside="openProfile = false"
                            class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">

                            <!-- Name -->
                            <div class="px-4 py-2">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->role->nama ?? 'User' }}</p>
                            </div>

                            <div class="border-t border-gray-100 my-2"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full text-left flex items-center space-x-2 px-4 py-2 text-sm 
                                            text-red-600 hover:bg-red-50 transition rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Logout</span>
                                </button>
                            </form>

                        </div>
                    </div>

                </div>

            </div>
        </header>

        <!-- Content -->
        <main class="p-6 min-h-screen">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>