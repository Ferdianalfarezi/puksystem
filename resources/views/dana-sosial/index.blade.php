@extends('layouts.app')

@section('title', 'Dana Sosial')

@section('content')
@php
    $user = Auth::user();
    $userRole = $user->role->nama ?? '';
    $isBidangSosial = $user->bidang_id == 4;
    $isKoorlap = $userKoorlap !== null;
    $canCreate = $userRole === 'superadmin' || $isKoorlap;
    $canApprove = $userRole === 'superadmin' || $isBidangSosial;
@endphp

<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dana Sosial</h1>
                <p class="text-gray-600 mt-1">
                    @if($userRole === 'superadmin')
                        Kelola semua pengajuan dana sosial
                    @elseif($isBidangSosial)
                        Review dan approval pengajuan dana sosial
                    @elseif($isKoorlap)
                        Pengajuan dana sosial untuk anggota Anda
                    @endif
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('dana-sosial.history') }}" 
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Riwayat</span>
                </a>
                @if($canCreate)
                <button 
                    onclick="openCreateModal()"
                    class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Ajukan Dana Sosial</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Pengajuan</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $allDanaSosial->count() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Menunggu Approval -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Menunggu Approval</p>
                        <p class="text-2xl font-bold text-yellow-900 mt-1">
                            {{ $allDanaSosial->where('status', 'menunggu_persetujuan_bidang_sosial')->count() }}
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Disetujui -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Disetujui</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">
                            {{ $allDanaSosial->where('status', 'disetujui')->count() }}
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Nominal -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Total Nominal</p>
                        <p class="text-xl font-bold text-green-900 mt-1">
                            Rp {{ number_format($allDanaSosial->sum('nominal'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between md:space-x-4 space-y-3 md:space-y-0">
            @if($userRole === 'superadmin' || $isBidangSosial)
            <div class="w-full md:w-auto">
                <select id="filterKoorlap" onchange="filterByKoorlap()" class="w-full md:w-64 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">📋 Semua Koorlap</option>
                    @foreach($koorlaps as $koorlap)
                        <option value="{{ $koorlap->id }}" {{ request('koorlap_id') == $koorlap->id ? 'selected' : '' }}>
                            {{ $koorlap->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

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
                    placeholder="Cari nama user..."
                    onkeyup="searchTable()"
                >
            </div>

            <div class="w-full md:w-auto">
                <select id="filterJenis" onchange="filterByJenis()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">Semua Jenis</option>
                    <option value="rawat_inap" {{ request('jenis') == 'rawat_inap' ? 'selected' : '' }}>Rawat Inap</option>
                    <option value="duka_cita" {{ request('jenis') == 'duka_cita' ? 'selected' : '' }}>Duka Cita</option>
                    <option value="banjir" {{ request('jenis') == 'banjir' ? 'selected' : '' }}>Banjir</option>
                </select>
            </div>

            <div class="w-full md:w-auto">
                <select id="filterStatus" onchange="filterByStatus()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">Semua Status</option>
                    <option value="menunggu_persetujuan_bidang_sosial" {{ request('status') == 'menunggu_persetujuan_bidang_sosial' ? 'selected' : '' }}>Menunggu Approval</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>

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
            <table class="w-full" id="danaSosialTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        @if($userRole === 'superadmin' || $isBidangSosial)
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Koorlap</th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penerima</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nominal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Evident</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="danaSosialTableBody">
                    @forelse($danaSosials as $index => $ds)
                        <tr class="hover:bg-gray-50 transition" data-status="{{ $ds->status }}" data-jenis="{{ $ds->jenis }}">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $danaSosials->firstItem() + $index }}
                            </td>
                            
                            @if($userRole === 'superadmin' || $isBidangSosial)
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $ds->koorlap->nama }}</div>
                                <div class="text-xs text-gray-500">{{ $ds->koorlap->user->name ?? '-' }}</div>
                            </td>
                            @endif
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $ds->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $ds->user->nik ?? '-' }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ds->jenis_badge_class }}">
                                    {{ $ds->jenis_label }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($ds->nominal, 0, ',', '.') }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ds->evident)
                                    <a href="{{ asset('storage/' . $ds->evident) }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 text-sm flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        <span>Lihat</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ds->status_badge_class }}">
                                    {{ $ds->status_label }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ds->created_at->format('d M Y') }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Detail Button -->
                                    <button onclick="openDetailModal({{ $ds->id }})"
                                        class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                        title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <!-- Approve/Reject Button (Bidang Sosial & Superadmin) -->
                                    @if($canApprove && $ds->canBeApproved())
                                        <button onclick="openApprovalModal({{ $ds->id }})"
                                                class="bg-yellow-500 text-white p-2 rounded-lg hover:bg-yellow-600 transition"
                                                title="Approval">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    @endif

                                    <!-- Verify Button (Koorlap owner & Superadmin) -->
                                    @if($ds->canBeVerified() && ($userRole === 'superadmin' || ($isKoorlap && $ds->koorlap_id == $userKoorlap->id)))
                                        <button onclick="verifyDanaSosial({{ $ds->id }})"
                                                class="bg-green-500 text-white p-2 rounded-lg hover:bg-green-600 transition"
                                                title="Serahkan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($userRole === 'superadmin' || $isBidangSosial) ? '9' : '8' }}" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada pengajuan dana sosial</p>
                                @if($canCreate)
                                <p class="text-gray-500 text-sm">Klik "Ajukan Dana Sosial" untuk membuat pengajuan</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($danaSosials->hasPages() || $danaSosials->count() > 0)
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="text-sm text-gray-600 mb-3 md:mb-0">
                        @if($perPage === 'all')
                            Menampilkan <span class="font-semibold">{{ $danaSosials->total() }}</span> data
                        @else
                            Menampilkan <span class="font-semibold">{{ $danaSosials->firstItem() }}</span> 
                            sampai <span class="font-semibold">{{ $danaSosials->lastItem() }}</span> 
                            dari <span class="font-semibold">{{ $danaSosials->total() }}</span> data
                        @endif
                    </div>
                    
                    @if($perPage !== 'all')
                        {{ $danaSosials->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
@if($canCreate)
@include('dana-sosial.create')
@endif

<!-- Detail Modal -->
@include('dana-sosial.detail')

<!-- Approval Modal -->
@if($canApprove)
@include('dana-sosial.approval')
@endif

@endsection

@push('scripts')
<script>
    // ========================================
    // Filter & Search Functions
    // ========================================
    
    function changePerPage() {
        applyFilters();
    }

    function filterByKoorlap() {
        applyFilters();
    }

    function filterByJenis() {
        applyFilters();
    }

    function filterByStatus() {
        applyFilters();
    }

    function applyFilters() {
        const url = new URL(window.location.href);
        
        const perPage = document.getElementById('perPageSelect').value;
        url.searchParams.set('perPage', perPage);
        
        const koorlapSelect = document.getElementById('filterKoorlap');
        if (koorlapSelect) {
            const koorlapId = koorlapSelect.value;
            if (koorlapId) {
                url.searchParams.set('koorlap_id', koorlapId);
            } else {
                url.searchParams.delete('koorlap_id');
            }
        }
        
        const jenis = document.getElementById('filterJenis').value;
        if (jenis) {
            url.searchParams.set('jenis', jenis);
        } else {
            url.searchParams.delete('jenis');
        }
        
        const status = document.getElementById('filterStatus').value;
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        window.location.href = url.toString();
    }

    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const tableBody = document.getElementById('danaSosialTableBody');
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

    // ========================================
    // Modal Functions
    // ========================================
    
    function openCreateModal() {
        const modal = document.getElementById('createModal');
        document.getElementById('createForm').reset();
        document.getElementById('nominalWrapper').classList.add('hidden');
        clearErrors();
        document.body.style.overflow = 'hidden';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        void modal.offsetWidth;
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
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

    async function openDetailModal(id) {
        try {
            const response = await fetch(`/dana-sosial/${id}`);
            const data = await response.json();
            
            if (data.success) {
                const ds = data.data;
                
                document.getElementById('detailKoorlap').textContent = ds.koorlap.nama;
                document.getElementById('detailUser').textContent = ds.user.name;
                document.getElementById('detailNik').textContent = ds.user.nik || '-';
                document.getElementById('detailBidang').textContent = ds.user.bidang || '-';
                document.getElementById('detailJenis').textContent = ds.jenis_label;
                document.getElementById('detailNominal').textContent = 'Rp ' + Number(ds.nominal).toLocaleString('id-ID');
                document.getElementById('detailStatus').textContent = ds.status_label;
                document.getElementById('detailStatus').className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + ds.status_badge_class;
                document.getElementById('detailCreatedAt').textContent = ds.created_at;
                
                // Evident
                const evidentContainer = document.getElementById('detailEvidentContainer');
                if (ds.evident_url) {
                    evidentContainer.innerHTML = `<a href="${ds.evident_url}" target="_blank" class="text-blue-600 hover:text-blue-800">Lihat File</a>`;
                } else {
                    evidentContainer.innerHTML = '<span class="text-gray-400">Tidak ada</span>';
                }
                
                // Approval info
                const approvalInfo = document.getElementById('detailApprovalInfo');
                if (ds.approved_by_name) {
                    approvalInfo.innerHTML = `
                        <p><span class="font-medium">Disetujui oleh:</span> ${ds.approved_by_name}</p>
                        <p><span class="font-medium">Tanggal:</span> ${ds.approved_at}</p>
                        ${ds.catatan_approval ? `<p><span class="font-medium">Catatan:</span> ${ds.catatan_approval}</p>` : ''}
                    `;
                    approvalInfo.classList.remove('hidden');
                } else {
                    approvalInfo.classList.add('hidden');
                }
                
                // Verification info
                const verifyInfo = document.getElementById('detailVerifyInfo');
                if (ds.verified_by_name) {
                    verifyInfo.innerHTML = `
                        <p><span class="font-medium">Diserahkan oleh:</span> ${ds.verified_by_name}</p>
                        <p><span class="font-medium">Tanggal:</span> ${ds.verified_at}</p>
                    `;
                    verifyInfo.classList.remove('hidden');
                } else {
                    verifyInfo.classList.add('hidden');
                }
                
                const modal = document.getElementById('detailModal');
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
            Swal.fire('Error!', 'Gagal memuat data', 'error');
        }
    }

    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 250);
    }

    function openApprovalModal(id) {
        document.getElementById('approvalDanaSosialId').value = id;
        document.getElementById('approvalCatatan').value = '';
        
        // Reset radio buttons
        document.querySelectorAll('input[name="approval_action"]').forEach(radio => {
            radio.checked = false;
        });
        
        const modal = document.getElementById('approvalModal');
        document.body.style.overflow = 'hidden';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        void modal.offsetWidth;
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
    }

    function closeApprovalModal() {
        const modal = document.getElementById('approvalModal');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 250);
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // ========================================
    // Form Handlers
    // ========================================
    
    // Jenis change handler - auto fill nominal
    function onJenisChange() {
        const jenis = document.getElementById('createJenis').value;
        const nominalWrapper = document.getElementById('nominalWrapper');
        const nominalInput = document.getElementById('createNominal');
        
        if (jenis === 'duka_cita') {
            nominalWrapper.classList.remove('hidden');
            nominalInput.value = '';
            nominalInput.removeAttribute('readonly');
            nominalInput.setAttribute('required', 'required');
        } else if (jenis === 'rawat_inap') {
            nominalWrapper.classList.remove('hidden');
            nominalInput.value = '300000';
            nominalInput.setAttribute('readonly', 'readonly');
            nominalInput.removeAttribute('required');
        } else if (jenis === 'banjir') {
            nominalWrapper.classList.remove('hidden');
            nominalInput.value = '200000';
            nominalInput.setAttribute('readonly', 'readonly');
            nominalInput.removeAttribute('required');
        } else {
            nominalWrapper.classList.add('hidden');
            nominalInput.value = '';
        }
    }

    @if($userRole === 'superadmin')
    // Koorlap change handler - load users
    async function onKoorlapChange() {
        const koorlapId = document.getElementById('createKoorlapId').value;
        const userSelect = document.getElementById('createUserId');
        
        userSelect.innerHTML = '<option value="">-- Pilih Penerima --</option>';
        
        if (!koorlapId) return;
        
        try {
            const response = await fetch(`/dana-sosial/users-by-koorlap?koorlap_id=${koorlapId}`);
            const data = await response.json();
            
            if (data.success) {
                data.data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.nik || '-'}) - ${user.bidang}`;
                    userSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }
    @endif

    // Create form submit
    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);

        try {
            const response = await fetch('/dana-sosial', {
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
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
        }
    });

    // Approval form submit
    @if($canApprove)
    document.getElementById('approvalForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const id = document.getElementById('approvalDanaSosialId').value;
        const selectedRadio = document.querySelector('input[name="approval_action"]:checked');
        const action = selectedRadio ? selectedRadio.value : null;
        const catatan = document.getElementById('approvalCatatan').value;

        if (!action) {
            Swal.fire('Error!', 'Pilih aksi approval', 'error');
            return;
        }

        const confirmText = action === 'approve' ? 'Setujui pengajuan ini?' : 'Tolak pengajuan ini?';
        const result = await Swal.fire({
            title: confirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'approve' ? '#10b981' : '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: action === 'approve' ? 'Ya, Setujui' : 'Ya, Tolak',
            cancelButtonText: 'Batal'
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`/dana-sosial/${id}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action, catatan })
            });

            const data = await response.json();

            if (data.success) {
                closeApprovalModal();
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
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
        }
    });
    @endif

    // Verify function
    async function verifyDanaSosial(id) {
        const result = await Swal.fire({
            title: 'Serahkan Dana Sosial?',
            text: 'Dana sosial akan ditandai sebagai diserahkan dan dipindahkan ke riwayat.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Serahkan',
            cancelButtonText: 'Batal'
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`/dana-sosial/${id}/verify`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
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
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
        }
    }

    // Escape key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeDetailModal();
            closeApprovalModal();
        }
    });
</script>
@endpush