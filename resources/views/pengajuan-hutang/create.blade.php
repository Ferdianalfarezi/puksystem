<!-- CREATE Modal -->
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Tambah Pengajuan Hutang</h2>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="createForm" class="p-6 space-y-4">
            @csrf

            <!-- Dropdown Peminjam (Searchable) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Peminjam <span class="text-red-500">*</span>
                </label>
                <select id="createUserId" name="user_id" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Pilih Peminjam --</option>
                </select>
                <span class="text-red-500 text-sm error-message" id="error-create-user_id"></span>
            </div>

            <!-- Bidang (Auto-fill, Readonly) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang</label>
                <input type="text" id="createBidangDisplay" readonly
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
                    <input type="number" id="createJumlah" name="jumlah" required min="1" step="0.01"
                        class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="0">
                </div>
                <span class="text-red-500 text-sm error-message" id="error-create-jumlah"></span>
            </div>

            <!-- Keperluan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Keperluan <span class="text-red-500">*</span>
                </label>
                <textarea id="createKeperluan" name="keperluan" required rows="4"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Jelaskan keperluan peminjaman..."></textarea>
                <span class="text-red-500 text-sm error-message" id="error-create-keperluan"></span>
            </div>

            <!-- Tahun -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tahun <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="createTahun" 
                    name="tahun"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Contoh: 2024"
                    min="2000"
                    max="2100"
                    required
                    value="{{ now()->year }}"
                >
                <span class="text-red-500 text-sm error-message" id="error-create-tahun"></span>
            </div>

            <!-- Tanggal Peminjaman -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tanggal Peminjaman <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    id="createTanggal" 
                    name="tanggal"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    required
                    value="{{ now()->format('Y-m-d') }}"
                >
                <span class="text-red-500 text-sm error-message" id="error-create-tanggal"></span>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi:</p>
                        <p>Pengajuan hutang akan disimpan sebagai <strong>draft</strong>. Anda dapat mengajukannya setelah data tersimpan.</p>
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

<style>
#createModal.active {
    opacity: 1;
}

#createModal.active > div {
    transform: scale(1);
}
</style>

<script>
let usersData = [];

async function openCreateModal() {
    const modal = document.getElementById('createModal');
    
    // Reset form
    document.getElementById('createForm').reset();
    clearErrors();
    
    // Set default values
    document.getElementById('createTahun').value = {{ now()->year }};
    document.getElementById('createTanggal').value = '{{ now()->format("Y-m-d") }}';
    document.getElementById('createBidangDisplay').value = '';
    
    // Load users for dropdown
    await loadUsers();
    
    document.body.style.overflow = 'hidden';
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    void modal.offsetWidth;
    
    requestAnimationFrame(() => {
        modal.classList.add('active');
    });
}

async function loadUsers() {
    try {
        const response = await fetch('/pengajuan-hutang/create', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            usersData = data.users;
            
            const selectUser = document.getElementById('createUserId');
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
                    document.getElementById('createBidangDisplay').value = selectedOption.dataset.bidangNama;
                } else {
                    document.getElementById('createBidangDisplay').value = '';
                }
            });
        }
    } catch (error) {
        console.error('Error loading users:', error);
        Swal.fire('Error!', 'Gagal memuat data user', 'error');
    }
}

function closeCreateModal() {
    const modal = document.getElementById('createModal');
    
    modal.classList.remove('active');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('createForm').reset();
        clearErrors();
        
        document.body.style.overflow = '';
    }, 250);
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
}

// Submit Create Form
document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();

    const formData = new FormData(this);

    try {
        const response = await fetch('/pengajuan-hutang', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            closeCreateModal();
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
                    const errorElement = document.getElementById(`error-create-${key}`);
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
        closeCreateModal();
    }
});
</script>