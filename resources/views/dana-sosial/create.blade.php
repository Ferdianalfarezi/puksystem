<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 modal-backdrop" onclick="if(event.target === this) closeCreateModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto modal-content" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 sticky top-0 bg-white rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-900">Ajukan Dana Sosial</h3>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="createForm" class="p-6 space-y-5" enctype="multipart/form-data">
            @csrf
            
            @php
                $user = Auth::user();
                $userRole = $user->role->nama ?? '';
            @endphp

            <!-- Koorlap (Superadmin only) -->
            @if($userRole === 'superadmin')
            <div>
                <label for="createKoorlapId" class="block text-sm font-medium text-gray-700 mb-2">
                    Koorlap <span class="text-red-500">*</span>
                </label>
                <select 
                    id="createKoorlapId" 
                    name="koorlap_id" 
                    required
                    onchange="onKoorlapChange()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="">-- Pilih Koorlap --</option>
                    @foreach($koorlaps as $koorlap)
                        <option value="{{ $koorlap->id }}">{{ $koorlap->nama }}</option>
                    @endforeach
                </select>
                <p class="error-message text-red-500 text-sm mt-1" id="error-create-koorlap_id"></p>
            </div>
            @endif

            <!-- User/Penerima -->
            <div>
                <label for="createUserId" class="block text-sm font-medium text-gray-700 mb-2">
                    Penerima <span class="text-red-500">*</span>
                </label>
                <select 
                    id="createUserId" 
                    name="user_id" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="">-- Pilih Penerima --</option>
                    @if($userRole !== 'superadmin')
                        @foreach($availableUsers as $availableUser)
                            <option value="{{ $availableUser->id }}">
                                {{ $availableUser->name }} ({{ $availableUser->nik ?? '-' }}) - {{ $availableUser->bidang->nama ?? '-' }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <p class="error-message text-red-500 text-sm mt-1" id="error-create-user_id"></p>
            </div>

            <!-- Jenis -->
            <div>
                <label for="createJenis" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Dana Sosial <span class="text-red-500">*</span>
                </label>
                <select 
                    id="createJenis" 
                    name="jenis" 
                    required
                    onchange="onJenisChange()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="">-- Pilih Jenis --</option>
                    <option value="rawat_inap">Rawat Inap (Rp 300.000)</option>
                    <option value="duka_cita">Duka Cita (Input Manual)</option>
                    <option value="banjir">Banjir (Rp 200.000)</option>
                </select>
                <p class="error-message text-red-500 text-sm mt-1" id="error-create-jenis"></p>
            </div>

            <!-- Nominal (hidden by default, shown based on jenis) -->
            <div id="nominalWrapper" class="hidden">
                <label for="createNominal" class="block text-sm font-medium text-gray-700 mb-2">
                    Nominal <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">Rp</span>
                    <input 
                        type="number" 
                        id="createNominal" 
                        name="nominal"
                        min="0"
                        step="1000"
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                        placeholder="Masukkan nominal"
                    >
                </div>
                <p class="error-message text-red-500 text-sm mt-1" id="error-create-nominal"></p>
            </div>

            <!-- Evident (Optional) -->
            <div>
                <label for="createEvident" class="block text-sm font-medium text-gray-700 mb-2">
                    Evident (Opsional)
                </label>
                <input 
                    type="file" 
                    id="createEvident" 
                    name="evident"
                    accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800"
                >
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF. Maksimal 5MB</p>
                <p class="error-message text-red-500 text-sm mt-1" id="error-create-evident"></p>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeCreateModal()" 
                    class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 px-6 py-3 bg-black text-white rounded-lg font-semibold hover:bg-gray-800 transition">
                    Ajukan
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