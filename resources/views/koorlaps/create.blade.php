<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-250">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-250 scale-95 opacity-0 modal-content">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Add New Koorlap</h3>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="createForm">
            @csrf
            <div class="p-6 space-y-4">
                <!-- Nama -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Koorlap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                        placeholder="Enter koorlap name"
                    >
                    <p class="text-xs text-red-600 mt-1 error-message" id="error-create-nama"></p>
                </div>

                <!-- User -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        User Account <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="user_id" 
                        id="createUserId"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    >
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-red-600 mt-1 error-message" id="error-create-user_id"></p>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button 
                    type="button" 
                    onclick="closeCreateModal()"
                    class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2.5 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Add Koorlap</span>
                </button>
            </div>
        </form>
    </div>
</div>