{{-- resources/views/anggota/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Anggota') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Edit Anggota: {{ $anggota->nama_lengkap }}</h1>

                    {{-- Tampilkan error global jika ada --}}
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    
                    <form action="{{ route('anggota.update', $anggota) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h2 class="text-xl font-semibold mt-4 mb-3 border-b pb-2">1. Akun & Hak Akses</h2>
                        
                        {{-- Username (Hanya tampil, tidak bisa diubah) --}}
                        <div class="mb-4">
                            <label class="block text-gray-700">Username</label>
                            <p class="w-full bg-gray-200 p-2 rounded-md">{{ $anggota->user->username ?? 'Tidak ada username' }}</p>
                        </div>

                        {{-- Hak Akses (Role) --}}
                        <div class="mb-4">
                            <label for="role" class="block text-gray-700">Hak Akses</label>
                            <select name="role" id="role" class="w-full border-gray-300 rounded-md">
                                <option value="0" {{ ($anggota->user->role ?? 0) === 0 ? 'selected' : '' }}>Anggota</option>
                                <option value="1" {{ ($anggota->user->role ?? 0) === 1 ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-2">2. Data Pribadi</h2>

                        {{-- Nama Lengkap --}}
                        <div class="mb-4">
                            <label for="nama_lengkap" class="block text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="w-full border-gray-300 rounded-md" value="{{ old('nama_lengkap', $anggota->nama_lengkap) }}" required>
                            @error('nama_lengkap')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-4">
                            <label for="alamat" class="block text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" class="w-full border-gray-300 rounded-md">{{ old('alamat', $anggota->alamat) }}</textarea>
                            @error('alamat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Nomor HP --}}
                        <div class="mb-4">
                            <label for="no_hp" class="block text-gray-700">Nomor HP</label>
                            <input type="text" name="no_hp" id="no_hp" class="w-full border-gray-300 rounded-md" value="{{ old('no_hp', $anggota->no_hp) }}">
                            @error('no_hp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-2">3. Simpanan & Saldo</h2>
                        
                        {{-- Simpanan Pokok (TIDAK BISA DIUBAH) --}}
                        <div class="mb-4">
                            <label for="saldo_pokok" class="block text-gray-700">Simpanan Pokok (Saldo)</label>
                            <input type="number" id="saldo_pokok" class="w-full border-gray-300 rounded-md bg-gray-100" 
                                   value="{{ number_format($anggota->saldo_pokok ?? 0, 2, '.', '') }}" disabled>
                            <p class="text-sm text-gray-500 mt-1">Saldo Pokok hanya bisa berubah melalui transaksi Simpanan Baru.</p>
                        </div>
                        
                        {{-- Saldo Wajib Khusus (BISA DIUBAH) --}}
                        <div class="mb-4">
                            <label for="saldo_wajib_khusus" class="block text-gray-700">Saldo Wajib Khusus</label>
                            <input type="number" name="saldo_wajib_khusus" id="saldo_wajib_khusus" class="w-full border-gray-300 rounded-md" 
                                   value="{{ old('saldo_wajib_khusus', $anggota->saldo_wajib_khusus ?? 0) }}" required min="0">
                            @error('saldo_wajib_khusus')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Saldo Mandiri / Voucher (BISA DIUBAH) --}}
                        <div class="mb-4">
                            <label for="saldo_mandiri" class="block text-gray-700">Saldo Mandiri / Voucher</label>
                            <input type="number" name="saldo_mandiri" id="saldo_mandiri" class="w-full border-gray-300 rounded-md" 
                                   value="{{ old('saldo_mandiri', $anggota->saldo_mandiri ?? 0) }}" required min="0">
                            @error('saldo_mandiri')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Iuran Simpanan Wajib (BISA DIUBAH) --}}
                        <div class="mb-4">
                            <label for="simpanan_wajib" class="block text-gray-700">Iuran Simpanan Wajib (Per Bulan)</label>
                            <input type="number" name="simpanan_wajib" id="simpanan_wajib" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_wajib', $anggota->simpanan_wajib) }}" required min="0">
                            @error('simpanan_wajib')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Iuran Simpanan Manasuka (BISA DIUBAH) --}}
                        <div class="mb-4">
                            <label for="simpanan_manasuka" class="block text-gray-700">Iuran Simpanan Manasuka (Per Bulan)</label>
                            <input type="number" name="simpanan_manasuka" id="simpanan_manasuka" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_manasuka', $anggota->simpanan_manasuka) }}" required min="0">
                            @error('simpanan_manasuka')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Iuran Simpanan Wajib Khusus (BISA DIUBAH) --}}
                        <div class="mb-4">
                            <label for="simpanan_wajib_khusus" class="block text-gray-700">Iuran Simpanan Wajib Khusus (Per Bulan)</label>
                            <input type="number" name="simpanan_wajib_khusus" id="simpanan_wajib_khusus" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_wajib_khusus', $anggota->simpanan_wajib_khusus) }}" required min="0">
                            @error('simpanan_wajib_khusus')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mt-6">Perbarui Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>