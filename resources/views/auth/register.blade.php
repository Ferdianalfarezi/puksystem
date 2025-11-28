<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Daftar ke PUKsystem
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Buat akun baru untuk mengakses sistem
                </p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="space-y-4">
                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700 font-medium" />
                        <x-text-input id="name" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black" 
                                      type="text" 
                                      name="name" 
                                      :value="old('name')" 
                                      required 
                                      autofocus 
                                      autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Username -->
                    <div>
                        <x-input-label for="username" :value="__('Username')" class="text-gray-700 font-medium" />
                        <x-text-input id="username" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black" 
                                      type="text" 
                                      name="username" 
                                      :value="old('username')" 
                                      required 
                                      autocomplete="username" />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div>
                        <x-input-label for="role_id" :value="__('Role')" class="text-gray-700 font-medium" />
                        <select id="role_id" 
                                name="role_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black" 
                                required>
                            <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->nama }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                    </div>

                    <!-- Bidang -->
                    <div>
                        <x-input-label for="bidang_id" :value="__('Bidang')" class="text-gray-700 font-medium" />
                        <select id="bidang_id" 
                                name="bidang_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black" 
                                required>
                            <option value="">Pilih Bidang</option>
                            @foreach($bidangs as $bidang)
                                <option value="{{ $bidang->id }}" {{ old('bidang_id') == $bidang->id ? 'selected' : '' }}>
                                    {{ $bidang->nama }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('bidang_id')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
                        <x-text-input id="password" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                      type="password"
                                      name="password"
                                      required 
                                      autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700 font-medium" />
                        <x-text-input id="password_confirmation" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                      type="password"
                                      name="password_confirmation" 
                                      required 
                                      autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition duration-150 ease-in-out">
                        {{ __('Register') }}
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-medium text-black hover:text-gray-700">
                            Login disini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>