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
                    <h1 class="text-2xl font-bold mb-4">Formulir Tambah Anggota</h1>

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('anggota.store') }}" method="POST">
                        @csrf
                        
                        {{-- Bagian Data Login & Pribadi --}}
                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-2">1. Akun & Data Pribadi</h2>
                        
                        <div class="mb-4">
                            <label for="username" class="block text-gray-700">Username</label>
                            <input type="text" name="username" id="username" class="w-full border-gray-300 rounded-md" value="{{ old('username') }}" required>
                            @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-gray-700">Password</label>
                            <input type="password" name="password" id="password" class="w-full border-gray-300 rounded-md" required minlength="8">
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="nama_lengkap" class="block text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="w-full border-gray-300 rounded-md" value="{{ old('nama_lengkap') }}" required>
                            @error('nama_lengkap')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="alamat" class="block text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" class="w-full border-gray-300 rounded-md">{{ old('alamat') }}</textarea>
                            @error('alamat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="no_hp" class="block text-gray-700">Nomor HP</label>
                            <input type="text" name="no_hp" id="no_hp" class="w-full border-gray-300 rounded-md" value="{{ old('no_hp') }}">
                            @error('no_hp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>


                        {{-- Bagian Iuran Bulanan --}}
                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-2">2. Iuran Simpanan Bulanan</h2>
                        
                        <div class="mb-4">
                            <label for="simpanan_wajib_per_bulan" class="block text-gray-700">Iuran Simpanan Wajib (Per Bulan)</label>
                            <input type="number" name="simpanan_wajib_per_bulan" id="simpanan_wajib_per_bulan" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_wajib_per_bulan', 0) }}" required min="0">
                            @error('simpanan_wajib_per_bulan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Simpanan Wajib Khusus sekarang adalah IURAN BULANAN --}}
                        <div class="mb-4">
                            <label for="simpanan_wajib_khusus_per_bulan" class="block text-gray-700">Iuran Simpanan Wajib Khusus (Per Bulan)</label>
                            <input type="number" name="simpanan_wajib_khusus_per_bulan" id="simpanan_wajib_khusus_per_bulan" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_wajib_khusus_per_bulan', 0) }}" required min="0">
                            @error('simpanan_wajib_khusus_per_bulan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="simpanan_manasuka_per_bulan" class="block text-gray-700">Iuran Simpanan Manasuka (Per Bulan)</label>
                            <input type="number" name="simpanan_manasuka_per_bulan" id="simpanan_manasuka_per_bulan" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_manasuka_per_bulan', 0) }}" required min="0">
                            @error('simpanan_manasuka_per_bulan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="voucher_awal" class="block text-gray-700">Voucher</label>
                            <input type="number" name="voucher_awal" id="voucher_awal" class="w-full border-gray-300 rounded-md" value="{{ old('voucher_awal', 0) }}" min="0">
                            @error('voucher_awal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>


                        {{-- Bagian Pembayaran Awal --}}
                        <h2 class="text-xl font-semibold mt-6 mb-3 border-b pb-2">3. Pembayaran Awal (Simpanan Pokok & Voucher)</h2>
                        
                        <div class="mb-4">
                            <label for="simpanan_pokok_awal" class="block text-gray-700">Simpanan Pokok Awal (Dibayar sekali di awal)</label>
                            <input type="number" name="simpanan_pokok_awal" id="simpanan_pokok_awal" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_pokok_awal', 0) }}" min="0">
                            @error('simpanan_pokok_awal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mt-6">Tambah Anggota</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>