<!-- DETAIL Modal Bendahara -->
<div id="detailModalBendahara" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Detail Pengajuan Hutang</h2>
            <button onclick="closeDetailModalBendahara()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            
            <!-- Basic Info -->
            <div>
                <h3 class="text-2xl font-bold text-gray-900" id="detailBendaharaNama">-</h3>
                <p class="text-sm text-gray-500 mt-1">Pengajuan Hutang</p>
            </div>

            <!-- Data Peminjam -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Peminjam</p>
                    <p class="text-lg font-bold text-gray-900" id="detailBendaharaPeminjam">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Bidang</p>
                    <p class="text-lg font-bold text-gray-900" id="detailBendaharaBidang">-</p>
                </div>
            </div>

            <!-- Jumlah -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Jumlah Hutang</p>
                <p class="text-2xl font-bold text-blue-900" id="detailBendaharaJumlah">Rp 0</p>
            </div>

            <!-- Keperluan -->
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Keperluan</p>
                <p class="text-sm text-gray-900 whitespace-pre-wrap" id="detailBendaharaKeperluan">-</p>
            </div>

            <!-- Tanggal & Tahun -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Peminjaman</p>
                    <p class="text-sm font-bold text-gray-900" id="detailBendaharaTanggal">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tahun</p>
                    <p class="text-sm font-bold text-gray-900" id="detailBendaharaTahun">-</p>
                </div>
            </div>

            <!-- Submission Info -->
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-bold text-gray-900 mb-3">Informasi Pengajuan</h4>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-blue-900">Diajukan oleh</p>
                            <p class="text-sm text-blue-800" id="detailBendaharaSubmittedBy">-</p>
                            <p class="text-xs text-blue-600 mt-1" id="detailBendaharaSubmittedAt">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex justify-end pt-4">
                <button onclick="closeDetailModalBendahara()"
                    class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#detailModalBendahara.active {
    opacity: 1;
}

#detailModalBendahara.active > div {
    transform: scale(1);
}
</style>

<script>
async function openDetailModalBendahara(id) {
    try {
        const response = await fetch(`/bendahara/hutang/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const ph = data.data;
            
            document.getElementById('detailBendaharaNama').textContent = ph.nama;
            document.getElementById('detailBendaharaPeminjam').textContent = ph.user.name;
            document.getElementById('detailBendaharaBidang').textContent = ph.bidang.nama;
            document.getElementById('detailBendaharaJumlah').textContent = 'Rp ' + formatNumber(ph.jumlah);
            document.getElementById('detailBendaharaKeperluan').textContent = ph.keperluan;
            document.getElementById('detailBendaharaTanggal').textContent = ph.tanggal_formatted || '-';
            document.getElementById('detailBendaharaTahun').textContent = ph.tahun;
            document.getElementById('detailBendaharaSubmittedBy').textContent = ph.submitted_by_name || '-';
            document.getElementById('detailBendaharaSubmittedAt').textContent = ph.submitted_at_formatted || '-';
            
            const modal = document.getElementById('detailModalBendahara');
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

function closeDetailModalBendahara() {
    const modal = document.getElementById('detailModalBendahara');
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
        closeDetailModalBendahara();
    }
});
</script>