<!-- EDIT Modal -->
<div id="editModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Edit Pengajuan Hutang</h2>
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
            <input type="hidden" id="editHutangId">

            <!-- Dropdown Peminjam (Searchable) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Peminjam <span class="text-red-500">*</span>
                </label>
                <select id="editUserId" name="user_id" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Pilih Peminjam --</option>
                </select>
                <span class="text-red-500 text-sm error-message" id="error-edit-user_id"></span>
            </div>

            <!-- Bidang (Auto-fill, Readonly) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang</label>
                <input type="text" id="editBidangDisplay" readonly
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 text-gray-600 cursor-not-allowed"
                    placeholder="Otomatis terisi setelah memilih peminjam">
            </div>

            <!-- Jumlah Hutang -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Jumlah Hutang <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                    <input type="number" id="editJumlah" name="jumlah" required min="1" step="0.01"
                        class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="0">
                </div>
                <span class="text-red-500 text-sm error-message" id="error-edit-jumlah"></span>
            </div>

            <!-- Keperluan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Keperluan <span class="text-red-500">*</span>
                </label>
                <textarea id="editKeperluan" name="keperluan" required rows="4"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Jelaskan keperluan peminjaman..."></textarea>
                <span class="text-red-500 text-sm error-message" id="error-edit-keperluan"></span>
            </div>

            <!-- Tahun -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
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
                <span class="text-red-500 text-sm error-message" id="error-edit-tahun"></span>
            </div>

            <!-- Tanggal Peminjaman -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tanggal Peminjaman <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    id="editTanggal" 
                    name="tanggal"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    required
                >
                <span class="text-red-500 text-sm error-message" id="error-edit-tanggal"></span>
            </div>

            <!-- Info Box -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-1">Perhatian:</p>
                        <p>Hanya pengajuan hutang dengan status <strong>draft</strong> yang dapat diedit.</p>
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

<style>
#editModal.active {
    opacity: 1;
}

#editModal.active > div {
    transform: scale(1);
}
</style>

<script>
async function openEditModal(id) {
    try {
        // Load users for dropdown first
        await loadUsersForEdit();
        
        // Fetch pengajuan hutang data
        const response = await fetch(`/pengajuan-hutang/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const ph = data.data;
            
            document.getElementById('editHutangId').value = ph.id;
            document.getElementById('editUserId').value = ph.user.id;
            document.getElementById('editJumlah').value = ph.jumlah;
            document.getElementById('editKeperluan').value = ph.keperluan;
            document.getElementById('editTahun').value = ph.tahun;
            document.getElementById('editTanggal').value = ph.tanggal;
            
            // Trigger change event to auto-fill bidang
            const selectUser = document.getElementById('editUserId');
            const selectedOption = selectUser.options[selectUser.selectedIndex];
            if (selectedOption && selectedOption.dataset.bidangNama) {
                document.getElementById('editBidangDisplay').value = selectedOption.dataset.bidangNama;
            }
            
            clearErrors();
            
            const modal = document.getElementById('editModal');
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            void modal.offsetWidth;
            requestAnimationFrame(() => {
                modal.classList.add('active');
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat data pengajuan hutang', 'error');
    }
}

async function loadUsersForEdit() {
    try {
        const response = await fetch('/pengajuan-hutang/create', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const selectUser = document.getElementById('editUserId');
            selectUser.innerHTML = '<option value="">-- Pilih Peminjam --</option>';
            
            data.users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.name} (${user.bidang_nama})`;
                option.dataset.bidangId = user.bidang_id;
                option.dataset.bidangNama = user.bidang_nama;
                selectUser.appendChild(option);
            });
            
            // Event listener untuk auto-fill bidang
            selectUser.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    document.getElementById('editBidangDisplay').value = selectedOption.dataset.bidangNama;
                } else {
                    document.getElementById('editBidangDisplay').value = '';
                }
            });
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    
    modal.classList.remove('active');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('editForm').reset();
        clearErrors();
        
        document.body.style.overflow = '';
    }, 250);
}

// Submit Edit Form
document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();

    const formData = new FormData(this);
    const id = document.getElementById('editHutangId').value;
    formData.append('_method', 'PUT');

    try {
        const response = await fetch(`/pengajuan-hutang/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            closeEditModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const errorElement = document.getElementById(`error-edit-${key}`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[key][0];
                    }
                });
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});
</script>   