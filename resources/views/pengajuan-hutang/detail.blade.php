<!-- DETAIL Modal -->
<div id="detailModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-0 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Detail Pengajuan Hutang</h2>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
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
                    <h3 class="text-2xl font-bold text-gray-900" id="detailNama">-</h3>
                    <p class="text-sm text-gray-500 mt-1">Pengajuan Hutang</p>
                </div>
                <div id="detailStatusBadge"></div>
            </div>

            <!-- Progress Bar (jika dicairkan) -->
            <div id="detailProgressSection" class="hidden">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Progress Pembayaran</span>
                        <span class="text-sm font-bold text-green-600" id="detailPersenLunas">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="detailProgressBar" class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-gray-600">Terbayar: <span id="detailTotalTerbayar" class="font-semibold">Rp 0</span></span>
                        <span class="text-xs text-gray-600">Sisa: <span id="detailSisaHutang" class="font-semibold text-red-600">Rp 0</span></span>
                    </div>
                </div>
            </div>

            <!-- Data Peminjam -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Peminjam</p>
                    <p class="text-lg font-bold text-gray-900" id="detailPeminjam">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Bidang</p>
                    <p class="text-lg font-bold text-gray-900" id="detailBidang">-</p>
                </div>
            </div>

            <!-- Jumlah & Sisa Hutang -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Jumlah Hutang</p>
                    <p class="text-2xl font-bold text-blue-900" id="detailJumlah">Rp 0</p>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-1">Sisa Hutang</p>
                    <p class="text-2xl font-bold text-red-900" id="detailSisaHutangBesar">Rp 0</p>
                </div>
            </div>

            <!-- Keperluan -->
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Keperluan</p>
                <p class="text-sm text-gray-900 whitespace-pre-wrap" id="detailKeperluan">-</p>
            </div>

            <!-- Tanggal & Tahun -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Peminjaman</p>
                    <p class="text-sm font-bold text-gray-900" id="detailTanggal">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tahun</p>
                    <p class="text-sm font-bold text-gray-900" id="detailTahun">-</p>
                </div>
            </div>

            <!-- Workflow Timeline -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-bold text-gray-900 mb-4">Timeline Approval</h4>
                
                <!-- Submission -->
                <div id="detailSubmissionSection" class="hidden mb-4">
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
                            <p class="text-xs text-gray-600">oleh <span id="detailSubmittedBy" class="font-semibold">-</span></p>
                            <p class="text-xs text-gray-500" id="detailSubmittedAt">-</p>
                        </div>
                    </div>
                </div>

                <!-- Bendahara Review -->
                <div id="detailBendaharaSection" class="hidden mb-4">
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
                            <p class="text-xs text-gray-600">oleh <span id="detailReviewedByBendahara" class="font-semibold">-</span></p>
                            <p class="text-xs text-gray-500" id="detailReviewedAtBendahara">-</p>
                            <div id="detailCatatanBendaharaBox" class="hidden mt-2 bg-green-50 border border-green-200 rounded p-2">
                                <p class="text-xs text-green-800" id="detailCatatanBendahara">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ketua Review -->
                <div id="detailKetuaSection" class="hidden mb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Disetujui Ketua</p>
                            <p class="text-xs text-gray-600">oleh <span id="detailReviewedByKetua" class="font-semibold">-</span></p>
                            <p class="text-xs text-gray-500" id="detailReviewedAtKetua">-</p>
                            <div id="detailCatatanKetuaBox" class="hidden mt-2 bg-purple-50 border border-purple-200 rounded p-2">
                                <p class="text-xs text-purple-800" id="detailCatatanKetua">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pembayaran (jika ada) -->
            <div id="detailPembayaranSection" class="hidden border-t border-gray-200 pt-6">
                <h4 class="text-lg font-bold text-gray-900 mb-4">Riwayat Pembayaran</h4>
                <div id="detailPembayaranList" class="space-y-3">
                    <!-- Will be populated by JS -->
                </div>
            </div>

            <!-- Timestamps -->
            <div class="border-t border-gray-200 pt-4">
                <div class="grid grid-cols-2 gap-4 text-xs text-gray-600">
                    <div>
                        <span class="font-semibold">Dibuat:</span>
                        <span id="detailCreatedAt">-</span>
                    </div>
                    <div>
                        <span class="font-semibold">Terakhir Diupdate:</span>
                        <span id="detailUpdatedAt">-</span>
                    </div>
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex justify-end pt-4">
                <button onclick="closeDetailModal()"
                    class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
