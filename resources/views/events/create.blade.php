<!-- CREATE Modal -->
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Add Event</h2>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="createForm" class="p-6 space-y-4">
            @csrf

            <!-- Nama Event -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Event <span class="text-red-500">*</span>
                </label>
                <input type="text" id="createNamaEvent" name="nama_event" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Masukkan nama event">
                <span class="text-red-500 text-sm error-message" id="error-create-nama_event"></span>
            </div>

            <!-- Grid 2 Columns -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Jumlah Peserta -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Jumlah Peserta <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="createJumlahPeserta" name="jumlah_peserta" required min="1"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="0">
                    <span class="text-red-500 text-sm error-message" id="error-create-jumlah_peserta"></span>
                </div>

                <!-- Waktu Pelaksanaan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Waktu Pelaksanaan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="createWaktuPelaksanaan" name="waktu_pelaksanaan" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <span class="text-red-500 text-sm error-message" id="error-create-waktu_pelaksanaan"></span>
                </div>
            </div>

            <!-- Tempat Pelaksanaan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tempat Pelaksanaan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="createTempatPelaksanaan" name="tempat_pelaksanaan" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Masukkan tempat pelaksanaan">
                <span class="text-red-500 text-sm error-message" id="error-create-tempat_pelaksanaan"></span>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-blue-900">Info</p>
                        <p class="text-xs text-blue-700 mt-1">Event akan dibuat atas nama Anda. Peserta dapat melakukan absensi dengan scan QR Code.</p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeCreateModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Save Event
                </button>
            </div>
        </form>
    </div>
</div>