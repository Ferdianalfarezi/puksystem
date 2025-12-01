<!-- CREATE Modal -->
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Tambah Program Kerja</h2>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="createForm" class="p-6 space-y-4">
            @csrf

            @php
                $userRole = Auth::user()->role->nama ?? '';
            @endphp

            <!-- Dropdown Bidang (Hanya untuk Superadmin) -->
            @if($userRole === 'superadmin')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang <span class="text-red-500">*</span></label>
                <select id="createBidangId" name="bidang_id" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Pilih Bidang --</option>
                    @foreach($allBidangs as $bidang)
                        <option value="{{ $bidang->id }}">{{ $bidang->nama }}</option>
                    @endforeach
                </select>
                <span class="text-red-500 text-sm error-message" id="error-create-bidang_id"></span>
            </div>
            @endif

            <!-- Nama Program -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Program Kerja <span class="text-red-500">*</span></label>
                <input type="text" id="createNama" name="nama" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Contoh: Pelatihan SDM">
                <span class="text-red-500 text-sm error-message" id="error-create-nama"></span>
            </div>

            <!-- ✅ TAMBAHKAN: Jenis Pengeluaran -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pengeluaran <span class="text-red-500">*</span></label>
                <select id="createJenisPengeluaran" name="jenis_pengeluaran" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Pilih Jenis Pengeluaran --</option>
                    @foreach(\App\Models\ProgramKerja::JENIS_PENGELUARAN as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
                <span class="text-red-500 text-sm error-message" id="error-create-jenis_pengeluaran"></span>
            </div>

            <!-- Anggaran -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Anggaran <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                    <input type="number" id="createAnggaran" name="anggaran" required min="0" step="0.01"
                        class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="0">
                </div>
                <span class="text-red-500 text-sm error-message" id="error-create-anggaran"></span>
            </div>

            <!-- Tahun -->
            <div>
                <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-2">
                    Tahun <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="tahun" 
                    name="tahun"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Contoh: 2024"
                    min="2000"
                    max="2100"
                    required
                >
                <span class="text-red-500 text-xs error-message" id="error-create-tahun"></span>
            </div>

            <!-- Tanggal -->
            <div>
                <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">
                    Tanggal Pelaksanaan <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    id="tanggal" 
                    name="tanggal"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    required
                >
                <span class="text-red-500 text-xs error-message" id="error-create-tanggal"></span>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi:</p>
                        <p>Program kerja akan disimpan sebagai <strong>draft</strong>. Anda dapat mengajukannya setelah data tersimpan.</p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeCreateModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>