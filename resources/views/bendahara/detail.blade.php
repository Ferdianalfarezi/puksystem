<!-- Detail Modal Bendahara -->
<div id="detailModalBendahara" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-gradient-to-r from-yellow-600 to-yellow-700 z-10 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Detail Program Kerja</h2>
                    <p class="text-sm text-yellow-100">Review informasi program kerja</p>
                </div>
            </div>
            <button onclick="closeDetailModalBendahara()" class="text-white hover:text-yellow-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            
            <!-- Card: Informasi Program -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informasi Program
                </h4>
                <div class="space-y-3">
                    <div class="flex border-b border-gray-200 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Nama Program</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base font-bold text-gray-900" id="detailNamaProgram"></p>
                        </div>
                    </div>
                    <div class="flex border-b border-gray-200 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Bidang</p>
                        </div>
                        <div class="w-2/3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-black text-white" id="detailBidang"></span>
                        </div>
                    </div>
                    <div class="flex border-b border-gray-200 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Anggaran</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-xl font-bold text-green-600" id="detailAnggaran"></p>
                        </div>
                    </div>
                    <div class="flex border-b border-gray-200 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Tahun</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-gray-900 font-medium" id="detailTahun"></p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Tanggal</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-gray-900 font-medium" id="detailTanggal"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Pengajuan -->
            <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                <h4 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Informasi Pengajuan
                </h4>
                <div class="space-y-3">
                    <div class="flex border-b border-blue-100 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-blue-700">Diajukan Oleh</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-blue-900 font-medium" id="detailSubmittedBy"></p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-blue-700">Tanggal Pengajuan</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-blue-900 font-medium" id="detailSubmittedAt"></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl flex items-center justify-end space-x-3">
            <button 
                type="button" 
                onclick="closeDetailModalBendahara()"
                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
            >
                Tutup
            </button>
            <button 
                type="button"
                onclick="openApproveModalFromDetail()"
                class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Setuju</span>
            </button>
            <button 
                type="button"
                onclick="openRejectModalFromDetail()"
                class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>Tolak</span>
            </button>
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
let currentDetailProgramId = null;
let currentDetailProgramName = '';

function openDetailModalBendahara(id) {
    currentDetailProgramId = id;
    
    // Fetch data program kerja
    fetch(`/bendahara/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const pk = data.data;
            currentDetailProgramName = pk.nama;
            
            // Populate modal
            document.getElementById('detailNamaProgram').textContent = pk.nama;
            document.getElementById('detailBidang').textContent = pk.bidang.nama;
            document.getElementById('detailAnggaran').textContent = 'Rp ' + formatRupiah(pk.anggaran);
            document.getElementById('detailTahun').textContent = pk.tahun;
            document.getElementById('detailTanggal').textContent = pk.tanggal ? formatDate(pk.tanggal) : '-';
            document.getElementById('detailSubmittedBy').textContent = pk.submitted_by_name || '-';
            document.getElementById('detailSubmittedAt').textContent = pk.submitted_at ? formatDateTime(pk.submitted_at) : '-';
            
            // Show modal
            const modal = document.getElementById('detailModalBendahara');
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            void modal.offsetWidth;
            requestAnimationFrame(() => {
                modal.classList.add('active');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat data program kerja', 'error');
    });
}

function closeDetailModalBendahara() {
    const modal = document.getElementById('detailModalBendahara');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        currentDetailProgramId = null;
        currentDetailProgramName = '';
    }, 300);
}

function openApproveModalFromDetail() {
    closeDetailModalBendahara();
    setTimeout(() => {
        openApproveModal(currentDetailProgramId, currentDetailProgramName);
    }, 300);
}

function openRejectModalFromDetail() {
    closeDetailModalBendahara();
    setTimeout(() => {
        openRejectModal(currentDetailProgramId, currentDetailProgramName);
    }, 300);
}

function formatRupiah(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    const dateOptions = { day: 'numeric', month: 'short', year: 'numeric' };
    const timeOptions = { hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('id-ID', dateOptions) + ', ' + date.toLocaleTimeString('id-ID', timeOptions) + ' WIB';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModalBendahara();
    }
});
</script>