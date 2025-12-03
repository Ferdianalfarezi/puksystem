<!-- BAYAR Modal -->
<div id="bayarModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all duration-300 scale-95">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-green-50 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="bg-green-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Bayar Hutang</h2>
            </div>
            <button onclick="closeBayarModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="bayarForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="bayarHutangId">
            <input type="hidden" id="bayarSisaHutangMax">

            <!-- Info Peminjam -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-blue-800">
                            Pembayaran untuk: <strong id="bayarNamaPeminjam"></strong>
                        </p>
                        <p class="text-xs text-blue-600 mt-1">
                            Sisa Hutang: <strong id="bayarSisaHutangDisplay" class="text-red-600"></strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Jumlah Bayar -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Jumlah Pembayaran <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                    <input type="number" id="bayarJumlah" name="jumlah_bayar" required min="1" step="0.01"
                        class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition"
                        placeholder="0"
                        oninput="updateSisaSetelahBayar()">
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-gray-600 error-message" id="error-bayar-jumlah_bayar"></span>
                    <button type="button" onclick="bayarLunas()" class="text-xs text-green-600 hover:text-green-700 font-semibold underline">
                        Bayar Lunas
                    </button>
                </div>
            </div>

            <!-- Preview Sisa Setelah Bayar -->
            <div id="previewSisa" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hidden">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-yellow-800">Sisa setelah pembayaran:</span>
                    <span class="text-lg font-bold text-yellow-900" id="sisaSetelahBayar">Rp 0</span>
                </div>
                <div id="lunasIndicator" class="hidden mt-2 text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                        🎉 LUNAS!
                    </span>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Metode Pembayaran <span class="text-red-500">*</span>
                </label>
                <select id="bayarMetode" name="metode_pembayaran" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition">
                    <option value="">-- Pilih Metode --</option>
                    <option value="tunai">Tunai</option>
                    <option value="transfer_bank">Transfer Bank</option>
                    <option value="cek">Cek</option>
                    <option value="giro">Giro</option>
                </select>
                <span class="text-red-500 text-sm error-message" id="error-bayar-metode_pembayaran"></span>
            </div>

            <!-- Nomor Referensi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nomor Referensi (Opsional)
                </label>
                <input type="text" id="bayarNomorReferensi" name="nomor_referensi"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition"
                    placeholder="Contoh: TRX123456789">
                <p class="text-xs text-gray-500 mt-1">Nomor transaksi, nomor cek, atau referensi lainnya</p>
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Catatan (Opsional)
                </label>
                <textarea id="bayarCatatan" name="catatan" rows="3"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition"
                    placeholder="Tambahkan catatan pembayaran..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeBayarModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-green-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-600 transition">
                    Proses Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<style>
#bayarModal.active {
    opacity: 1;
}

#bayarModal.active > div {
    transform: scale(1);
}
</style>

<script>
let currentSisaHutang = 0;

function openBayarModal(id, nama, sisaHutang) {
    currentSisaHutang = sisaHutang;
    
    document.getElementById('bayarHutangId').value = id;
    document.getElementById('bayarSisaHutangMax').value = sisaHutang;
    document.getElementById('bayarNamaPeminjam').textContent = nama;
    document.getElementById('bayarSisaHutangDisplay').textContent = 'Rp ' + formatNumber(sisaHutang);
    
    // Reset form
    document.getElementById('bayarForm').reset();
    document.getElementById('previewSisa').classList.add('hidden');
    clearBayarErrors();
    
    const modal = document.getElementById('bayarModal');
    document.body.style.overflow = 'hidden';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    void modal.offsetWidth;
    requestAnimationFrame(() => {
        modal.classList.add('active');
    });
}

function closeBayarModal() {
    const modal = document.getElementById('bayarModal');
    modal.classList.remove('active');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('bayarForm').reset();
        document.getElementById('previewSisa').classList.add('hidden');
        clearBayarErrors();
        document.body.style.overflow = '';
    }, 250);
}

function clearBayarErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
}

function updateSisaSetelahBayar() {
    const jumlahBayar = parseFloat(document.getElementById('bayarJumlah').value) || 0;
    const sisaHutang = parseFloat(document.getElementById('bayarSisaHutangMax').value);
    
    if (jumlahBayar > 0) {
        document.getElementById('previewSisa').classList.remove('hidden');
        const sisaSetelah = sisaHutang - jumlahBayar;
        document.getElementById('sisaSetelahBayar').textContent = 'Rp ' + formatNumber(Math.max(0, sisaSetelah));
        
        // Show lunas indicator
        if (sisaSetelah <= 0) {
            document.getElementById('lunasIndicator').classList.remove('hidden');
        } else {
            document.getElementById('lunasIndicator').classList.add('hidden');
        }
    } else {
        document.getElementById('previewSisa').classList.add('hidden');
    }
}

function bayarLunas() {
    const sisaHutang = parseFloat(document.getElementById('bayarSisaHutangMax').value);
    document.getElementById('bayarJumlah').value = sisaHutang;
    updateSisaSetelahBayar();
}

// Submit Bayar Form
document.getElementById('bayarForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearBayarErrors();

    const formData = new FormData(this);
    const id = document.getElementById('bayarHutangId').value;

    try {
        const response = await fetch(`/list-hutang/${id}/bayar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            closeBayarModal();
            
            // Special message jika lunas
            const icon = data.data.is_lunas ? 'success' : 'success';
            const title = data.data.is_lunas ? 'Lunas! 🎉' : 'Berhasil!';
            
            Swal.fire({
                icon: icon,
                title: title,
                html: data.message + '<br><small class="text-gray-600">Saldo Kas: Rp ' + formatNumber(data.data.saldo_kas) + '</small>',
                showConfirmButton: true,
                confirmButtonColor: '#10b981',
            }).then(() => {
                location.reload();
            });
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const errorElement = document.getElementById(`error-bayar-${key}`);
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
        Swal.fire('Error!', 'Terjadi kesalahan saat memproses pembayaran!', 'error');
    }
});

function formatNumber(num) {
    return parseFloat(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBayarModal();
    }
});
</script>