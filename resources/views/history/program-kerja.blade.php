@extends('layouts.app')

@section('title', 'History Program Kerja')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">History Program Kerja</h1>
                <p class="text-gray-600 mt-1">Riwayat lengkap semua program kerja dan aktivitasnya</p>
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Program -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Program</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $programKerjas->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Dicairkan -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Dicairkan</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">
                            {{ $programKerjas->where('status', 'dicairkan')->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Menunggu Pencairan -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Menunggu Pencairan</p>
                        <p class="text-2xl font-bold text-yellow-900 mt-1">
                            {{ $programKerjas->where('status', 'menunggu_pencairan')->count() }}
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Dana Dicairkan -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Total Dana</p>
                        <p class="text-lg font-bold text-purple-900 mt-1">
                            Rp {{ number_format($programKerjas->where('status', 'dicairkan')->sum('anggaran'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('history.program-kerja') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 mb-2">Cari Program</label>
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Nama program..."
                >
            </div>

            <!-- Filter Status -->
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                <select 
                    name="status"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="menunggu_konfirmasi_bendahara" {{ request('status') == 'menunggu_konfirmasi_bendahara' ? 'selected' : '' }}>Menunggu Bendahara</option>
                    <option value="menunggu_approval_ketua" {{ request('status') == 'menunggu_approval_ketua' ? 'selected' : '' }}>Menunggu Ketua</option>
                    <option value="menunggu_pencairan" {{ request('status') == 'menunggu_pencairan' ? 'selected' : '' }}>Menunggu Pencairan</option>
                    <option value="dicairkan" {{ request('status') == 'dicairkan' ? 'selected' : '' }}>Dicairkan</option>
                    <option value="ditolak_bendahara" {{ request('status') == 'ditolak_bendahara' ? 'selected' : '' }}>Ditolak Bendahara</option>
                    <option value="ditolak_ketua" {{ request('status') == 'ditolak_ketua' ? 'selected' : '' }}>Ditolak Ketua</option>
                </select>
            </div>

            <!-- Filter Bidang (Hanya untuk superadmin, bendahara, sekretaris, ketua) -->
            @php
                $userRole = Auth::user()->role->nama ?? '';
            @endphp
            
            @if(in_array($userRole, ['superadmin', 'bendahara', 'sekretaris', 'ketua']))
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 mb-2">Bidang</label>
                <select 
                    name="bidang_id"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="">Semua Bidang</option>
                    @foreach($bidangs as $bidang)
                        <option value="{{ $bidang->id }}" {{ request('bidang_id') == $bidang->id ? 'selected' : '' }}>
                            {{ $bidang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            @else
            <!-- Untuk admin, tampilkan bidangnya saja (read-only) -->
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 mb-2">Bidang</label>
                <input 
                    type="text"
                    value="{{ Auth::user()->bidang->nama ?? 'N/A' }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                    disabled
                >
            </div>
            @endif

            <!-- Filter Tahun -->
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 mb-2">Tahun</label>
                <select 
                    name="tahun"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="">Semua Tahun</option>
                    @foreach($tahuns as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="md:col-span-4 flex space-x-3">
                <button 
                    type="submit"
                    class="bg-black text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-800 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Filter</span>
                </button>
                <a 
                    href="{{ route('history.program-kerja') }}"
                    class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-300 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Program</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Anggaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pencairan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($programKerjas as $index => $pk)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $programKerjas->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium text-dark">
                                    {{ $pk->bidang->nama }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $pk->nama }}</div>
                                @if($pk->submitted_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                       {{$pk->jenis_pengeluaran }}
                                    </div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($pk->anggaran, 0, ',', '.') }}
                                </div>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pk->pencairan)
                                    <div class="text-xs text-green-600 font-semibold">
                                        Rp {{ number_format($pk->pencairan->jumlah_dicairkan, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $pk->pencairan->tanggal_pencairan->format('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Belum dicairkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openHistoryDetailModal({{ $pk->id }})"
                                        class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                        title="Detail History">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada data history</p>
                                <p class="text-gray-500 text-sm">History program kerja akan muncul di sini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($programKerjas->hasPages())
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                {{ $programKerjas->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@include('history.detail')
@endsection