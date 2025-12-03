<!-- EDIT Modal -->
<div id="editModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Edit Event</h2>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editForm" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editEventId" name="id">

            <!-- Nama Event -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Event <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editNamaEvent" name="nama_event" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Masukkan nama event">
                <span class="text-red-500 text-sm error-message" id="error-edit-nama_event"></span>
            </div>

            <!-- Grid 2 Columns -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Jumlah Peserta -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Jumlah Peserta <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="editJumlahPeserta" name="jumlah_peserta" required min="1"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="0">
                    <span class="text-red-500 text-sm error-message" id="error-edit-jumlah_peserta"></span>
                </div>

                <!-- Waktu Pelaksanaan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Waktu Pelaksanaan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="editWaktuPelaksanaan" name="waktu_pelaksanaan" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <span class="text-red-500 text-sm error-message" id="error-edit-waktu_pelaksanaan"></span>
                </div>
            </div>

            <!-- Tempat Pelaksanaan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tempat Pelaksanaan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editTempatPelaksanaan" name="tempat_pelaksanaan" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Masukkan tempat pelaksanaan">
                <span class="text-red-500 text-sm error-message" id="error-edit-tempat_pelaksanaan"></span>
            </div>

            <!-- Warning Box -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-900">Perhatian</p>
                        <p class="text-xs text-yellow-700 mt-1">Pastikan data yang diubah sudah benar. Perubahan akan mempengaruhi data kehadiran yang sudah tercatat.</p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Update Event
                </button>
            </div>
        </form>
    </div>
</div>