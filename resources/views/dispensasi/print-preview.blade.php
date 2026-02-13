<!-- Modal Preview Cetak -->
<div id="printPreviewModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-all duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[95vh] overflow-hidden transform transition-all duration-300 scale-95 flex flex-col">
        <!-- Modal Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
            <h3 class="text-lg font-semibold text-gray-900">Preview Surat Dispensasi</h3>
            <div class="flex items-center space-x-2">
                <button onclick="printFromIframe()" class="bg-black text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-800 transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak</span>
                </button>
                <button onclick="closePrintPreviewModal()" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Modal Body - Iframe Preview -->
        <div class="flex-1 overflow-hidden bg-gray-100 p-4">
            <iframe id="printPreviewIframe" class="w-full h-full bg-white rounded-lg shadow" style="min-height: 70vh;"></iframe>
        </div>
    </div>
</div>