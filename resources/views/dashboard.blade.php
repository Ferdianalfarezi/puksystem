@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
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

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <button onclick="location.href='{{ route('users.create') }}'" 
                    class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Add User</span>
            </button>
            
            <button onclick="location.href='{{ route('roles.create') }}'" 
                    class="bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition transform hover:scale-105 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Add Role</span>
            </button>
            
            <button onclick="location.href='{{ route('bidangs.create') }}'" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition transform hover:scale-105 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Add Bidang</span>
            </button>
            
            <button onclick="location.href='{{ route('users.index') }}'" 
                    class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition transform hover:scale-105 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                <span>Manage Users</span>
            </button>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Recent Users</h2>
                <p class="text-sm text-gray-600">Latest registered users</p>
            </div>
            <div class="p-6">
                @php
                    $recentUsers = \App\Models\User::with(['role', 'bidang'])->latest()->take(5)->get();
                @endphp
                @if($recentUsers->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentUsers as $user)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-black transition">
                            <div class="flex items-center space-x-3 flex-1">
                                <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center">
                                    <span class="text-white font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $user->username }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $user->role->nama ?? 'No Role' }} • {{ $user->bidang->nama ?? 'No Bidang' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <p class="text-gray-600 mt-4">No users yet</p>
                        <p class="text-gray-500 text-sm">Start by creating some users</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Roles Overview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Roles & Bidang Overview</h2>
                <p class="text-sm text-gray-600">User distribution</p>
            </div>
            <div class="p-6">
                @php
                    $rolesWithCount = \App\Models\Role::withCount('users')->get();
                    $bidangsWithCount = \App\Models\Bidang::withCount('users')->get();
                @endphp
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">By Role</h3>
                        <div class="space-y-2">
                            @forelse($rolesWithCount as $role)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-black rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-900">{{ $role->nama }}</span>
                                </div>
                                <span class="text-sm text-gray-600 font-semibold">{{ $role->users_count }}</span>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500">No roles found</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">By Bidang</h3>
                        <div class="space-y-2">
                            @forelse($bidangsWithCount->take(3) as $bidang)
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-900">{{ Str::limit($bidang->nama, 20) }}</span>
                                </div>
                                <span class="text-sm text-blue-600 font-semibold">{{ $bidang->users_count }}</span>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500">No bidang found</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
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