<!-- DETAIL Modal for Ketua -->
<div id="detailModalKetua" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Detail Pengajuan Budget</h2>
            <button onclick="closeDetailModalKetua()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-6" id="detailContentKetua">
            <div class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
            </div>
        </div>
    </div>
</div>

<style>
#detailModalKetua.active {
    opacity: 1;
}

#detailModalKetua.active > div {
    transform: scale(1);
}
</style>

<script>
async function openDetailModalKetua(id) {
    const modal = document.getElementById('detailModalKetua');
    const content = document.getElementById('detailContentKetua');
    
    document.body.style.overflow = 'hidden';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    void modal.offsetWidth;
    requestAnimationFrame(() => {
        modal.classList.add('active');
    });
    
    try {
        console.log('Fetching:', `/ketua/pengajuan/${id}`); // Debug
        
        const response = await fetch(`/ketua/pengajuan/${id}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            const pb = data.data;
            
            // Status badges
            const statusBadgeClasses = {
                'draft': 'bg-gray-100 text-gray-800',
                'menunggu_konfirmasi_bendahara': 'bg-yellow-100 text-yellow-800',
                'menunggu_approval_ketua': 'bg-blue-100 text-blue-800',
                'menunggu_pencairan': 'bg-purple-100 text-purple-800',
                'dicairkan': 'bg-green-100 text-green-800',
                'ditolak_bendahara': 'bg-red-100 text-red-800',
                'ditolak_ketua': 'bg-red-100 text-red-800'
            };
            
            const statusLabels = {
                'draft': 'Draft',
                'menunggu_konfirmasi_bendahara': 'Menunggu Bendahara',
                'menunggu_approval_ketua': 'Menunggu Ketua',
                'menunggu_pencairan': 'Menunggu Pencairan',
                'dicairkan': 'Dicairkan',
                'ditolak_bendahara': 'Ditolak Bendahara',
                'ditolak_ketua': 'Ditolak Ketua'
            };

            // Jenis badges
            const jenisBadgeClasses = {
                'program_kerja': 'bg-indigo-100 text-indigo-800',
                'pengajuan_budget': 'bg-cyan-100 text-cyan-800'
            };
            
            const jenisBadgeClass = {
                'Kesekretariatan': 'bg-blue-100 text-blue-800',
                'Perjalanan Dinas': 'bg-purple-100 text-purple-800',
                'Aksi': 'bg-green-100 text-green-800',
                'Dana Sosial': 'bg-pink-100 text-pink-800',
                'Dansos Duka': 'bg-pink-100 text-pink-800',
                'Dansos Banjir': 'bg-pink-100 text-pink-800',
                'Dansos Ekternal': 'bg-pink-100 text-pink-800',
                'Pendidikan': 'bg-yellow-100 text-yellow-800',
                'Rapat': 'bg-gray-100 text-gray-800',
                'Rapat GM': 'bg-gray-100 text-gray-800',
                'COS DPP': 'bg-indigo-100 text-indigo-800',
                'Iuaran FKJ': 'bg-orange-100 text-orange-800',
                'Iuran GM': 'bg-orange-100 text-orange-800'
            };
            
            let html = `
                <!-- Info Utama -->
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900">${pb.nama}</h3>
                            <p class="text-sm text-gray-600 mt-1">${pb.bidang.nama}</p>
                            ${pb.program_kerja ? `
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${jenisBadgeClasses[pb.jenis] || 'bg-gray-100 text-gray-800'}">
                                        ${pb.jenis_label} - ${pb.program_kerja.nama}
                                    </span>
                                </div>
                            ` : ''}
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusBadgeClasses[pb.status] || 'bg-gray-100 text-gray-800'}">
                            ${statusLabels[pb.status] || pb.status}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Anggaran</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">Rp ${Number(pb.anggaran).toLocaleString('id-ID')}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Tahun</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">${pb.tahun}</p>
                        </div>
                    </div>

                    ${pb.jenis_pengeluaran ? `
                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Jenis Pengeluaran</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${jenisBadgeClass[pb.jenis_pengeluaran] || 'bg-gray-100 text-gray-800'}">
                            ${pb.jenis_pengeluaran}
                        </span>
                    </div>
                    ` : ''}
                </div>

                <!-- ✅ DETAIL AKSI -->
                ${pb.jenis_pengeluaran === 'Aksi' && (pb.no_surat || pb.jumlah_anggota || pb.nama_aksi) ? `
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <div class="flex items-center space-x-2 mb-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <h4 class="text-sm font-bold text-green-900">Detail Aksi</h4>
                    </div>
                    <div class="space-y-2 text-sm">
                        ${pb.no_surat ? `<p class="text-green-800"><strong>No Surat:</strong> ${pb.no_surat}</p>` : ''}
                        ${pb.jumlah_anggota ? `<p class="text-green-800"><strong>Jumlah Anggota:</strong> ${pb.jumlah_anggota} orang</p>` : ''}
                        ${pb.nama_aksi ? `<p class="text-green-800"><strong>Nama Aksi:</strong> ${pb.nama_aksi}</p>` : ''}
                        ${pb.tempat_aksi ? `<p class="text-green-800"><strong>Tempat:</strong> ${pb.tempat_aksi}</p>` : ''}
                        ${pb.jam_aksi ? `<p class="text-green-800"><strong>Jam:</strong> ${pb.jam_aksi}</p>` : ''}
                    </div>
                </div>
                ` : ''}

                <!-- ✅ LAMPIRAN PDF -->
                ${pb.lampiran_url ? `
                <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-100 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-blue-900">Lampiran PDF</p>
                                <p class="text-xs text-blue-700">${pb.lampiran_filename || 'dokumen.pdf'}</p>
                            </div>
                        </div>
                        <a href="${pb.lampiran_url}" target="_blank" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>Lihat</span>
                        </a>
                    </div>
                </div>
                ` : ''}

                <!-- Tanggal & Pengajuan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Tanggal Pengajuan</p>
                        <p class="text-sm text-blue-900">${pb.tanggal_formatted || '-'}</p>
                    </div>
                    ${pb.submitted_at_formatted ? `
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Diajukan Pada</p>
                        <p class="text-sm text-green-900">${pb.submitted_at_formatted}</p>
                        ${pb.submitted_by_name ? `<p class="text-xs text-green-700 mt-1">oleh ${pb.submitted_by_name}</p>` : ''}
                    </div>
                    ` : ''}
                </div>

                <!-- Review Bendahara -->
                ${pb.reviewed_at_bendahara_formatted ? `
                <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                    <div class="flex items-center space-x-2 mb-3">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="text-sm font-bold text-yellow-900">Review Bendahara</h4>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p class="text-yellow-800"><strong>Reviewer:</strong> ${pb.reviewed_by_bendahara_name || '-'}</p>
                        <p class="text-yellow-800"><strong>Waktu:</strong> ${pb.reviewed_at_bendahara_formatted}</p>
                        ${pb.catatan_bendahara ? `<p class="text-yellow-800"><strong>Catatan:</strong> ${pb.catatan_bendahara}</p>` : ''}
                    </div>
                </div>
                ` : ''}

                <!-- Review Ketua (if exists) -->
                ${pb.reviewed_at_ketua_formatted ? `
                <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                    <div class="flex items-center space-x-2 mb-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="text-sm font-bold text-blue-900">Review Ketua</h4>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p class="text-blue-800"><strong>Reviewer:</strong> ${pb.reviewed_by_ketua_name || '-'}</p>
                        <p class="text-blue-800"><strong>Waktu:</strong> ${pb.reviewed_at_ketua_formatted}</p>
                        ${pb.catatan_ketua ? `<p class="text-blue-800"><strong>Catatan:</strong> ${pb.catatan_ketua}</p>` : ''}
                    </div>
                </div>
                ` : ''}

                <!-- Pencairan (if exists) -->
                ${pb.pencairan ? `
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <div class="flex items-center space-x-2 mb-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="text-sm font-bold text-green-900">Informasi Pencairan</h4>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p class="text-green-800"><strong>Jumlah Dicairkan:</strong> Rp ${Number(pb.pencairan.jumlah_dicairkan).toLocaleString('id-ID')}</p>
                        <p class="text-green-800"><strong>Tanggal:</strong> ${pb.pencairan.tanggal_pencairan_formatted}</p>
                        <p class="text-green-800"><strong>Metode:</strong> ${pb.pencairan.metode_pencairan_label}</p>
                        ${pb.pencairan.nomor_referensi ? `<p class="text-green-800"><strong>No. Referensi:</strong> ${pb.pencairan.nomor_referensi}</p>` : ''}
                        <p class="text-green-800"><strong>Dicairkan oleh:</strong> ${pb.pencairan.dicairkan_oleh_name || '-'}</p>
                        ${pb.pencairan.catatan ? `<p class="text-green-800"><strong>Catatan:</strong> ${pb.pencairan.catatan}</p>` : ''}
                    </div>
                </div>
                ` : ''}

                <!-- Timestamps -->
                <div class="bg-gray-100 rounded-lg p-4 text-xs text-gray-600 space-y-1">
                    <p><strong>Dibuat:</strong> ${pb.created_at_formatted}</p>
                    <p><strong>Terakhir diupdate:</strong> ${pb.updated_at_formatted}</p>
                </div>
            `;
            
            content.innerHTML = html;
        } else {
            throw new Error(data.message || 'Gagal memuat data');
        }
    } catch (error) {
        console.error('Error:', error);
        content.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-4 text-gray-600 font-semibold">Gagal memuat detail</p>
                <p class="text-sm text-gray-500 mt-2">${error.message}</p>
            </div>
        `;
    }
}

function closeDetailModalKetua() {
    const modal = document.getElementById('detailModalKetua');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }, 250);
}

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModalKetua();
    }
});
</script>