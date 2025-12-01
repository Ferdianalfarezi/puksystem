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
                <p class="text-sm font-medium text-green-100 mb-2">Saldo Kas Global</p>
                <p class="text-5xl font-bold">
                    Rp {{ number_format($kasGlobal->saldo, 0, ',', '.') }}
                </p>
                <p class="text-sm text-green-100 mt-3">
                    {{ $kasGlobal->saldo >= 0 ? '✓ Saldo Positif' : '⚠ Saldo Minus (Deficit)' }}
                </p>
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
        @php
            $totalMasuk = $histories->where('jenis', 'masuk')->sum('jumlah');
            $totalKeluar = $histories->where('jenis', 'keluar')->sum('jumlah');
            $totalTransaksi = $histories->total();
        @endphp
        
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Sumber
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
                                    Uang Masuk
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                    </svg>
                                    Uang Keluar
                                </span>
                            @endif
                        </td>

                        <!-- Sumber -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium 
                                {{ $history->sumber === 'pencairan' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($history->sumber) }}
                            </span>
                        </td>

                        <!-- Keterangan -->
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-md">
                                {{ Str::limit($history->keterangan, 60) }}
                            </div>
                            @if($history->referable)
                                <div class="text-xs text-gray-500 mt-1">
                                    Ref: {{ class_basename($history->referable_type) }} #{{ $history->referable_id }}
                                </div>
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
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-600 mt-4 font-semibold">Belum ada transaksi</p>
                            <p class="text-gray-500 text-sm">Transaksi kas akan muncul di sini</p>
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