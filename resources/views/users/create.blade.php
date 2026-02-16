<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-900">Add User</h2>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="createForm" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <!-- Name -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="createName" name="name" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Enter full name">
                    <span class="text-red-500 text-sm error-message" id="error-create-name"></span>
                </div>

                <!-- NIK -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        NIK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="createNik" name="nik" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Enter NIK">
                    <p class="text-xs text-gray-500 mt-1">NIK akan digunakan untuk QR Code kehadiran</p>
                    <span class="text-red-500 text-sm error-message" id="error-create-nik"></span>
                </div>

                <!-- Username -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="createUsername" name="username" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Enter username">
                    <span class="text-red-500 text-sm error-message" id="error-create-username"></span>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select id="createRoleId" name="role_id" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nama }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-create-role_id"></span>
                </div>

                <!-- Bidang -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Bidang <span class="text-red-500">*</span>
                    </label>
                    <select id="createBidangId" name="bidang_id" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="">Select Bidang</option>
                        @foreach($bidangs as $bidang)
                            <option value="{{ $bidang->id }}">{{ $bidang->nama }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-create-bidang_id"></span>
                </div>

                <!-- Koorlap -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Koorlap (Koordinator Lapangan) <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <select id="createKoorlapId" name="koorlap_id"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="">Select Koorlap (Optional)</option>
                        @foreach($koorlaps as $koorlap)
                            <option value="{{ $koorlap->id }}">{{ $koorlap->nama }} ({{ $koorlap->user->name }})</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-create-koorlap_id"></span>
                </div>

                <!-- Departemen -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Departemen <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <input type="text" id="createDepartemen" name="departemen"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Enter departemen">
                    <span class="text-red-500 text-sm error-message" id="error-create-departemen"></span>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="createPassword" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Enter password">
                    <span class="text-red-500 text-sm error-message" id="error-create-password"></span>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="createPasswordConfirmation" name="password_confirmation" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Confirm password">
                    <span class="text-red-500 text-sm error-message" id="error-create-password_confirmation"></span>
                </div>

                <!-- Status -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="createStatus" name="status" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="active">Active</option>
                        <option value="not active">Not Active</option>
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-create-status"></span>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeCreateModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Save User
                </button>
            </div>
        </form>
    </div>
</div>