<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 transition-opacity duration-250 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-transform duration-250 scale-95">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold">Approve Dispensasi</h3>
            </div>
            <button onclick="closeApproveModal()" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="approveForm" class="p-6 space-y-5">
            @csrf
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-gray-700">
                    Anda akan meng-approve dispensasi untuk aksi:
                </p>
                <p class="text-lg font-bold text-gray-900 mt-2" id="approveDispensasiName"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Catatan (Opsional)
                </label>
                <textarea 
                    name="catatan" 
                    id="approveCatatan"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition resize-none"
                    placeholder="Tambahkan catatan jika diperlukan..."
                ></textarea>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Dispensasi yang di-approve dapat dicetak oleh admin bidang
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button 
                    type="button"
                    onclick="closeApproveModal()"
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="bg-green-500 text-white px-6 py-2.5 rounded-lg hover:bg-green-600 font-semibold transition transform hover:scale-105"
                >
                    Approve Dispensasi
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    #approveModal.active {
        opacity: 1;
    }
    #approveModal.active > div {
        transform: scale(1);
    }
</style>