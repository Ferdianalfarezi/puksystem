// Tambah Kas Modal Functions
function openTambahKasModal() {
    const modal = document.getElementById('tambahKasModal');
    const modalContent = document.getElementById('tambahKasModalContent');
    
    // Reset form
    document.getElementById('tambahKasForm').reset();
    document.getElementById('previewSaldo').style.display = 'none';
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    
    // Show modal with animation
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }, 10);
}

function closeTambahKasModal() {
    const modal = document.getElementById('tambahKasModal');
    const modalContent = document.getElementById('tambahKasModalContent');
    
    // Hide with animation
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

// Character counter for keterangan
document.addEventListener('DOMContentLoaded', function() {
    const keteranganInput = document.getElementById('keteranganSetoran');
    const charCount = document.getElementById('charCount');
    
    if (keteranganInput && charCount) {
        keteranganInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/500`;
        });
    }
    
    // Preview saldo baru
    const jumlahInput = document.getElementById('jumlahSetoran');
    if (jumlahInput) {
        jumlahInput.addEventListener('input', function() {
            const currentSaldoText = document.getElementById('currentSaldo').textContent.replace(/\./g, '');
            const currentSaldo = parseFloat(currentSaldoText) || 0;
            const jumlah = parseFloat(this.value) || 0;
            
            if (jumlah > 0) {
                const newSaldo = currentSaldo + jumlah;
                document.getElementById('newSaldo').textContent = newSaldo.toLocaleString('id-ID');
                document.getElementById('previewSaldo').style.display = 'block';
            } else {
                document.getElementById('previewSaldo').style.display = 'none';
            }
        });
    }
});

// Handle form submission
document.getElementById('tambahKasForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    fetch('/kas/setor', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonColor: '#16a34a',
            }).then(() => {
                // Reload page to update saldo
                location.reload();
            });
            
            closeTambahKasModal();
        } else {
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const errorElement = document.getElementById(`error-${key}`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[key][0];
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan saat menambah kas.',
                    confirmButtonColor: '#dc2626',
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan pada server.',
            confirmButtonColor: '#dc2626',
        });
    })
    .finally(() => {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});

// Close modal when clicking outside
document.getElementById('tambahKasModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeTambahKasModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('tambahKasModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeTambahKasModal();
        }
    }
});