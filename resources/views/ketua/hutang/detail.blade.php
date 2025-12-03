<!-- DETAIL Modal Ketua -->
<div id="detailModalKetua" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Detail Pengajuan Hutang</h2>
            <button onclick="closeDetailModalKetua()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            
            <!-- Basic Info -->
            <div>
                <h3 class="text-2xl font-bold text-gray-900" id="detailKetuaNama">-</h3>
                <p class="text-sm text-gray-500 mt-1">Pengajuan Hutang</p>
            </div>

            <!-- Data Peminjam -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Peminjam</p>
                    <p class="text-lg font-bold text-gray-900" id="detailKetuaPeminjam">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Bidang</p>
                    <p class="text-lg font-bold text-gray-900" id="detailKetuaBidang">-</p>
                </div>
            </div>

            <!-- Jumlah -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider mb-1">Jumlah Hutang</p>
                <p class="text-2xl font-bold text-purple-900" id="detailKetuaJumlah">Rp 0</p>
            </div>

            <!-- Keperluan -->
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Keperluan</p>
                <p class="text-sm text-gray-900 whitespace-pre-wrap" id="detailKetuaKeperluan">-</p>
            </div>

            <!-- Tanggal & Tahun -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Peminjaman</p>
                    <p class="text-sm font-bold text-gray-900" id="detailKetuaTanggal">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tahun</p>
                    <p class="text-sm font-bold text-gray-900" id="detailKetuaTahun">-</p>
                </div>
            </div>

            <!-- Timeline Approval -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-bold text-gray-900 mb-4">Timeline Approval</h4>
                
                <!-- Submission -->
                <div class="mb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Diajukan</p>
                            <p class="text-xs text-gray-600">oleh <span id="detailKetuaSubmittedBy" class="font-semibold">-</span></p>
                            <p class="text-xs text-gray-500" id="detailKetuaSubmittedAt">-</p>
                        </div>
                    </div>
                </div>

                <!-- Bendahara Review -->
                <div class="mb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Dikonfirmasi Bendahara</p>
                            <p class="text-xs text-gray-600">oleh <span id="detailKetuaReviewedByBendahara" class="font-semibold">-</span></p>
                            <p class="text-xs text-gray-500" id="detailKetuaReviewedAtBendahara">-</p>
                            <div id="detailKetuaCatatanBendaharaBox" class="hidden mt-2 bg-green-50 border border-green-200 rounded p-2">
                                <p class="text-xs text-green-800" id="detailKetuaCatatanBendahara">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="border-t border-gray-200 pt-4">
                <div class="grid grid-cols-2 gap-4 text-xs text-gray-600">
                    <div>
                        <span class="font-semibold">Dibuat:</span>
                        <span id="detailKetuaCreatedAt">-</span>
                    </div>
                    <div>
                        <span class="font-semibold">Terakhir Diupdate:</span>
                        <span id="detailKetuaUpdatedAt">-</span>
                    </div>
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex justify-end pt-4">
                <button onclick="closeDetailModalKetua()"
                    class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Tutup
                </button>
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
    try {
        const response = await fetch(`/ketua/hutang/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const ph = data.data;
            
            document.getElementById('detailKetuaNama').textContent = ph.nama;
            document.getElementById('detailKetuaPeminjam').textContent = ph.user.name;
            document.getElementById('detailKetuaBidang').textContent = ph.bidang.nama;
            document.getElementById('detailKetuaJumlah').textContent = 'Rp ' + formatNumber(ph.jumlah);
            document.getElementById('detailKetuaKeperluan').textContent = ph.keperluan;
            document.getElementById('detailKetuaTanggal').textContent = ph.tanggal_formatted || '-';
            document.getElementById('detailKetuaTahun').textContent = ph.tahun;
            
            // Submission
            document.getElementById('detailKetuaSubmittedBy').textContent = ph.submitted_by_name || '-';
            document.getElementById('detailKetuaSubmittedAt').textContent = ph.submitted_at_formatted || '-';
            
            // Bendahara
            document.getElementById('detailKetuaReviewedByBendahara').textContent = ph.reviewed_by_bendahara_name || '-';
            document.getElementById('detailKetuaReviewedAtBendahara').textContent = ph.reviewed_at_bendahara_formatted || '-';
            
            if (ph.catatan_bendahara) {
                document.getElementById('detailKetuaCatatanBendaharaBox').classList.remove('hidden');
                document.getElementById('detailKetuaCatatanBendahara').textContent = ph.catatan_bendahara;
            } else {
                document.getElementById('detailKetuaCatatanBendaharaBox').classList.add('hidden');
            }
            
            // Timestamps
            document.getElementById('detailKetuaCreatedAt').textContent = ph.submitted_at_formatted || '-';
            document.getElementById('detailKetuaUpdatedAt').textContent = ph.reviewed_at_bendahara_formatted || '-';
            
            const modal = document.getElementById('detailModalKetua');
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
        Swal.fire('Error!', 'Gagal memuat detail pengajuan hutang', 'error');
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

function formatNumber(num) {
    return parseFloat(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModalKetua();
    }
});
</script>