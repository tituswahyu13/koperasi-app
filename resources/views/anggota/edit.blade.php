{{-- resources/views/anggota/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Anggota: ') . $anggota->nama_lengkap }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Form Edit Anggota</h1>

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

                    <form action="{{ route('anggota.update', $anggota->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-1">Data Akun & Profil</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="username" class="block text-gray-700">Username (Non-Editable)</label>
                                <input type="text" id="username" class="w-full border-gray-300 rounded-md bg-gray-100" value="{{ $anggota->user->username }}" disabled>
                                <p class="text-sm text-gray-500 mt-1">Username harus diubah melalui halaman Manajemen Pengguna jika diperlukan.</p>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="block text-gray-700">Password (Isi jika ingin diubah)</label>
                                {{-- Password field dikosongkan secara default untuk keamanan --}}
                                <input type="password" name="password" id="password" class="w-full border-gray-300 rounded-md">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                <p class="text-sm text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah password.</p>
                            </div>

                            <div class="mb-4">
                                <label for="status_aktif" class="block text-gray-700">Status Keanggotaan</label>
                                <select name="status_aktif" id="status_aktif" class="w-full border-gray-300 rounded-md" required>
                                    <option value="1" {{ old('status_aktif', $anggota->status_aktif) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status_aktif', $anggota->status_aktif) == 0 ? 'selected' : '' }}>Non-Aktif</option>
                                </select>
                                <x-input-error :messages="$errors->get('status_aktif')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nama_lengkap" class="block text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="w-full border-gray-300 rounded-md" value="{{ old('nama_lengkap', $anggota->nama_lengkap) }}" required>
                            <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="alamat" class="block text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" class="w-full border-gray-300 rounded-md">{{ old('alamat', $anggota->alamat) }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="no_hp" class="block text-gray-700">Nomor HP</label>
                            <input type="text" name="no_hp" id="no_hp" class="w-full border-gray-300 rounded-md" value="{{ old('no_hp', $anggota->no_hp) }}">
                            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                        </div>

                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-1">Iuran Wajib (per Bulan)</h2>
                        <p class="text-sm text-gray-500 mb-4">Nilai ini menentukan jumlah iuran yang akan dibebankan setiap bulan.</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Simpanan Wajib (Iuran Bulanan) --}}
                            <div class="mb-4">
                                <label for="simpanan_wajib" class="block text-gray-700">Simpanan Wajib (Iuran)</label>
                                <input type="number" name="simpanan_wajib" id="simpanan_wajib" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_wajib', $anggota->simpanan_wajib) }}" required min="0">
                                <x-input-error :messages="$errors->get('simpanan_wajib')" class="mt-2" />
                            </div>

                            {{-- Simpanan Wajib Khusus (Iuran Bulanan) --}}
                            <div class="mb-4">
                                <label for="simpanan_wajib_khusus" class="block text-gray-700">Simpanan Wajib Khusus (Iuran)</label>
                                <input type="number" name="simpanan_wajib_khusus" id="simpanan_wajib_khusus" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_wajib_khusus', $anggota->simpanan_wajib_khusus) }}" required min="0">
                                <x-input-error :messages="$errors->get('simpanan_wajib_khusus')" class="mt-2" />
                            </div>

                            {{-- Simpanan Manasuka (Iuran Bulanan) --}}
                            <div class="mb-4">
                                <label for="simpanan_manasuka" class="block text-gray-700">Simpanan Manasuka (Iuran)</label>
                                <input type="number" name="simpanan_manasuka" id="simpanan_manasuka" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_manasuka', $anggota->simpanan_manasuka) }}" required min="0">
                                <x-input-error :messages="$errors->get('simpanan_manasuka')" class="mt-2" />
                            </div>

                            {{-- Voucher (Iuran Bulanan) --}}
                            <div class="mb-4">
                                <label for="voucher" class="block text-gray-700">Voucher</label>
                                <input type="number" name="voucher" id="voucher" class="w-full border-gray-300 rounded-md" value="{{ old('voucher', $anggota->voucher) }}" required min="0">
                                <x-input-error :messages="$errors->get('voucher')" class="mt-2" />
                            </div>

                            {{-- Saldo Mandiri --}}
                            <div class="mb-4">
                                <label for="saldo_mandiri" class="block text-gray-700">Saldo Mandiri (Non-Editable)</label>
                                <input type="number" id="saldo_mandiri" class="w-full border-gray-300 rounded-md bg-gray-100" value="{{ $anggota->saldo_mandiri }}" disabled>
                                <p class="text-sm text-gray-500 mt-1">Saldo mandiri hanya dapat diubah melalui transaksi simpanan/penarikan.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button type="submit" class="bg-indigo-600 hover:bg-indigo-700 transition ease-in-out duration-150">
                                {{ __('Perbarui Data Anggota') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>