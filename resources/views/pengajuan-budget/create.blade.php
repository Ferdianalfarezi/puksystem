<!-- CREATE Modal -->
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Tambah Pengajuan Budget</h2>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="createForm" class="p-6 space-y-4" enctype="multipart/form-data">
            @csrf

            @php
                $userRole = Auth::user()->role->nama ?? '';
            @endphp

            <!-- Dropdown Bidang (Hanya untuk Superadmin) -->
            @if($userRole === 'superadmin')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang <span class="text-red-500">*</span></label>
                <select id="createBidangId" name="bidang_id" required onchange="onBidangChange('create')"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Pilih Bidang --</option>
                    @foreach($allBidangs as $bidang)
                        <option value="{{ $bidang->id }}">{{ $bidang->nama }}</option>
                    @endforeach
                </select>
                <span class="text-red-500 text-sm error-message" id="error-create-bidang_id"></span>
            </div>
            @else
            <!-- Admin: Tampilkan bidang sebagai readonly -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang</label>
                <input type="text" value="{{ Auth::user()->bidang->nama ?? '-' }}" readonly
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-100 text-gray-600 cursor-not-allowed">
                <input type="hidden" name="bidang_id" value="{{ Auth::user()->bidang_id }}">
            </div>
            @endif

            <!-- Jenis -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis <span class="text-red-500">*</span></label>
                <select id="createJenis" name="jenis" required onchange="onJenisChange('create')"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="program_kerja">Program Kerja</option>
                    <option value="pengajuan_budget">Pengajuan Budget</option>
                </select>
                <span class="text-red-500 text-sm error-message" id="error-create-jenis"></span>
            </div>

            <!-- Dropdown Program Kerja (Muncul jika jenis = program_kerja) -->
            <div id="createProgramKerjaWrapper" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Program Kerja <span class="text-red-500">*</span></label>
                <select id="createProgramKerjaId" name="program_kerja_id" onchange="onProgramKerjaChange('create')"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Pilih Program Kerja --</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Data akan otomatis terisi sesuai program kerja yang dipilih</p>
                <span class="text-red-500 text-sm error-message" id="error-create-program_kerja_id"></span>
            </div>

            <!-- Nama Pengajuan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Pengajuan Budget <span class="text-red-500">*</span></label>
                <input type="text" id="createNama" name="nama" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Contoh: Pembelian ATK Tahun 2025">
                <span class="text-red-500 text-sm error-message" id="error-create-nama"></span>
            </div>

            <!-- Jenis Pengeluaran -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pengeluaran <span class="text-red-500">*</span></label>
                <select id="createJenisPengeluaran" name="jenis_pengeluaran" required onchange="toggleAksiFields()"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Pilih Jenis Pengeluaran --</option>
                    @foreach(\App\Models\PengajuanBudget::JENIS_PENGELUARAN as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
                <span class="text-red-500 text-sm error-message" id="error-create-jenis_pengeluaran"></span>
            </div>

            <!-- ✅ FIELD TAMBAHAN UNTUK AKSI -->
            <div id="aksiFieldsContainer" class="hidden space-y-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-semibold text-blue-900 mb-2">📋 Detail Aksi</p>
                
                <!-- No Surat -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No Surat <span class="text-red-500">*</span></label>
                    <input type="text" id="createNoSurat" name="no_surat"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Contoh: 001/AKSI/2024">
                    <span class="text-red-500 text-sm error-message" id="error-create-no_surat"></span>
                </div>
                
                <!-- Jumlah Anggota -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Anggota <span class="text-red-500">*</span></label>
                    <input type="number" id="createJumlahAnggota" name="jumlah_anggota" min="1"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Contoh: 50">
                    <span class="text-red-500 text-sm error-message" id="error-create-jumlah_anggota"></span>
                </div>
                
                <!-- Nama Aksi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Aksi <span class="text-red-500">*</span></label>
                    <input type="text" id="createNamaAksi" name="nama_aksi"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Contoh: Demo Tolak Omnibus Law">
                    <span class="text-red-500 text-sm error-message" id="error-create-nama_aksi"></span>
                </div>
                
                <!-- Tempat Aksi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tempat Aksi <span class="text-red-500">*</span></label>
                    <input type="text" id="createTempatAksi" name="tempat_aksi"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Contoh: Gedung DPR RI">
                    <span class="text-red-500 text-sm error-message" id="error-create-tempat_aksi"></span>
                </div>
                
                <!-- Jam Aksi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Aksi <span class="text-red-500">*</span></label>
                    <input type="time" id="createJamAksi" name="jam_aksi"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <span class="text-red-500 text-sm error-message" id="error-create-jam_aksi"></span>
                </div>
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
                <label for="createTahun" class="block text-sm font-semibold text-gray-700 mb-2">
                    Tahun <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="createTahun" 
                    name="tahun"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Contoh: 2025"
                    min="2000"
                    max="2100"
                    required
                >
                <span class="text-red-500 text-xs error-message" id="error-create-tahun"></span>
            </div>

            <!-- ✅ LAMPIRAN PDF -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Lampiran (PDF)
                    <span class="text-gray-500 text-xs font-normal ml-1">(Opsional)</span>
                </label>
                <input type="file" id="createLampiran" name="lampiran" accept=".pdf"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
                <p class="mt-1 text-xs text-gray-500">📄 Format: PDF | 📦 Maksimal: 5MB</p>
                <span class="text-red-500 text-sm error-message" id="error-create-lampiran"></span>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi:</p>
                        <p>Pengajuan budget akan disimpan sebagai <strong>draft</strong>. Anda dapat mengajukannya setelah data tersimpan.</p>
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