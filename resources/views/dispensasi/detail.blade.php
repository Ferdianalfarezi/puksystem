<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 transition-opacity duration-250 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform transition-transform duration-250 scale-95">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <div class="flex items-center space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold">Detail Dispensasi</h3>
            </div>
            <button onclick="closeDetailModal()" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            
            <!-- Info Aksi -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-200">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informasi Aksi
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Aksi</p>
                        <p class="text-sm font-medium text-gray-900" id="detailNamaAksi">-</p>
                    </div>
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Bidang</p>
                        <p class="text-sm font-medium text-gray-900" id="detailBidang">-</p>
                    </div>
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tempat Aksi</p>
                        <p class="text-sm font-medium text-gray-900" id="detailTempatAksi">-</p>
                    </div>
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal</p>
                        <p class="text-sm font-medium text-gray-900" id="detailTanggalAksi">-</p>
                    </div>
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jam Aksi</p>
                        <p class="text-sm font-medium text-gray-900" id="detailJamAksi">-</p>
                    </div>
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Lampiran</p>
                        <p class="text-sm font-medium text-gray-900" id="detailLampiran">-</p>
                    </div>
                </div>
            </div>

            <!-- Daftar Peserta -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Daftar Peserta
                </h4>
                
                <ul class="space-y-2" id="detailUserList">
                    <!-- Will be filled by JS -->
                </ul>
            </div>

            <!-- Keterangan -->
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Keterangan</p>
                <p class="text-sm text-gray-900 bg-gray-50 rounded-lg p-4" id="detailKeterangan">-</p>
            </div>

            <!-- Metadata -->
            <div class="bg-gray-50 rounded-lg p-4 border-t">
                <p class="text-xs text-gray-500">
                    Dibuat pada: <span class="font-medium text-gray-700" id="detailCreatedAt">-</span>
                </p>
            </div>

            <!-- Close Button -->
            <div class="flex justify-end pt-4 border-t">
                <button 
                    onclick="closeDetailModal()"
                    class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #detailModal.active {
        opacity: 1;
    }
    #detailModal.active > div {
        transform: scale(1);
    }
</style>