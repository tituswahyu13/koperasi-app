{{-- resources/views/anggota/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Anggota Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Form Tambah Anggota</h1>

                    {{-- Menampilkan pesan error jika ada --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Menampilkan pesan sukses/error dari Controller --}}
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('anggota.store') }}" method="POST">
                        @csrf

                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-1">Data Akun & Profil</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="username" class="block text-gray-700">Username</label>
                                <input type="text" name="username" id="username" class="w-full border-gray-300 rounded-md" value="{{ old('username') }}" required>
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <label for="password" class="block text-gray-700">Password</label>
                                <input type="password" name="password" id="password" class="w-full border-gray-300 rounded-md" required>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nama_lengkap" class="block text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="w-full border-gray-300 rounded-md" value="{{ old('nama_lengkap') }}" required>
                            <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="alamat" class="block text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" class="w-full border-gray-300 rounded-md">{{ old('alamat') }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="no_hp" class="block text-gray-700">Nomor HP</label>
                            <input type="text" name="no_hp" id="no_hp" class="w-full border-gray-300 rounded-md" value="{{ old('no_hp') }}">
                            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                        </div>

                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-1">Saldo Awal (Dana Masuk Saat Pendaftaran)</h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Simpanan Pokok (Masuk ke saldo_pokok) --}}
                            <div class="mb-4">
                                <label for="simpanan_pokok" class="block text-gray-700">Simpanan Pokok </label>
                                <input type="number" name="simpanan_pokok" id="simpanan_pokok" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_pokok', 0) }}" required min="0">
                                <x-input-error :messages="$errors->get('simpanan_pokok')" class="mt-2" />
                                <!-- <p class="text-sm text-gray-500 mt-1">Nilai ini akan dicatat di `saldo_pokok`.</p> -->
                            </div>
                        </div>

                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-1">Iuran/Simpanan (per Bulan)</h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Simpanan Wajib (Iuran Bulanan) --}}
                            <div class="mb-4">
                                <label for="simpanan_wajib" class="block text-gray-700">Simpanan Wajib</label>
                                <input type="number" name="simpanan_wajib" id="simpanan_wajib" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_wajib', 0) }}" required min="0">
                                <x-input-error :messages="$errors->get('simpanan_wajib')" class="mt-2" />
                            </div>

                            {{-- Simpanan Wajib Khusus (Iuran Bulanan) --}}
                            <div class="mb-4">
                                <label for="simpanan_wajib_khusus" class="block text-gray-700">Simpanan Wajib Khusus</label>
                                <input type="number" name="simpanan_wajib_khusus" id="simpanan_wajib_khusus" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_wajib_khusus', 0) }}" required min="0">
                                <x-input-error :messages="$errors->get('simpanan_wajib_khusus')" class="mt-2" />
                            </div>

                            {{-- Simpanan Manasuka (Iuran Bulanan) --}}
                            <div class="mb-4">
                                <label for="simpanan_manasuka" class="block text-gray-700">Simpanan Manasuka</label>
                                <input type="number" name="simpanan_manasuka" id="simpanan_manasuka" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_manasuka', 0) }}" required min="0">
                                <x-input-error :messages="$errors->get('simpanan_manasuka')" class="mt-2" />
                            </div>

                            {{-- Voucher (Masuk ke kolom voucher) --}}
                            <div class="mb-4">
                                <label for="voucher" class="block text-gray-700">Voucher</label>
                                <input type="number" name="voucher" id="voucher" class="w-full border-gray-300 rounded-md" value="{{ old('voucher', 0) }}" required min="0">
                                <x-input-error :messages="$errors->get('voucher')" class="mt-2" />
                                <!-- <p class="text-sm text-gray-500 mt-1">Nilai ini akan dicatat di `voucher`.</p> -->
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button type="submit" class="bg-indigo-600 hover:bg-indigo-700 transition ease-in-out duration-150">
                                {{ __('Simpan Anggota') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>