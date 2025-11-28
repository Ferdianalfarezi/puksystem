<!-- EDIT Modal -->
<div id="editModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Edit Program Kerja</h2>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="editProgramId">

            @php
                $userRole = Auth::user()->role->nama ?? '';
            @endphp

            <!-- Dropdown Bidang (Hanya untuk Superadmin) -->
            @if($userRole === 'superadmin')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang <span class="text-red-500">*</span></label>
                <select id="editBidangId" name="bidang_id" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Pilih Bidang --</option>
                    @foreach($allBidangs as $bidang)
                        <option value="{{ $bidang->id }}">{{ $bidang->nama }}</option>
                    @endforeach
                </select>
                <span class="text-red-500 text-sm error-message" id="error-edit-bidang_id"></span>
            </div>
            @endif

            <!-- Nama Program -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Program Kerja <span class="text-red-500">*</span></label>
                <input type="text" id="editNama" name="nama" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Contoh: Pelatihan SDM">
                <span class="text-red-500 text-sm error-message" id="error-edit-nama"></span>
            </div>

            <!-- Anggaran -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Anggaran <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                    <input type="number" id="editAnggaran" name="anggaran" required min="0" step="0.01"
                        class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="0">
                </div>
                <span class="text-red-500 text-sm error-message" id="error-edit-anggaran"></span>
            </div>

            <!-- Tahun -->
<div>
    <label for="editTahun" class="block text-sm font-semibold text-gray-700 mb-2">
        Tahun <span class="text-red-500">*</span>
    </label>
    <input 
        type="number" 
        id="editTahun" 
        name="tahun"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
        placeholder="Contoh: 2024"
        min="2000"
        max="2100"
        required
    >
    <span class="text-red-500 text-xs error-message" id="error-edit-tahun"></span>
</div>

<!-- Tanggal -->
<div>
    <label for="editTanggal" class="block text-sm font-semibold text-gray-700 mb-2">
        Tanggal Pelaksanaan <span class="text-red-500">*</span>
    </label>
    <input 
        type="date" 
        id="editTanggal" 
        name="tanggal"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
        required
    >
    <span class="text-red-500 text-xs error-message" id="error-edit-tanggal"></span>
</div>

            <!-- Warning Box -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-1">Perhatian:</p>
                        <p>Hanya program kerja dengan status <strong>draft</strong> yang dapat diedit.</p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>