@extends('layouts.app')

@section('title', 'Surat Masuk')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Surat Masuk</h1>
                <p class="text-gray-600 mt-1">Dokumen pengajuan budget yang telah dicairkan</p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Total Surat</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">{{ $suratMasuk->total() }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between md:space-x-4 space-y-3 md:space-y-0">
            <div class="w-full md:w-auto">
                <select id="filterBidang" onchange="filterTable()" class="w-full md:w-64 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">📋 Semua Bidang</option>
                    @foreach($bidangs as $bidang)
                        <option value="{{ $bidang->id }}">{{ $bidang->nama }}</option>
                    @endforeach
                </select>
            </div>

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
                    placeholder="Cari surat..."
                    onkeyup="searchTable()"
                >
            </div>

            <div class="w-full md:w-auto">
                <select id="filterJenisPengeluaran" onchange="filterTable()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">Semua Jenis Pengeluaran</option>
                    <option value="Kesekretariatan">Kesekretariatan</option>
                    <option value="Perjalanan Dinas">Perjalanan Dinas</option>
                    <option value="Aksi">Aksi</option>
                    <option value="Dana Sosial">Dana Sosial</option>
                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Rapat">Rapat</option>
                </select>
            </div>

            <div class="w-full md:w-auto">
                <select id="filterTahun" onchange="filterTable()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                    <option value="">Semua Tahun</option>
                    @foreach($tahuns as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
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

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="suratMasukTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Pengajuan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Pengeluaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Anggaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lampiran</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="suratMasukTableBody">
                    @forelse($suratMasuk as $index => $surat)
                        <tr class="hover:bg-gray-50 transition" 
                            data-bidang="{{ $surat->bidang_id }}"
                            data-jenis-pengeluaran="{{ $surat->jenis_pengeluaran }}"
                            data-tahun="{{ $surat->tahun }}">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $suratMasuk->firstItem() + $index }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium text-gray-900">
                                    {{ $surat->bidang->nama }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $surat->getJenisBadgeClass() }}">
                                    {{ $surat->jenis_label }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $surat->nama }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Dicairkan: {{ $surat->pencairan ? $surat->pencairan->tanggal_pencairan->format('d M Y H:i') : '-' }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($surat->jenis_pengeluaran)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $surat->getJenisPengeluaranBadgeClass() }}">
                                        {{ $surat->jenis_pengeluaran }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($surat->anggaran, 0, ',', '.') }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $surat->tanggal ? $surat->tanggal->format('d M Y') : '-' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($surat->lampiran)
                                    <a href="{{ asset('storage/' . $surat->lampiran) }}" target="_blank"
                                       class="inline-flex items-center px-3 py-1 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition text-xs font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        PDF
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openDetailModal({{ $surat->id }})"
                                        class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                        title="Detail">
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
                            <td colspan="9" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada surat masuk</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suratMasuk->hasPages() || $suratMasuk->count() > 0)
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="text-sm text-gray-600 mb-3 md:mb-0">
                        @if($perPage === 'all')
                            Menampilkan <span class="font-semibold">{{ $suratMasuk->total() }}</span> data
                        @else
                            Menampilkan <span class="font-semibold">{{ $suratMasuk->firstItem() }}</span> 
                            sampai <span class="font-semibold">{{ $suratMasuk->lastItem() }}</span> 
                            dari <span class="font-semibold">{{ $suratMasuk->total() }}</span> data
                        @endif
                    </div>
                    
                    @if($perPage !== 'all')
                        {{ $suratMasuk->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@include('sekretaris.surat-masuk.detail')

@endsection

@push('scripts')
<script>
function changePerPage() {
    const perPage = document.getElementById('perPageSelect').value;
    const url = new URL(window.location.href);
    url.searchParams.set('perPage', perPage);
    window.location.href = url.toString();
}

function searchTable() {
    filterTable();
}

function filterTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const bidangFilter = document.getElementById('filterBidang').value;
    const jenisFilter = document.getElementById('filterJenisPengeluaran').value;
    const tahunFilter = document.getElementById('filterTahun').value;
    
    const tableBody = document.getElementById('suratMasukTableBody');
    const rows = tableBody.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        
        if (cells.length === 0) continue;
        
        const bidang = row.getAttribute('data-bidang');
        const jenisPengeluaran = row.getAttribute('data-jenis-pengeluaran');
        const tahun = row.getAttribute('data-tahun');
        
        let showRow = true;
        
        // Filter bidang
        if (bidangFilter && bidang !== bidangFilter) {
            showRow = false;
        }
        
        // Filter jenis pengeluaran
        if (jenisFilter && jenisPengeluaran !== jenisFilter) {
            showRow = false;
        }
        
        // Filter tahun
        if (tahunFilter && tahun !== tahunFilter) {
            showRow = false;
        }
        
        // Search
        if (searchInput) {
            let found = false;
            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().includes(searchInput)) {
                    found = true;
                    break;
                }
            }
            if (!found) showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    }
}
</script>
@endpush