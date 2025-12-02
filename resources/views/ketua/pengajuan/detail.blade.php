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
        const response = await fetch(`/ketua/pengajuan/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const pb = data.data;
            
            const jenisBadgeClasses = {
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
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <h3 class="text-lg font-bold text-gray-900">${pb.nama}</h3>
                    <p class="text-sm text-gray-600">${pb.bidang.nama}</p>
                    
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
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${jenisBadgeClasses[pb.jenis_pengeluaran] || 'bg-gray-100 text-gray-800'}">
                            ${pb.jenis_pengeluaran}
                        </span>
                    </div>
                    ` : ''}
                </div>

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

                <div class="bg-gray-100 rounded-lg p-4 text-xs text-gray-600">
                    <p><strong>Dibuat:</strong> ${pb.created_at_formatted}</p>
                </div>
            `;
            
            content.innerHTML = html;
        }
    } catch (error) {
        console.error('Error:', error);
        content.innerHTML = `<div class="text-center py-12"><p class="text-gray-600">Gagal memuat detail</p></div>`;
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
</script>