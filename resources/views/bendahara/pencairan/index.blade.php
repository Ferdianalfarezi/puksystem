@extends('layouts.app')

@section('title', 'Pencairan Dana')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Pencairan Dana</h1>
                <p class="text-gray-600 mt-1">Kelola pencairan dana untuk program kerja & pengajuan budget yang telah disetujui</p>
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Menunggu Pencairan -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Menunggu Pencairan</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $pencairanPaginated->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Anggaran -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Total Anggaran</p>
                        <p class="text-lg font-bold text-green-900 mt-1">
                            Rp {{ number_format($pencairanPaginated->sum('anggaran'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Saldo Kas -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Saldo Kas</p>
                        <p class="text-lg font-bold text-purple-900 mt-1">
                            Rp {{ number_format($kasGlobal->saldo, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Bidang Terlibat -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-orange-600 uppercase tracking-wider">Bidang Terlibat</p>
                        <p class="text-2xl font-bold text-orange-900 mt-1">
                            {{ $pencairanPaginated->pluck('bidang')->unique()->count() }}
                        </p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-start md:space-x-4 space-y-3 md:space-y-0">
            <div class="w-full md:w-1/2 lg:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchInput"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Cari program/pengajuan..."
                    onkeyup="searchTable()"
                >
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="pencairanTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Pengeluaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Anggaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Disetujui</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="pencairanTableBody">
                    @forelse($pencairanPaginated as $index => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $pencairanPaginated->firstItem() + $index }}</td>
                            
                            <!-- Tipe -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item['type'] === 'program_kerja')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        Program Kerja
                                    </span>
                                @elseif($item['type'] === 'pengajuan_budget')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Pengajuan Budget
                                    </span>
                                @elseif($item['type'] === 'pengajuan_hutang')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Pengajuan Hutang
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Bidang -->
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $item['bidang'] }}
                                </span>
                            </td>
                            
                            <!-- Nama -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item['nama'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    oleh {{ $item['submitted_by'] }}
                                </div>
                            </td>
                            
                            <!-- Jenis Pengeluaran -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item['jenis_pengeluaran'])
                                    @php
                                        $jenisBadgeClass = match($item['jenis_pengeluaran']) {
                                            'Kesekretariatan' => 'bg-blue-100 text-blue-800',
                                            'Perjalanan Dinas' => 'bg-purple-100 text-purple-800',
                                            'Aksi' => 'bg-green-100 text-green-800',
                                            'Dana Sosial', 'Dansos Duka', 'Dansos Banjir', 'Dansos Ekternal' => 'bg-pink-100 text-pink-800',
                                            'Pendidikan' => 'bg-yellow-100 text-yellow-800',
                                            'Rapat', 'Rapat GM' => 'bg-gray-100 text-gray-800',
                                            'COS DPP' => 'bg-indigo-100 text-indigo-800',
                                            'Iuaran FKJ', 'Iuran GM' => 'bg-orange-100 text-orange-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jenisBadgeClass }}">
                                        {{ $item['jenis_pengeluaran'] }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <!-- Anggaran -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-green-600">
                                    Rp {{ number_format($item['anggaran'], 0, ',', '.') }}
                                </div>
                            </td>
                            
                            <!-- Tanggal -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item['tanggal'] ? $item['tanggal']->format('d M Y') : '-' }}
                            </td>
                            
                            <!-- Disetujui -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item['reviewed_at_ketua'])
                                    <div class="text-xs text-gray-600">
                                        {{ $item['reviewed_at_ketua']->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $item['reviewed_at_ketua']->format('H:i') }} WIB
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openCairkanModal('{{ $item['type'] }}', {{ $item['id'] }}, '{{ $item['nama'] }}', {{ $item['anggaran'] }})"
                                            class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 transition">
                                        Cairkan
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Tidak ada yang menunggu pencairan</p>
                                <p class="text-gray-500 text-sm">Semua program kerja & pengajuan budget sudah dicairkan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pencairanPaginated->hasPages())
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                {{ $pencairanPaginated->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Cairkan Modal -->
@include('bendahara.pencairan.cairkan-modal')

@endsection

@push('scripts')
<script>
    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const tableBody = document.getElementById('pencairanTableBody');
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

    let currentType = null;
    let currentId = null;
    let currentName = '';
    let currentAnggaran = 0;

    function openCairkanModal(type, id, name, anggaran) {
        currentType = type;
        currentId = id;
        currentName = name;
        currentAnggaran = anggaran;
        
        document.getElementById('cairkanProgramName').textContent = name;
        document.getElementById('cairkanProgramAnggaran').textContent = 'Rp ' + formatRupiah(anggaran);
        document.getElementById('jumlahDicairkan').value = anggaran;
        document.getElementById('jumlahDicairkan').max = anggaran;
        document.getElementById('metodePencairan').value = 'transfer_bank';
        document.getElementById('nomorReferensi').value = '';
        document.getElementById('catatanPencairan').value = '';
        
        // Clear errors
        clearErrors();
        
        const modal = document.getElementById('cairkanModal');
        document.body.style.overflow = 'hidden';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        void modal.offsetWidth;
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
    }

    function closeCairkanModal() {
        const modal = document.getElementById('cairkanModal');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            currentType = null;
            currentId = null;
            currentName = '';
            currentAnggaran = 0;
        }, 250);
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Update display jumlah saat input berubah
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahInput = document.getElementById('jumlahDicairkan');
        if (jumlahInput) {
            jumlahInput.addEventListener('input', function() {
                const jumlah = parseFloat(this.value) || 0;
                const display = document.getElementById('jumlahDisplay');
                if (display) {
                    display.textContent = 'Rp ' + formatRupiah(jumlah);
                }
            });
        }
    });

    document.getElementById('cairkanForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);

        try {
            const response = await fetch(`/pencairan/${currentType}/${currentId}/cairkan`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                closeCairkanModal();
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
                        const errorElement = document.getElementById(`error-${key}`);
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

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCairkanModal();
        }
    });
</script>
@endpush