async function openDetailModal(id) {
    try {
        const response = await fetch(`/pengajuan-hutang/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const ph = data.data;
            
            // Basic Info
            document.getElementById('detailNama').textContent = ph.nama;
            document.getElementById('detailPeminjam').textContent = ph.user.name;
            document.getElementById('detailBidang').textContent = ph.bidang.nama;
            document.getElementById('detailJumlah').textContent = 'Rp ' + formatNumber(ph.jumlah);
            document.getElementById('detailSisaHutangBesar').textContent = 'Rp ' + formatNumber(ph.sisa_hutang);
            document.getElementById('detailKeperluan').textContent = ph.keperluan;
            document.getElementById('detailTanggal').textContent = ph.tanggal_formatted;
            document.getElementById('detailTahun').textContent = ph.tahun;
            
            // Status Badge
            const statusConfig = {
                'draft': { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Draft' },
                'menunggu_konfirmasi_bendahara': { bg: 'bg-yellow-100', text: 'text-yellow-800', label: 'Menunggu Bendahara' },
                'menunggu_approval_ketua': { bg: 'bg-blue-100', text: 'text-blue-800', label: 'Menunggu Ketua' },
                'menunggu_pencairan': { bg: 'bg-purple-100', text: 'text-purple-800', label: 'Menunggu Pencairan' },
                'dicairkan': { bg: 'bg-orange-100', text: 'text-orange-800', label: 'Dicairkan' },
                'lunas': { bg: 'bg-green-100', text: 'text-green-800', label: 'Lunas' },
                'ditolak_bendahara': { bg: 'bg-red-100', text: 'text-red-800', label: 'Ditolak Bendahara' },
                'ditolak_ketua': { bg: 'bg-red-100', text: 'text-red-800', label: 'Ditolak Ketua' },
            };
            const config = statusConfig[ph.status] || { bg: 'bg-gray-100', text: 'text-gray-800', label: ph.status };
            document.getElementById('detailStatusBadge').innerHTML = `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${config.bg} ${config.text}">
                    ${config.label}
                </span>
            `;
            
            // Progress Bar (jika dicairkan)
            if (ph.status === 'dicairkan' || ph.status === 'lunas') {
                document.getElementById('detailProgressSection').classList.remove('hidden');
                document.getElementById('detailPersenLunas').textContent = ph.persen_lunas.toFixed(1) + '%';
                document.getElementById('detailProgressBar').style.width = ph.persen_lunas + '%';
                document.getElementById('detailTotalTerbayar').textContent = 'Rp ' + formatNumber(ph.total_terbayar);
                document.getElementById('detailSisaHutang').textContent = 'Rp ' + formatNumber(ph.sisa_hutang);
            } else {
                document.getElementById('detailProgressSection').classList.add('hidden');
            }
            
            // Timeline - Submission
            if (ph.submitted_at) {
                document.getElementById('detailSubmissionSection').classList.remove('hidden');
                document.getElementById('detailSubmittedBy').textContent = ph.submitted_by_name || '-';
                document.getElementById('detailSubmittedAt').textContent = ph.submitted_at_formatted || '-';
            } else {
                document.getElementById('detailSubmissionSection').classList.add('hidden');
            }
            
            // Timeline - Bendahara
            if (ph.reviewed_at_bendahara) {
                document.getElementById('detailBendaharaSection').classList.remove('hidden');
                document.getElementById('detailReviewedByBendahara').textContent = ph.reviewed_by_bendahara_name || '-';
                document.getElementById('detailReviewedAtBendahara').textContent = ph.reviewed_at_bendahara_formatted || '-';
                
                if (ph.catatan_bendahara) {
                    document.getElementById('detailCatatanBendaharaBox').classList.remove('hidden');
                    document.getElementById('detailCatatanBendahara').textContent = ph.catatan_bendahara;
                } else {
                    document.getElementById('detailCatatanBendaharaBox').classList.add('hidden');
                }
            } else {
                document.getElementById('detailBendaharaSection').classList.add('hidden');
            }
            
            // Timeline - Ketua
            if (ph.reviewed_at_ketua) {
                document.getElementById('detailKetuaSection').classList.remove('hidden');
                document.getElementById('detailReviewedByKetua').textContent = ph.reviewed_by_ketua_name || '-';
                document.getElementById('detailReviewedAtKetua').textContent = ph.reviewed_at_ketua_formatted || '-';
                
                if (ph.catatan_ketua) {
                    document.getElementById('detailCatatanKetuaBox').classList.remove('hidden');
                    document.getElementById('detailCatatanKetua').textContent = ph.catatan_ketua;
                } else {
                    document.getElementById('detailCatatanKetuaBox').classList.add('hidden');
                }
            } else {
                document.getElementById('detailKetuaSection').classList.add('hidden');
            }
            
            // Riwayat Pembayaran
            if (ph.pembayaran && ph.pembayaran.length > 0) {
                document.getElementById('detailPembayaranSection').classList.remove('hidden');
                const pembayaranList = document.getElementById('detailPembayaranList');
                pembayaranList.innerHTML = '';
                
                ph.pembayaran.forEach((p, index) => {
                    const pembayaranItem = `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-green-900">Pembayaran #${index + 1}</span>
                                <span class="text-lg font-bold text-green-700">Rp ${formatNumber(p.jumlah_bayar)}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-700">
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
            } else {
                document.getElementById('detailPembayaranSection').classList.add('hidden');
            }
            
            // Timestamps
            document.getElementById('detailCreatedAt').textContent = ph.created_at_formatted;
            document.getElementById('detailUpdatedAt').textContent = ph.updated_at_formatted;
            
            // Show modal dengan animasi
            const modal = document.getElementById('detailModal');
            const modalContent = modal.querySelector('div.bg-white');
            
            // Tampilkan modal (tanpa animasi dulu)
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Tunggu satu frame agar DOM update, lalu trigger animasi
            requestAnimationFrame(() => {
                // Animasikan backdrop (bg opacity)
                modal.classList.remove('bg-opacity-0');
                modal.classList.add('bg-opacity-50');
                
                // Animasikan konten modal (scale dan opacity)
                modalContent.classList.remove('scale-95');
                modalContent.classList.remove('opacity-0');
                modalContent.classList.add('scale-100');
                modalContent.classList.add('opacity-100');
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat detail pengajuan hutang', 'error');
    }
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    const modalContent = modal.querySelector('div.bg-white');
    
    // Animasikan keluar (kebalikan dari masuk)
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    
    modalContent.classList.remove('scale-100');
    modalContent.classList.remove('opacity-100');
    modalContent.classList.add('scale-95');
    modalContent.classList.add('opacity-0');
    
    // Tunggu animasi selesai sebelum sembunyikan modal
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        
        // Reset state untuk next time
        modal.classList.remove('bg-opacity-0');
        modalContent.classList.remove('scale-95');
        modalContent.classList.remove('opacity-0');
    }, 300); // Durasi sama dengan CSS (300ms)
}

function formatNumber(num) {
    return parseFloat(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('detailModal');
        if (!modal.classList.contains('hidden')) {
            closeDetailModal();
        }
    }
});

// Close modal when clicking on backdrop
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailModal();
    }
});
</script>