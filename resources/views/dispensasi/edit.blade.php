<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 transition-opacity duration-250 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-transform duration-250 scale-95">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <div class="flex items-center space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold">Edit Dispensasi</h3>
            </div>
            <button onclick="closeEditModal()" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editForm" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            <input type="hidden" id="editDispensasiId" name="id">
            
            <!-- Pilih Aksi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Pilih Aksi <span class="text-red-500">*</span>
                </label>
                <select 
                    name="pengajuan_budget_id" 
                    id="editPengajuanBudgetId"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
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
                <p class="text-red-500 text-xs mt-1 error-message" id="error-edit-pengajuan_budget_id"></p>
            </div>

            <!-- Pilih Peserta (Dynamic) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Pilih Peserta <span class="text-red-500">*</span>
                    </label>
                    <button 
                        type="button"
                        onclick="addEditPesertaRow()"
                        class="bg-orange-500 text-white px-3 py-1.5 rounded-lg hover:bg-orange-600 font-semibold transition text-sm flex items-center space-x-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Tambah Peserta</span>
                    </button>
                </div>
                
                <div id="editPesertaContainer" class="space-y-3">
                    <!-- Rows akan di-populate dari JavaScript saat edit -->
                </div>
                
                <p class="text-xs text-gray-500 mt-1">Klik tombol + untuk menambah peserta lain</p>
                <p class="text-red-500 text-xs mt-1 error-message" id="error-edit-user_ids"></p>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea 
                    name="keterangan" 
                    id="editKeterangan"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition resize-none"
                    placeholder="Catatan tambahan..."
                ></textarea>
                <p class="text-red-500 text-xs mt-1 error-message" id="error-edit-keterangan"></p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button 
                    type="button"
                    onclick="closeEditModal()"
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="bg-orange-500 text-white px-6 py-2.5 rounded-lg hover:bg-orange-600 font-semibold transition transform hover:scale-105"
                >
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    #editModal.active {
        opacity: 1;
    }
    #editModal.active > div {
        transform: scale(1);
    }
    
    .peserta-select-edit option {
        padding: 8px 12px;
        cursor: pointer;
    }
    
    .peserta-select-edit option:hover {
        background-color: #f3f4f6;
    }
</style>

@push('scripts')
<script>
// Data user options (diambil dari controller) untuk EDIT
const userOptionsDataEdit = [
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

// Fungsi init searchable select untuk EDIT
function initEditSearchableSelect(row) {
    const searchInput = row.querySelector('.peserta-search-edit');
    const selectElement = row.querySelector('.peserta-select-edit');
    const hiddenInput = row.querySelector('.selected-user-value-edit');
    
    // Show dropdown saat input focus
    searchInput.addEventListener('focus', function() {
        selectElement.classList.remove('hidden');
        filterEditOptions(searchInput.value, selectElement);
    });
    
    // Search saat user mengetik
    searchInput.addEventListener('input', function() {
        filterEditOptions(this.value, selectElement);
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

// Fungsi filter options berdasarkan search untuk EDIT
function filterEditOptions(searchTerm, selectElement) {
    const options = selectElement.querySelectorAll('option');
    const term = searchTerm.toLowerCase();
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'none';
            return;
        }
        
        const searchText = option.dataset.search || '';
        
        if (searchText.includes(term)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}

// Fungsi tambah row peserta untuk EDIT
function addEditPesertaRow(selectedUserId = '', selectedUserText = '') {
    const container = document.getElementById('editPesertaContainer');
    
    const newRow = document.createElement('div');
    newRow.className = 'peserta-row-edit';
    
    let optionsHtml = '<option value="">-- Pilih Peserta --</option>';
    userOptionsDataEdit.forEach(user => {
        const selected = selectedUserId && user.id.toString() === selectedUserId.toString() ? 'selected' : '';
        optionsHtml += `<option value="${user.id}" data-search="${user.searchText}" ${selected}>
            ${user.name} - ${user.nik} (${user.bidang})
        </option>`;
    });
    
    newRow.innerHTML = `
        <div class="flex items-center space-x-2">
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    class="peserta-search-edit w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                    placeholder="Cari nama atau NIK..."
                    autocomplete="off"
                    value="${selectedUserText}"
                >
                <select 
                    class="peserta-select-edit absolute top-full left-0 w-full max-h-48 overflow-y-auto border border-gray-300 rounded-lg bg-white shadow-lg z-10 hidden"
                    size="8"
                >
                    ${optionsHtml}
                </select>
                <input type="hidden" class="selected-user-value-edit" name="user_ids[]" value="${selectedUserId}" required>
            </div>
            <button 
                type="button"
                onclick="removeEditPesertaRow(this)"
                class="bg-red-500 text-white p-2.5 rounded-lg hover:bg-red-600 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    `;
    
    container.appendChild(newRow);
    initEditSearchableSelect(newRow);
    updateEditDeleteButtons();
}

// Fungsi hapus row peserta untuk EDIT
function removeEditPesertaRow(button) {
    const row = button.closest('.peserta-row-edit');
    row.remove();
    updateEditDeleteButtons();
}

// Update visibility tombol delete untuk EDIT (minimal 1 row harus ada)
function updateEditDeleteButtons() {
    const rows = document.querySelectorAll('.peserta-row-edit');
    rows.forEach((row, index) => {
        const deleteBtn = row.querySelector('button[onclick*="removeEditPesertaRow"]');
        if (rows.length === 1) {
            deleteBtn.style.display = 'none';
        } else {
            deleteBtn.style.display = 'block';
        }
    });
}

// Reset form saat modal ditutup
function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('editForm').reset();
        document.getElementById('editPesertaContainer').innerHTML = '';
        clearErrors();
        document.body.style.overflow = '';
    }, 250);
}
</script>
@endpush