<!-- HISTORY DETAIL Modal -->
<div id="historyDetailModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm opacity-0 transition-opacity duration-250">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto transform transition-all duration-250">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-xl">
            <h2 class="text-lg font-bold text-gray-900">Detail History Pengajuan Budget</h2>
            <button onclick="closeHistoryDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5" id="historyDetailModalBody">
            <!-- Loading State -->
            <div id="historyDetailLoading" class="animate-pulse space-y-3">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
            </div>

            <!-- Content Container (akan di-populate via JS) -->
            <div id="historyDetailContent" class="hidden space-y-4"></div>
        </div>
    </div>
</div>

<script>
// Global variable untuk menyimpan ID pengajuan yang sedang ditampilkan
let currentHistoryDetailPengajuanId = null;

async function openHistoryDetailModal(id) {
    currentHistoryDetailPengajuanId = id;
    const modal = document.getElementById('historyDetailModal');
    
    // Show modal
    document.body.style.overflow = 'hidden';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Reset content
    document.getElementById('historyDetailLoading').classList.remove('hidden');
    document.getElementById('historyDetailContent').classList.add('hidden');
    
    // Trigger fade in animation
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
    });

    try {
        const response = await fetch(`/history/pengajuan-budget/${id}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response bukan JSON. Kemungkinan ada error di server.');
        }

        const data = await response.json();
        
        if (data.success) {
            const pb = data.data;
            renderHistoryDetailContent(pb);
            // Hide loading, show content
            document.getElementById('historyDetailLoading').classList.add('hidden');
            document.getElementById('historyDetailContent').classList.remove('hidden');
        } else {
            Swal.fire('Error!', data.message || 'Gagal memuat data history', 'error');
            closeHistoryDetailModal();
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', error.message || 'Terjadi kesalahan saat memuat data', 'error');
        closeHistoryDetailModal();
    }
}

function renderHistoryDetailContent(pb) {
    // Status Configuration
    const statusConfig = {
        'draft': { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Draft' },
        'menunggu_konfirmasi_bendahara': { bg: 'bg-yellow-100', text: 'text-yellow-800', label: 'Menunggu Bendahara' },
        'menunggu_approval_ketua': { bg: 'bg-blue-100', text: 'text-blue-800', label: 'Menunggu Ketua' },
        'menunggu_pencairan': { bg: 'bg-purple-100', text: 'text-purple-800', label: 'Menunggu Pencairan' },
        'dicairkan': { bg: 'bg-green-100', text: 'text-green-800', label: 'Dicairkan' },
        'ditolak_bendahara': { bg: 'bg-red-100', text: 'text-red-800', label: 'Ditolak Bendahara' },
        'ditolak_ketua': { bg: 'bg-red-100', text: 'text-red-800', label: 'Ditolak Ketua' },
    };
    const config = statusConfig[pb.status] || { bg: 'bg-gray-100', text: 'text-gray-800', label: pb.status };

    // Format currency
    const formattedAnggaran = new Intl.NumberFormat('id-ID').format(pb.anggaran);

    let html = `
        <!-- Header Info Card -->
        <div class="bg-gradient-to-r from-black to-gray-800 rounded-lg p-4 text-white">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="text-xl font-bold mb-1">${pb.nama}</h3>
                    <div class="flex items-center gap-3 text-sm opacity-90">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            ${pb.bidang.nama}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            ${pb.tahun}
                        </span>
                    </div>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${config.bg} ${config.text}">
                    ${config.label}
                </span>
            </div>
            <div class="flex items-baseline gap-2 border-t border-white border-opacity-20 pt-3">
                <span class="text-3xl font-bold">Rp ${formattedAnggaran}</span>
                <span class="text-sm opacity-75">Anggaran</span>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-600 mb-1">Tanggal Pengajuan</p>
                <p class="text-sm font-semibold text-gray-900">${pb.tanggal_formatted || '-'}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-600 mb-1">Jenis Pengeluaran</p>
                <p class="text-sm font-semibold text-gray-900">${pb.jenis_pengeluaran || '-'}</p>
            </div>
        </div>
    `;

    // Pencairan Info (if dicairkan)
    if (pb.status === 'dicairkan' && pb.pencairan) {
        const pencairan = pb.pencairan;
        const formattedPencairan = new Intl.NumberFormat('id-ID').format(pencairan.jumlah_dicairkan);
        
        html += `
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="text-sm font-bold text-green-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informasi Pencairan
                </h4>
                <div class="space-y-2">
                    <div class="flex justify-between items-baseline">
                        <span class="text-xs text-green-700">Jumlah Dicairkan</span>
                        <span class="text-lg font-bold text-green-900">Rp ${formattedPencairan}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-green-600">Tanggal:</span>
                            <span class="font-medium text-green-900 ml-1">${pencairan.tanggal_pencairan_formatted || '-'}</span>
                        </div>
                        <div>
                            <span class="text-green-600">Metode:</span>
                            <span class="font-medium text-green-900 ml-1">${pencairan.metode_pencairan_label || '-'}</span>
                        </div>
                    </div>
                    ${pencairan.nomor_referensi ? `
                        <div class="text-xs">
                            <span class="text-green-600">No. Referensi:</span>
                            <span class="font-medium text-green-900 ml-1">${pencairan.nomor_referensi}</span>
                        </div>
                    ` : ''}
                    ${pencairan.dicairkan_oleh_name ? `
                        <div class="text-xs">
                            <span class="text-green-600">Dicairkan oleh:</span>
                            <span class="font-medium text-green-900 ml-1">${pencairan.dicairkan_oleh_name}</span>
                        </div>
                    ` : ''}
                    ${pencairan.catatan ? `
                        <div class="mt-2 pt-2 border-t border-green-200">
                            <p class="text-xs text-green-700"><strong>Catatan:</strong> ${pencairan.catatan}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    // Timeline Aktivitas (if submitted)
    if (pb.submitted_at_formatted) {
        html += `
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Timeline Aktivitas
                </h4>
                ${renderHistoryTimeline(pb)}
            </div>
        `;
    }

    // Metadata
    html += `
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Metadata Pengajuan
            </h4>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <p class="text-gray-600 mb-1">Dibuat</p>
                    <p class="font-semibold text-gray-900">${pb.created_at_formatted || '-'}</p>
                </div>
                ${pb.submitted_at_formatted ? `
                <div>
                    <p class="text-gray-600 mb-1">Diajukan</p>
                    <p class="font-semibold text-gray-900">${pb.submitted_at_formatted}</p>
                    ${pb.submitted_by_name ? `<p class="text-gray-500 mt-0.5">oleh ${pb.submitted_by_name}</p>` : ''}
                </div>
                ` : ''}
                <div>
                    <p class="text-gray-600 mb-1">Terakhir Update</p>
                    <p class="font-semibold text-gray-900">${pb.updated_at_formatted || '-'}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-1">Total Aktivitas</p>
                    <p class="font-semibold text-gray-900">${pb.histories_count || 0} aktivitas</p>
                </div>
            </div>
        </div>
    `;

    document.getElementById('historyDetailContent').innerHTML = html;
}

function renderHistoryTimeline(pb) {
    if (!pb.histories || pb.histories.length === 0) {
        return `
            <div class="text-center py-4">
                <svg class="w-8 h-8 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-2 text-xs text-gray-600">Belum ada aktivitas</p>
            </div>
        `;
    }

    let html = '<div class="space-y-3">';

    pb.histories.forEach((history, index) => {
        const isSuccess = ['menunggu_konfirmasi_bendahara', 'menunggu_approval_ketua', 'menunggu_pencairan', 'dicairkan'].includes(history.status_ke);
        const isRejected = ['ditolak_bendahara', 'ditolak_ketua'].includes(history.status_ke);
        const bgColor = isSuccess ? 'bg-green-100' : (isRejected ? 'bg-red-100' : 'bg-gray-100');
        const iconColor = isSuccess ? 'text-green-600' : (isRejected ? 'text-red-600' : 'text-gray-600');

        const icon = isSuccess 
            ? '<svg class="w-4 h-4 ' + iconColor + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
            : (isRejected 
                ? '<svg class="w-4 h-4 ' + iconColor + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
                : '<svg class="w-4 h-4 ' + iconColor + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>');

        const isLast = index === pb.histories.length - 1;

        html += `
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full ${bgColor} flex items-center justify-center">
                        ${icon}
                    </div>
                </div>
                <div class="flex-1 ${!isLast ? 'pb-3 border-b border-gray-200' : ''}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">
                                ${history.status_ke_label}
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                ${history.dilakukan_pada_formatted || '-'}
                            </p>
                            ${history.dilakukan_oleh_name ? `<p class="text-xs text-gray-500 mt-0.5">oleh ${history.dilakukan_oleh_name}</p>` : ''}
                        </div>
                    </div>
                    ${history.catatan ? `
                        <div class="mt-2 bg-gray-50 rounded-lg p-2">
                            <p class="text-xs text-gray-700"><strong>Catatan:</strong> ${history.catatan}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    });

    html += '</div>';
    return html;
}

function closeHistoryDetailModal() {
    const modal = document.getElementById('historyDetailModal');
    
    // Trigger fade out animation
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    
    // Wait for animation to complete before hiding
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        currentHistoryDetailPengajuanId = null;
    }, 250);
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && currentHistoryDetailPengajuanId) {
        closeHistoryDetailModal();
    }
});
</script>