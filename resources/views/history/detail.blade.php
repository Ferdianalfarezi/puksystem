@extends('layouts.app')

@section('title', 'Detail History Program Kerja')

@section('content')
<div class="space-y-6">
    
    <!-- Breadcrumb & Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('history.program-kerja') }}" class="hover:text-black transition">History</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-black font-medium">Detail</span>
        </div>
        <a href="{{ route('history.program-kerja') }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column - Program Info & Timeline -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Card: Informasi Program -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-black to-gray-800 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Informasi Program Kerja</h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Nama Program -->
                    <div class="flex border-b border-gray-100 pb-4">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Nama Program</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base font-bold text-gray-900">{{ $programKerja->nama }}</p>
                        </div>
                    </div>

                    <!-- Bidang -->
                    <div class="flex border-b border-gray-100 pb-4">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Bidang</p>
                        </div>
                        <div class="w-2/3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-black text-white">
                                {{ $programKerja->bidang->nama }}
                            </span>
                        </div>
                    </div>

                    <!-- Anggaran -->
                    <div class="flex border-b border-gray-100 pb-4">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Anggaran</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-xl font-bold text-green-600">
                                Rp {{ number_format($programKerja->anggaran, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Tahun -->
                    <div class="flex border-b border-gray-100 pb-4">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Tahun</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-gray-900 font-medium">{{ $programKerja->tahun }}</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Status</p>
                        </div>
                        <div class="w-2/3">
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
                                $config = $statusConfig[$programKerja->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => $programKerja->status];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                {{ $config['label'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Timeline History -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Timeline Aktivitas</h2>
                </div>
                <div class="p-6">
                    @if($programKerja->histories->count() > 0)
                        <div class="space-y-4">
                            @foreach($programKerja->histories->sortByDesc('dilakukan_pada') as $history)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        @php
                                            $isSuccess = in_array($history->status_ke, ['menunggu_konfirmasi_bendahara', 'menunggu_approval_ketua', 'menunggu_pencairan', 'dicairkan']);
                                            $isRejected = in_array($history->status_ke, ['ditolak_bendahara', 'ditolak_ketua']);
                                            $bgColor = $isSuccess ? 'bg-green-100' : ($isRejected ? 'bg-red-100' : 'bg-gray-100');
                                            $iconColor = $isSuccess ? 'text-green-600' : ($isRejected ? 'text-red-600' : 'text-gray-600');
                                        @endphp
                                        <div class="h-10 w-10 rounded-full {{ $bgColor }} flex items-center justify-center">
                                            @if($isSuccess)
                                                <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @elseif($isRejected)
                                                <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $history->status_ke_label }}
                                                </p>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    {{ $history->dilakukan_pada->format('d M Y, H:i') }} WIB
                                                </p>
                                                @if($history->dilakukanOleh)
                                                    <p class="text-xs text-gray-500 mt-0.5">oleh {{ $history->dilakukanOleh->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($history->catatan)
                                            <div class="mt-2 bg-gray-50 rounded-lg p-3">
                                                <p class="text-xs text-gray-700"><strong>Catatan:</strong> {{ $history->catatan }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Belum ada history</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column - Additional Info -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Card: Informasi Pencairan -->
            @if($programKerja->pencairan)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-green-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white">Informasi Pencairan</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Jumlah Dicairkan</p>
                        <p class="text-xl font-bold text-green-600 mt-1">
                            Rp {{ number_format($programKerja->pencairan->jumlah_dicairkan, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Tanggal Pencairan</p>
                        <p class="text-sm text-gray-900 mt-1">
                            {{ $programKerja->pencairan->tanggal_pencairan->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Metode</p>
                        <p class="text-sm text-gray-900 mt-1">
                            {{ $programKerja->pencairan->metode_pencairan_label }}
                        </p>
                    </div>
                    @if($programKerja->pencairan->nomor_referensi)
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Nomor Referensi</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $programKerja->pencairan->nomor_referensi }}</p>
                    </div>
                    @endif
                    @if($programKerja->pencairan->dicairkanOleh)
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Dicairkan Oleh</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $programKerja->pencairan->dicairkanOleh->name }}</p>
                    </div>
                    @endif
                    @if($programKerja->pencairan->catatan)
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Catatan</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $programKerja->pencairan->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Card: Metadata -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-800 px-6 py-4">
                    <h3 class="text-lg font-bold text-white">Metadata</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Dibuat</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $programKerja->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    @if($programKerja->submitted_at)
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Diajukan</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $programKerja->submitted_at->format('d M Y, H:i') }} WIB</p>
                        @if($programKerja->submittedBy)
                            <p class="text-xs text-gray-500 mt-0.5">oleh {{ $programKerja->submittedBy->name }}</p>
                        @endif
                    </div>
                    @endif
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Terakhir Diupdate</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $programKerja->updated_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Total Aktivitas</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $programKerja->histories->count() }} aktivitas</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection