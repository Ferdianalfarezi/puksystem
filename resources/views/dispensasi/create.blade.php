<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 transition-opacity duration-250 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-transform duration-250 scale-95">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-black to-gray-800 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <div class="flex items-center space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold">Tambah Dispensasi</h3>
            </div>
            <button onclick="closeCreateModal()" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="createForm" class="p-6 space-y-5">
            @csrf
            
            <!-- Pilih Aksi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Pilih Aksi <span class="text-red-500">*</span>
                </label>
                <select 
                    name="pengajuan_budget_id" 
                    id="createPengajuanBudgetId"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    required
                >
                    <option value="">-- Pilih Aksi --</option>
                    @foreach($aksiPengajuans as $aksi)
                        <option value="{{ $aksi->id }}">
                            {{ $aksi->nama_aksi }} - {{ $aksi->tempat_aksi }} 
                            ({{ $aksi->tanggal ? $aksi->tanggal->format('d M Y') : '-' }})
                        </option>
                    @endforeach
                </select>
                <p class="text-red-500 text-xs mt-1 error-message" id="error-create-pengajuan_budget_id"></p>
            </div>

            <!-- Pilih Peserta (Dynamic) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Pilih Peserta <span class="text-red-500">*</span>
                    </label>
                    <button 
                        type="button"
                        onclick="addPesertaRow()"
                        class="bg-black text-white px-3 py-1.5 rounded-lg hover:bg-gray-800 font-semibold transition text-sm flex items-center space-x-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Tambah Peserta</span>
                    </button>
                </div>
                
                <div id="pesertaContainer" class="space-y-3">
                    <!-- Row pertama default -->
                    <div class="peserta-row">
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 relative">
                                <input 
                                    type="text" 
                                    class="peserta-search w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                                    placeholder="Cari nama atau NIK..."
                                    autocomplete="off"
                                >
                                <select 
                                    name="user_ids[]" 
                                    class="peserta-select absolute top-full left-0 w-full max-h-48 overflow-y-auto border border-gray-300 rounded-lg bg-white shadow-lg z-10 hidden"
                                    size="8"
                                    required
                                >
                                    <option value="">-- Pilih Peserta --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" data-name="{{ strtolower($user->name) }}" data-nik="{{ strtolower($user->nik ?? '') }}" data-bidang="{{ strtolower($user->bidang->nama ?? '') }}">
                                            {{ $user->name }} - {{ $user->nik ?? 'No NIK' }} ({{ $user->bidang->nama ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" class="selected-user-value" name="user_ids[]" required>
                            </div>
                            <button 
                                type="button"
                                onclick="removePesertaRow(this)"
                                class="bg-red-500 text-white p-2.5 rounded-lg hover:bg-red-600 transition"
                                style="display: none;"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 mt-1">Klik tombol + untuk menambah peserta lain</p>
                <p class="text-red-500 text-xs mt-1 error-message" id="error-create-user_ids"></p>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea 
                    name="keterangan" 
                    id="createKeterangan"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition resize-none"
                    placeholder="Catatan tambahan..."
                ></textarea>
                <p class="text-red-500 text-xs mt-1 error-message" id="error-create-keterangan"></p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button 
                    type="button"
                    onclick="closeCreateModal()"
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="bg-black text-white px-6 py-2.5 rounded-lg hover:bg-gray-800 font-semibold transition transform hover:scale-105"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    #createModal.active {
        opacity: 1;
    }
    #createModal.active > div {
        transform: scale(1);
    }
    
    .peserta-select option {
        padding: 8px 12px;
        cursor: pointer;
    }
    
    .peserta-select option:hover {
        background-color: #f3f4f6;
    }
</style>

@push('scripts')
<script>
// Template options user untuk dipakai saat add row baru
const userOptionsData = [
    @foreach($users as $user)
    {
        id: '{{ $user->id }}',
        name: '{{ $user->name }}',
        nik: '{{ $user->nik ?? "No NIK" }}',
        bidang: '{{ $user->bidang->nama ?? "-" }}',
        searchText: '{{ strtolower($user->name . " " . ($user->nik ?? "") . " " . ($user->bidang->nama ?? "")) }}'
    },
    @endforeach
];

// Fungsi init searchable select
function initSearchableSelect(row) {
    const searchInput = row.querySelector('.peserta-search');
    const selectElement = row.querySelector('.peserta-select');
    const hiddenInput = row.querySelector('.selected-user-value');
    
    // Show dropdown saat input focus
    searchInput.addEventListener('focus', function() {
        selectElement.classList.remove('hidden');
        filterOptions(searchInput.value, selectElement);
    });
    
    // Search saat user mengetik
    searchInput.addEventListener('input', function() {
        filterOptions(this.value, selectElement);
    });
    
    // Pilih option saat diklik
    selectElement.addEventListener('click', function(e) {
        if (e.target.tagName === 'OPTION' && e.target.value) {
            searchInput.value = e.target.textContent.trim();
            hiddenInput.value = e.target.value;
            selectElement.classList.add('hidden');
        }
    });
    
    // Hide dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        if (!row.contains(e.target)) {
            selectElement.classList.add('hidden');
        }
    });
}

