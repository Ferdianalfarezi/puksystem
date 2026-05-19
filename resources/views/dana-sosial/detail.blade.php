<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 modal-backdrop" onclick="if(event.target === this) closeDetailModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto modal-content" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 sticky top-0 bg-white rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-900">Detail Dana Sosial</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4">
            <!-- Koorlap -->
            <div class="flex justify-between py-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Koorlap</span>
                <span class="text-sm font-medium text-gray-900" id="detailKoorlap">-</span>
            </div>

            <!-- Penerima -->
            <div class="py-3 border-b border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Penerima</p>
                <p class="text-sm font-medium text-gray-900" id="detailUser">-</p>
                <p class="text-xs text-gray-500">NIK: <span id="detailNik">-</span></p>
                <p class="text-xs text-gray-500">Bidang: <span id="detailBidang">-</span></p>
            </div>

            <!-- Jenis -->
            <div class="flex justify-between py-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Jenis</span>
                <span class="text-sm font-medium text-gray-900" id="detailJenis">-</span>
            </div>

            <!-- Nominal -->
            <div class="flex justify-between py-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Nominal</span>
                <span class="text-sm font-bold text-gray-900" id="detailNominal">-</span>
            </div>

            <!-- Status -->
            <div class="flex justify-between py-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Status</span>
                <span id="detailStatus" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">-</span>
            </div>

            <!-- Evident -->
            <div class="flex justify-between py-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Evident</span>
                <span id="detailEvidentContainer">-</span>
            </div>

            <!-- Created At -->
            <div class="flex justify-between py-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Tanggal Pengajuan</span>
                <span class="text-sm font-medium text-gray-900" id="detailCreatedAt">-</span>
            </div>

            <!-- Approval Info -->
            <div id="detailApprovalInfo" class="hidden py-3 border-b border-gray-100 bg-blue-50 -mx-6 px-6">
                <p class="text-sm font-semibold text-blue-800 mb-2">Info Approval</p>
                <!-- Dynamic content -->
            </div>

            <!-- Verification Info -->
            <div id="detailVerifyInfo" class="hidden py-3 bg-green-50 -mx-6 px-6">
                <p class="text-sm font-semibold text-green-800 mb-2">Info Penyerahan</p>
                <!-- Dynamic content -->
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 border-t border-gray-200">
            <button onclick="closeDetailModal()" 
                class="w-full px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<style>
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.25s ease-out;
    }
    .modal-backdrop.active {
        opacity: 1;
    }
    .modal-content {
        transform: scale(0.95) translateY(-10px);
        opacity: 0;
        transition: all 0.25s ease-out;
    }
    .modal-backdrop.active .modal-content {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
</style>