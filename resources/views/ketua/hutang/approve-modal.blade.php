<!-- APPROVE Modal Ketua -->
<div id="approveModalKetua" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all duration-300 scale-95">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-purple-50 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Approve Pengajuan Hutang</h2>
            </div>
            <button onclick="closeApproveModalKetua()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="approveFormKetua" class="p-6 space-y-4">
            @csrf

            <!-- Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    Anda akan meng-approve pengajuan hutang: <strong id="approveHutangNameKetua"></strong>
                </p>
                <p class="text-xs text-blue-600 mt-2">
                    Setelah di-approve, pengajuan akan menunggu pencairan dana oleh bendahara.
                </p>
            </div>

            <!-- Catatan (Optional) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea id="approveCatatanKetua" name="catatan" rows="4"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition"
                    placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeApproveModalKetua()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-purple-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-purple-600 transition">
                    Approve & Lanjutkan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
#approveModalKetua.active {
    opacity: 1;
}

#approveModalKetua.active > div {
    transform: scale(1);
}
</style>