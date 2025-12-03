<!-- REJECT Modal -->
<div id="rejectModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all duration-300 scale-95">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-red-50 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="bg-red-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Tolak Pengajuan Hutang</h2>
            </div>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="rejectForm" class="p-6 space-y-4">
            @csrf

            <!-- Warning -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-sm text-red-800">
                        <p class="font-semibold">Perhatian!</p>
                        <p class="mt-1">Anda akan menolak pengajuan hutang: <strong id="rejectHutangName"></strong></p>
                    </div>
                </div>
            </div>

            <!-- Catatan (Required) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea id="rejectCatatan" name="catatan" rows="4" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500 transition"
                    placeholder="Jelaskan alasan penolakan..."></textarea>
                <span class="text-red-500 text-sm error-message" id="error-reject-catatan"></span>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-red-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-600 transition">
                    Tolak Pengajuan
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