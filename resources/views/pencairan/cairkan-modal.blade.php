<!-- Cairkan Modal -->
<div id="cairkanModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4 transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Pencairan Dana</h3>
                        <p class="text-sm text-green-100">Proses pencairan dana program kerja</p>
                    </div>
                </div>
                <button type="button" onclick="closeCairkanModal()" class="text-white hover:text-green-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="cairkanForm" class="p-6 space-y-6">
            @csrf

            <!-- Info Program -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 space-y-2">
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-green-900">Program Kerja:</p>
                        <p class="text-base font-bold text-green-800" id="cairkanProgramName"></p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-green-200">
                    <span class="text-sm font-medium text-green-700">Anggaran yang Disetujui:</span>
                    <span class="text-lg font-bold text-green-900" id="cairkanProgramAnggaran"></span>
                </div>
            </div>

            <!-- Jumlah Dicairkan -->
            <div>
                <label for="jumlahDicairkan" class="block text-sm font-semibold text-gray-700 mb-2">
                    Jumlah Dicairkan <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="jumlahDicairkan" 
                    name="jumlah_dicairkan"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                    placeholder="Masukkan jumlah pencairan"
                    min="0"
                    step="0.01"
                    required
                >
                <p class="text-xs text-gray-500 mt-1">Jumlah yang akan dicairkan (max sesuai anggaran)</p>
            </div>

            <!-- Metode Pencairan -->
            <div>
                <label for="metodePencairan" class="block text-sm font-semibold text-gray-700 mb-2">
                    Metode Pencairan <span class="text-red-500">*</span>
                </label>
                <select 
                    id="metodePencairan" 
                    name="metode_pencairan"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                    required
                >
                    <option value="transfer">Transfer Bank</option>
                    <option value="tunai">Tunai</option>
                    <option value="cek">Cek</option>
                </select>
                <span class="text-red-500 text-xs error-message" id="error-metode_pencairan"></span>
            </div>

            <!-- Nomor Referensi -->
            <div>
                <label for="nomorReferensi" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nomor Referensi
                </label>
                <input 
                    type="text" 
                    id="nomorReferensi" 
                    name="nomor_referensi"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                    placeholder="Nomor transfer / cek (opsional)"
                >
                <p class="text-xs text-gray-500 mt-1">Contoh: TF123456789 atau CEK-2024-001</p>
                <span class="text-red-500 text-xs error-message" id="error-nomor_referensi"></span>
            </div>

            <!-- Catatan -->
            <div>
                <label for="catatanPencairan" class="block text-sm font-semibold text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea 
                    id="catatanPencairan" 
                    name="catatan"
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition resize-none"
                    placeholder="Catatan tambahan (opsional)"
                ></textarea>
                <span class="text-red-500 text-xs error-message" id="error-catatan"></span>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="closeCairkanModal()"
                    class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Cairkan Dana</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
#cairkanModal.active {
    opacity: 1;
}

#cairkanModal.active > div {
    transform: scale(1);
}
</style>