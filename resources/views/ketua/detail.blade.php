<!-- Detail Modal Ketua -->
<div id="detailModalKetua" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-gradient-to-r from-black to-gray-800 z-10 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Detail Program Kerja</h2>
                    <p class="text-sm text-gray-300">Review informasi untuk approval</p>
                </div>
            </div>
            <button onclick="closeDetailModalKetua()" class="text-white hover:text-gray-300 transition">
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
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <p class="text-base font-bold text-gray-900" id="detailKetuaNamaProgram"></p>
                        </div>
                    </div>
                    <div class="flex border-b border-gray-200 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Bidang</p>
                        </div>
                        <div class="w-2/3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-black text-white" id="detailKetuaBidang"></span>
                        </div>
                    </div>
                    <div class="flex border-b border-gray-200 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Anggaran</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-xl font-bold text-green-600" id="detailKetuaAnggaran"></p>
                        </div>
                    </div>
                    <div class="flex border-b border-gray-200 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Tahun</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-gray-900 font-medium" id="detailKetuaTahun"></p>
                        </div>
                    </div>
                    <div class="flex border-b border-gray-200 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Tanggal Pelaksanaan</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-gray-900 font-medium" id="detailKetuaTanggal"></p>
                        </div>
                    </div>
                    <div class="flex" id="detailKetuaJenisPengeluaranWrapper">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-gray-600">Jenis Pengeluaran</p>
                        </div>
                        <div class="w-2/3">
                            <span id="detailKetuaJenisPengeluaran" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Review Bendahara -->
            <div class="bg-white-50 rounded-xl p-6 border border-dark-200">
                <h4 class="text-lg font-bold text-dark-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Review Bendahara
                </h4>
                <div class="space-y-3">
                    <div class="flex border-b border-yellow-100 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-dark-700">Status</p>
                        </div>
                        <div class="w-2/3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Disetujui
                            </span>
                        </div>
                    </div>
                    <div class="flex border-b border-dark-100 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-dark-700">Direview Oleh</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-dark-900 font-medium" id="detailKetuaReviewedBy"></p>
                        </div>
                    </div>
                    <div class="flex border-b border-dark-100 pb-3">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-dark-700">Tanggal Review</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-dark-900 font-medium" id="detailKetuaReviewedAt"></p>
                        </div>
                    </div>
                    <div class="flex" id="detailKetuaCatatanBendaharaWrapper" style="display: none;">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-dark-700">Catatan</p>
                        </div>
                        <div class="w-2/3">
                            <div class="bg-dark-100 border border-dark-300 rounded-lg p-3">
                                <p class="text-sm text-dark-900" id="detailKetuaCatatanBendahara"></p>
                            </div>
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
                            <p class="text-base text-blue-900 font-medium" id="detailKetuaSubmittedBy"></p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3">
                            <p class="text-sm font-semibold text-blue-700">Tanggal Pengajuan</p>
                        </div>
                        <div class="w-2/3">
                            <p class="text-base text-blue-900 font-medium" id="detailKetuaSubmittedAt"></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl flex items-center justify-end space-x-3">
            <button 
                type="button" 
                onclick="closeDetailModalKetua()"
                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
            >
                Tutup
            </button>
            <button 
                type="button"
                onclick="openApproveModalFromDetailKetua()"
                class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Setuju</span>
            </button>
            <button 
                type="button"
                onclick="openRejectModalFromDetailKetua()"
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
#detailModalKetua.active {
    opacity: 1;
}

#detailModalKetua.active > div {
    transform: scale(1);
}
</style>

<script>
let currentDetailKetuaProgramId = null;
let currentDetailKetuaProgramName = '';

function openDetailModalKetua(id) {
    currentDetailKetuaProgramId = id;
    
    // Fetch data program kerja
    fetch(`/ketua/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const pk = data.data;
            currentDetailKetuaProgramName = pk.nama;
            
            // Populate modal - Informasi Program
            document.getElementById('detailKetuaNamaProgram').textContent = pk.nama;
            document.getElementById('detailKetuaBidang').textContent = pk.bidang.nama;
            document.getElementById('detailKetuaAnggaran').textContent = 'Rp ' + formatRupiahKetua(pk.anggaran);
            document.getElementById('detailKetuaTahun').textContent = pk.tahun;
            document.getElementById('detailKetuaTanggal').textContent = pk.tanggal ? formatDateKetua(pk.tanggal) : '-';
            
            // Jenis Pengeluaran
            if (pk.jenis_pengeluaran) {
                const jenisBadgeClass = getJenisBadgeClass(pk.jenis_pengeluaran);
                document.getElementById('detailKetuaJenisPengeluaran').textContent = pk.jenis_pengeluaran;
                document.getElementById('detailKetuaJenisPengeluaran').className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${jenisBadgeClass}`;
                document.getElementById('detailKetuaJenisPengeluaranWrapper').style.display = 'flex';
            } else {
                document.getElementById('detailKetuaJenisPengeluaranWrapper').style.display = 'none';
            }
            
            // Review Bendahara
            document.getElementById('detailKetuaReviewedBy').textContent = pk.reviewed_by_bendahara_name || '-';
            document.getElementById('detailKetuaReviewedAt').textContent = pk.reviewed_at_bendahara ? formatDateTimeKetua(pk.reviewed_at_bendahara) : '-';
            
            // Catatan Bendahara
            if (pk.catatan_bendahara) {
                document.getElementById('detailKetuaCatatanBendahara').textContent = pk.catatan_bendahara;
                document.getElementById('detailKetuaCatatanBendaharaWrapper').style.display = 'flex';
            } else {
                document.getElementById('detailKetuaCatatanBendaharaWrapper').style.display = 'none';
            }
            
            // Pengajuan
            document.getElementById('detailKetuaSubmittedBy').textContent = pk.submitted_by_name || '-';
            document.getElementById('detailKetuaSubmittedAt').textContent = pk.submitted_at ? formatDateTimeKetua(pk.submitted_at) : '-';
            
            // Show modal
            const modal = document.getElementById('detailModalKetua');
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

function closeDetailModalKetua() {
    const modal = document.getElementById('detailModalKetua');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        currentDetailKetuaProgramId = null;
        currentDetailKetuaProgramName = '';
    }, 300);
}

function openApproveModalFromDetailKetua() {
    closeDetailModalKetua();
    setTimeout(() => {
        openApproveModal(currentDetailKetuaProgramId, currentDetailKetuaProgramName);
    }, 300);
}

function openRejectModalFromDetailKetua() {
    closeDetailModalKetua();
    setTimeout(() => {
        openRejectModal(currentDetailKetuaProgramId, currentDetailKetuaProgramName);
    }, 300);
}

function getJenisBadgeClass(jenis) {
    const badges = {
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
        'Iuran GM': 'bg-orange-100 text-orange-800',
    };
    return badges[jenis] || 'bg-gray-100 text-gray-800';
}

function formatRupiahKetua(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatDateKetua(dateString) {
    const date = new Date(dateString);
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

function formatDateTimeKetua(dateString) {
    const date = new Date(dateString);
    const dateOptions = { day: 'numeric', month: 'short', year: 'numeric' };
    const timeOptions = { hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('id-ID', dateOptions) + ', ' + date.toLocaleTimeString('id-ID', timeOptions) + ' WIB';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModalKetua();
    }
});
</script>