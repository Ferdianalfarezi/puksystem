@extends('layouts.app')

@section('title', 'Detail Program Kerja')

@section('content')
<div class="space-y-6">
    
    <!-- Breadcrumb & Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('program-kerja.index') }}" class="hover:text-black transition">Program Kerja</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-black font-medium">Detail</span>
        </div>
        <a href="{{ route('program-kerja.index') }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column - Program Info -->
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
                                    'disetujui' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Disetujui'],
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

            <!-- Card: Timeline Approval (jika sudah diajukan) -->
            @if($programKerja->submitted_at)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Timeline Approval</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Pengajuan -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-900">Program Diajukan</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $programKerja->submitted_at->format('d M Y, H:i') }} WIB
                                </p>
                                @if($programKerja->submittedBy)
                                    <p class="text-xs text-gray-500 mt-0.5">oleh {{ $programKerja->submittedBy->name }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Review Bendahara -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($programKerja->reviewed_at_bendahara)
                                    <div class="h-10 w-10 rounded-full {{ $programKerja->status == 'ditolak_bendahara' ? 'bg-red-100' : 'bg-green-100' }} flex items-center justify-center">
                                        @if($programKerja->status == 'ditolak_bendahara')
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-900">Review Bendahara</p>
                                @if($programKerja->reviewed_at_bendahara)
                                    <p class="text-xs text-gray-600 mt-1">
                                        {{ $programKerja->reviewed_at_bendahara->format('d M Y, H:i') }} WIB
                                    </p>
                                    @if($programKerja->reviewedByBendahara)
                                        <p class="text-xs text-gray-500 mt-0.5">oleh {{ $programKerja->reviewedByBendahara->name }}</p>
                                    @endif
                                    @if($programKerja->catatan_bendahara)
                                        <div class="mt-2 bg-gray-50 rounded-lg p-3">
                                            <p class="text-xs text-gray-700"><strong>Catatan:</strong> {{ $programKerja->catatan_bendahara }}</p>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-500 mt-1">Menunggu review dari bendahara</p>
                                @endif
                            </div>
                        </div>

                        <!-- Review Ketua (hanya muncul jika sudah lolos bendahara) -->
                        @if($programKerja->status != 'ditolak_bendahara' && $programKerja->reviewed_at_bendahara)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($programKerja->reviewed_at_ketua)
                                    <div class="h-10 w-10 rounded-full {{ $programKerja->status == 'ditolak_ketua' ? 'bg-red-100' : 'bg-green-100' }} flex items-center justify-center">
                                        @if($programKerja->status == 'ditolak_ketua')
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-gray-900">Approval Ketua</p>
                                @if($programKerja->reviewed_at_ketua)
                                    <p class="text-xs text-gray-600 mt-1">
                                        {{ $programKerja->reviewed_at_ketua->format('d M Y, H:i') }} WIB
                                    </p>
                                    @if($programKerja->reviewedByKetua)
                                        <p class="text-xs text-gray-500 mt-0.5">oleh {{ $programKerja->reviewedByKetua->name }}</p>
                                    @endif
                                    @if($programKerja->catatan_ketua)
                                        <div class="mt-2 bg-gray-50 rounded-lg p-3">
                                            <p class="text-xs text-gray-700"><strong>Catatan:</strong> {{ $programKerja->catatan_ketua }}</p>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-500 mt-1">Menunggu approval dari ketua</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Right Column - Actions & Info -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Card: Quick Actions -->
            @if($programKerja->isDraft())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-800 px-6 py-4">
                    <h3 class="text-lg font-bold text-white">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="openEditModal({{ $programKerja->id }})"
                            class="w-full bg-orange-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-orange-600 transition flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit Program</span>
                    </button>

                    <button onclick="submitProgram({{ $programKerja->id }})"
                            class="w-full bg-green-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-600 transition flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Ajukan Program</span>
                    </button>

                    <button onclick="deleteProgram({{ $programKerja->id }})"
                            class="w-full bg-red-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-600 transition flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Hapus Program</span>
                    </button>
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
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Terakhir Diupdate</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $programKerja->updated_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Include Edit Modal if needed -->
@include('program-kerja.edit')

@endsection

@push('scripts')
<script>
    async function openEditModal(id) {
        try {
            const response = await fetch(`/program-kerja/${id}`);
            const data = await response.json();
            
            if (data.success) {
                const pk = data.data;
                document.getElementById('editProgramId').value = pk.id;
                document.getElementById('editNama').value = pk.nama;
                document.getElementById('editAnggaran').value = pk.anggaran;
                document.getElementById('editTahun').value = pk.tahun;
                
                const modal = document.getElementById('editModal');
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
            Swal.fire('Error!', 'Gagal memuat data program kerja', 'error');
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('editForm').reset();
            document.body.style.overflow = '';
        }, 250);
    }

    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const id = document.getElementById('editProgramId').value;
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(`/program-kerja/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                closeEditModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
        }
    });

    async function deleteProgram(id) {
        const result = await Swal.fire({
            title: 'Yakin hapus?',
            text: "Program kerja ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/program-kerja/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = '/program-kerja';
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Gagal menghapus program kerja!', 'error');
            }
        }
    }

    async function submitProgram(id) {
        const result = await Swal.fire({
            title: 'Ajukan Program Kerja?',
            text: "Program kerja akan diajukan ke bendahara untuk dikonfirmasi",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ajukan!',
            cancelButtonText: 'Batal'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/program-kerja/${id}/submit`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
                Swal.fire('Error!', 'Gagal mengajukan program kerja!', 'error');
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
        }
    });
</script>
@endpush