// Fungsi filter options berdasarkan search
function filterOptions(searchTerm, selectElement) {
    const options = selectElement.querySelectorAll('option');
    const term = searchTerm.toLowerCase();
    let hasVisibleOption = false;
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'none';
            return;
        }
        
        const name = option.dataset.name || '';
        const nik = option.dataset.nik || '';
        const bidang = option.dataset.bidang || '';
        const searchText = name + ' ' + nik + ' ' + bidang;
        
        if (searchText.includes(term)) {
            option.style.display = 'block';
            hasVisibleOption = true;
        } else {
            option.style.display = 'none';
        }
    });
    
    // Tampilkan "tidak ada hasil" jika tidak ada option yang match
    if (!hasVisibleOption && searchTerm) {
        // Bisa tambahkan pesan "tidak ada hasil" jika perlu
    }
}

// Fungsi tambah row peserta
function addPesertaRow() {
    const container = document.getElementById('pesertaContainer');
    
    const newRow = document.createElement('div');
    newRow.className = 'peserta-row';
    
    let optionsHtml = '<option value="">-- Pilih Peserta --</option>';
    userOptionsData.forEach(user => {
        optionsHtml += `<option value="${user.id}" data-name="${user.searchText}" data-nik="${user.searchText}" data-bidang="${user.searchText}">
            ${user.name} - ${user.nik} (${user.bidang})
        </option>`;
    });
    
    newRow.innerHTML = `
        <div class="flex items-center space-x-2">
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    class="peserta-search w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Cari nama atau NIK..."
                    autocomplete="off"
                >
                <select 
                    class="peserta-select absolute top-full left-0 w-full max-h-48 overflow-y-auto border border-gray-300 rounded-lg bg-white shadow-lg z-10 hidden"
                    size="8"
                >
                    ${optionsHtml}
                </select>
                <input type="hidden" class="selected-user-value" name="user_ids[]" required>
            </div>
            <button 
                type="button"
                onclick="removePesertaRow(this)"
                class="bg-red-500 text-white p-2.5 rounded-lg hover:bg-red-600 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    `;
    
    container.appendChild(newRow);
    initSearchableSelect(newRow);
    updateDeleteButtons();
}

// Fungsi hapus row peserta
function removePesertaRow(button) {
    const row = button.closest('.peserta-row');
    row.remove();
    updateDeleteButtons();
}

// Update visibility tombol delete (minimal 1 row harus ada)
function updateDeleteButtons() {
    const rows = document.querySelectorAll('.peserta-row');
    rows.forEach((row, index) => {
        const deleteBtn = row.querySelector('button[onclick*="removePesertaRow"]');
        if (rows.length === 1) {
            deleteBtn.style.display = 'none';
        } else {
            deleteBtn.style.display = 'block';
        }
    });
}

// Reset form saat modal ditutup
function closeCreateModal() {
    document.getElementById('createModal').classList.remove('active');
    setTimeout(() => {
        document.getElementById('createModal').classList.add('hidden');
        document.getElementById('createForm').reset();
        
        // Reset ke 1 row peserta
        const container = document.getElementById('pesertaContainer');
        let optionsHtml = '<option value="">-- Pilih Peserta --</option>';
        userOptionsData.forEach(user => {
            optionsHtml += `<option value="${user.id}" data-name="${user.searchText}" data-nik="${user.searchText}" data-bidang="${user.searchText}">
                ${user.name} - ${user.nik} (${user.bidang})
            </option>`;
        });
        
        container.innerHTML = `
            <div class="peserta-row">
                <div class="flex items-center space-x-2">
                    <div class="flex-1 relative">
                        <input 
                            type="text" 
                            class="peserta-search w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                            placeholder="Cari nama atau NIK..."
                            autocomplete="off"
                        >
                        <select 
                            class="peserta-select absolute top-full left-0 w-full max-h-48 overflow-y-auto border border-gray-300 rounded-lg bg-white shadow-lg z-10 hidden"
                            size="8"
                        >
                            ${optionsHtml}
                        </select>
                        <input type="hidden" class="selected-user-value" name="user_ids[]" required>
                    </div>
                    <button 
                        type="button"
                        onclick="removePesertaRow(this)"
                        class="bg-red-500 text-white p-2.5 rounded-lg hover:bg-red-600 transition"
                        style="display: none;"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>A
        `;
        
        // Init ulang searchable select
        const firstRow = container.querySelector('.peserta-row');
        initSearchableSelect(firstRow);
    }, 250);
}

// Init searchable select untuk row pertama saat document ready
document.addEventListener('DOMContentLoaded', function() {
    const firstRow = document.querySelector('.peserta-row');
    if (firstRow) {
        initSearchableSelect(firstRow);
    }
});
</script>
@endpush