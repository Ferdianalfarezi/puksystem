<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 modal-backdrop" onclick="if(event.target === this) closeApprovalModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md modal-content" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Approval Dana Sosial</h3>
            <button onclick="closeApprovalModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="approvalForm" class="p-6 space-y-5">
            <input type="hidden" id="approvalDanaSosialId">
            
            <!-- Action -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Pilih Aksi <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative">
                        <input type="radio" name="approval_action" id="approvalActionApprove" value="approve" class="peer sr-only">
                        <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition">
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-medium text-gray-900">Setujui</span>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="approval_action" id="approvalActionReject" value="reject" class="peer sr-only">
                        <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition">
                            <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span class="font-medium text-gray-900">Tolak</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label for="approvalCatatan" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan (Opsional)
                </label>
                <textarea 
                    id="approvalCatatan" 
                    name="catatan"
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition resize-none"
                    placeholder="Tambahkan catatan jika diperlukan..."
                ></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeApprovalModal()" 
                    class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 px-6 py-3 bg-black text-white rounded-lg font-semibold hover:bg-gray-800 transition">
                    Konfirmasi
                </button>
            </div>
        </form>
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