<!-- TAMBAH KAS Modal -->
<div id="tambahKasModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div id="tambahKasModalContent" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto transform transition-all scale-95">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="bg-green-100 rounded-lg p-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Tambah Kas</h2>
            </div>
            <button onclick="closeTambahKasModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="tambahKasForm" class="p-6 space-y-4">
            @csrf

            <!-- Current Saldo Display -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                <p class="text-sm font-medium text-gray-600 mb-1">Saldo Kas Saat Ini</p>
                <p class="text-3xl font-bold text-green-600">
                    Rp <span id="currentSaldo">{{ number_format($kasGlobal->saldo, 0, ',', '.') }}</span>
                </p>
            </div>

            <!-- Jumlah Setoran -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Jumlah Setoran <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                    <input 
                        type="number" 
                        id="jumlahSetoran" 
                        name="jumlah" 
                        required 
                        min="1" 
                        step="0.01"
                        class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition"
                        placeholder="0">
                </div>
                <span class="text-red-500 text-sm error-message" id="error-jumlah"></span>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Keterangan <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="keteranganSetoran" 
                    name="keterangan" 
                    required
                    rows="4"
                    maxlength="500"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition resize-none"
                    placeholder="Contoh: Setoran kas awal bulan Januari 2025"></textarea>
                <div class="flex justify-between items-center mt-1">
                    <span class="text-red-500 text-sm error-message" id="error-keterangan"></span>
                    <span class="text-xs text-gray-500" id="charCount">0/500</span>
                </div>
            </div>

            <!-- Preview Saldo Baru -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4" id="previewSaldo" style="display: none;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Saldo Setelah Setoran</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">
                            Rp <span id="newSaldo">0</span>
                        </p>
                    </div>
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-1">Perhatian:</p>
                        <p>Transaksi ini akan tercatat di history kas sebagai <strong>uang masuk</strong>.</p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeTambahKasModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-700 transition flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Tambah Kas</span>
                </button>
            </div>
        </form>
    </div>
</div>