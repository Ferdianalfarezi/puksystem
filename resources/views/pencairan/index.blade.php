@extends('layouts.app')

@section('title', 'Pencairan Dana')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Pencairan Dana Program Kerja</h1>
                <p class="text-gray-600 mt-1">Kelola pencairan dana untuk program kerja yang telah disetujui</p>
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Menunggu Pencairan -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Menunggu Pencairan</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $programKerjas->total() }}</p>
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
                            Rp {{ number_format($programKerjas->sum('anggaran'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Bidang Terlibat -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Bidang Terlibat</p>
                        <p class="text-2xl font-bold text-purple-900 mt-1">
                            {{ $programKerjas->pluck('bidang_id')->unique()->count() }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    placeholder="Cari program kerja..."
                    onkeyup="searchTable()"
                >
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="programKerjaTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Program</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Anggaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Disetujui</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="programKerjaTableBody">
                    @forelse($programKerjas as $index => $pk)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $programKerjas->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-black text-white">
                                    {{ $pk->bidang->nama }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $pk->nama }}</div>
                                @if($pk->submittedBy)
                                    <div class="text-xs text-gray-500 mt-1">
                                        oleh {{ $pk->submittedBy->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-green-600">
                                    Rp {{ number_format($pk->anggaran, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pk->tanggal ? $pk->tanggal->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pk->tahun }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pk->reviewed_at_ketua)
                                    <div class="text-xs text-gray-600">
                                        {{ $pk->reviewed_at_ketua->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $pk->reviewed_at_ketua->format('H:i') }} WIB
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('pencairan.show', $pk->id) }}"
                                       class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition">
                                        Detail
                                    </a>
                                    <button onclick="openCairkanModal({{ $pk->id }}, '{{ $pk->nama }}', {{ $pk->anggaran }})"
                                            class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 transition">
                                        Cairkan
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Tidak ada program kerja yang menunggu pencairan</p>
                                <p class="text-gray-500 text-sm">Semua program kerja sudah dicairkan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($programKerjas->hasPages())
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                {{ $programKerjas->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Cairkan Modal -->
@include('pencairan.cairkan-modal')

@endsection

@push('scripts')
<script>
    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const tableBody = document.getElementById('programKerjaTableBody');
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

    let currentProgramId = null;
    let currentProgramName = '';
    let currentProgramAnggaran = 0;

    function openCairkanModal(id, name, anggaran) {
        currentProgramId = id;
        currentProgramName = name;
        currentProgramAnggaran = anggaran;
        
        document.getElementById('cairkanProgramName').textContent = name;
        document.getElementById('cairkanProgramAnggaran').textContent = 'Rp ' + formatRupiah(anggaran);
        document.getElementById('jumlahDicairkan').value = anggaran;
        document.getElementById('jumlahDicairkan').max = anggaran;
        document.getElementById('metodePencairan').value = 'transfer';
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
            currentProgramId = null;
            currentProgramName = '';
            currentProgramAnggaran = 0;
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
            const response = await fetch(`/pencairan/${currentProgramId}/cairkan`, {
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