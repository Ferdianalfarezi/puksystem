<!-- DETAIL History Modal -->
<div id="detailHistoryModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="bg-green-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Detail Hutang Lunas</h2>
            </div>
            <button onclick="closeDetailHistoryModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            
            <!-- Status Badge -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900" id="detailHistoryNama">-</h3>
                    <p class="text-sm text-gray-500 mt-1">Hutang Lunas</p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-800">
                    ✓ LUNAS
                </span>
            </div>

            <!-- Data Peminjam -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Peminjam</p>
                    <p class="text-lg font-bold text-gray-900" id="detailHistoryPeminjam">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Bidang</p>
                    <p class="text-lg font-bold text-gray-900" id="detailHistoryBidang">-</p>
                </div>
            </div>

            <!-- Jumlah -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-1">Jumlah Hutang (Lunas)</p>
                <p class="text-2xl font-bold text-green-900" id="detailHistoryJumlah">Rp 0</p>
            </div>

            <!-- Keperluan -->
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Keperluan</p>
                <p class="text-sm text-gray-900 whitespace-pre-wrap" id="detailHistoryKeperluan">-</p>
            </div>

            <!-- Tanggal & Tahun -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Peminjaman</p>
                    <p class="text-sm font-bold text-gray-900" id="detailHistoryTanggal">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tahun</p>
                    <p class="text-sm font-bold text-gray-900" id="detailHistoryTahun">-</p>
                </div>
            </div>

            <!-- Riwayat Pembayaran -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-bold text-gray-900 mb-4">Riwayat Pembayaran</h4>
                <div id="detailHistoryPembayaranList" class="space-y-3">
                    <!-- Will be populated by JS -->
                </div>
            </div>

            <!-- Summary Pembayaran -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Total Cicilan</p>
                        <p class="text-lg font-bold text-blue-900" id="detailHistoryTotalCicilan">0</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Total Terbayar</p>
                        <p class="text-lg font-bold text-blue-900" id="detailHistoryTotalTerbayar">Rp 0</p>
                    </div>
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex justify-end pt-4">
                <button onclick="closeDetailHistoryModal()"
                    class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#detailHistoryModal.active {
    opacity: 1;
}

#detailHistoryModal.active > div {
    transform: scale(1);
}
</style>

<script>
async function openDetailHistoryModal(id) {
    try {
        const response = await fetch(`/pengajuan-hutang/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const ph = data.data;
            
            document.getElementById('detailHistoryNama').textContent = ph.nama;
            document.getElementById('detailHistoryPeminjam').textContent = ph.user.name;
            document.getElementById('detailHistoryBidang').textContent = ph.bidang.nama;
            document.getElementById('detailHistoryJumlah').textContent = 'Rp ' + formatNumberHistory(ph.jumlah);
            document.getElementById('detailHistoryKeperluan').textContent = ph.keperluan;
            document.getElementById('detailHistoryTanggal').textContent = ph.tanggal_formatted;
            document.getElementById('detailHistoryTahun').textContent = ph.tahun;
            
            // Riwayat Pembayaran
            const pembayaranList = document.getElementById('detailHistoryPembayaranList');
            pembayaranList.innerHTML = '';
            
            if (ph.pembayaran && ph.pembayaran.length > 0) {
                ph.pembayaran.forEach((p, index) => {
                    const pembayaranItem = `
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">Pembayaran #${index + 1}</span>
                                <span class="text-lg font-bold text-green-600">Rp ${formatNumberHistory(p.jumlah_bayar)}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                                <div>
                                    <span class="font-semibold">Tanggal:</span> ${p.tanggal_bayar}
                                </div>
                                <div>
                                    <span class="font-semibold">Metode:</span> ${p.metode_pembayaran}
                                </div>
                                <div>
                                    <span class="font-semibold">Dibayar oleh:</span> ${p.dibayar_oleh_name}
                                </div>
                                ${p.nomor_referensi ? `<div><span class="font-semibold">Ref:</span> ${p.nomor_referensi}</div>` : ''}
                            </div>
                            ${p.catatan ? `<p class="text-xs text-gray-600 mt-2 italic">${p.catatan}</p>` : ''}
                        </div>
                    `;
                    pembayaranList.innerHTML += pembayaranItem;
                });
                
                // Summary
                document.getElementById('detailHistoryTotalCicilan').textContent = ph.pembayaran.length + 'x';
                document.getElementById('detailHistoryTotalTerbayar').textContent = 'Rp ' + formatNumberHistory(ph.total_terbayar);
            }
            
            const modal = document.getElementById('detailHistoryModal');
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
        Swal.fire('Error!', 'Gagal memuat detail hutang', 'error');
    }
}

function closeDetailHistoryModal() {
    const modal = document.getElementById('detailHistoryModal');
    modal.classList.remove('active');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }, 250);
}

function formatNumberHistory(num) {
    return parseFloat(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailHistoryModal();
    }
});
</script>