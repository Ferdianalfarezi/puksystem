<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 transition-opacity duration-250 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-transform duration-250 scale-95">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold">Tolak Dispensasi</h3>
            </div>
            <button onclick="closeRejectModal()" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="rejectForm" class="p-6 space-y-5">
            @csrf
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-gray-700">
                    Anda akan menolak dispensasi untuk aksi:
                </p>
                <p class="text-lg font-bold text-gray-900 mt-2" id="rejectDispensasiName"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="catatan" 
                    id="rejectCatatan"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none"
                    placeholder="Jelaskan alasan penolakan..."
                    required
                ></textarea>
                <p class="text-red-500 text-xs mt-1 error-message" id="error-reject-catatan"></p>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Dispensasi yang ditolak akan dikembalikan ke admin bidang
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button 
                    type="button"
                    onclick="closeRejectModal()"
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="bg-red-500 text-white px-6 py-2.5 rounded-lg hover:bg-red-600 font-semibold transition transform hover:scale-105"
                >
                    Tolak Dispensasi
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    #rejectModal.active {
        opacity: 1;
    }
    #rejectModal.active > div {
        transform: scale(1);
    }
</style>