{{-- resources/views/simpanan/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Simpanan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Catat Simpanan Baru</h1>

                    <form action="{{ route('simpanan.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="anggota_id" class="block text-gray-700">Pilih Anggota</label>
                            <select name="anggota_id" id="anggota_id" class="w-full border-gray-300 rounded-md">
                                @foreach ($anggotas as $anggota)
                                <option value="{{ $anggota->id }}">{{ $anggota->nama_lengkap }}</option>
                                @endforeach
                            </select>
                            @error('anggota_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="jumlah_simpanan" class="block text-gray-700">Jumlah Simpanan</label>
                            <input type="number" name="jumlah_simpanan" id="jumlah_simpanan" class="w-full border-gray-300 rounded-md" value="{{ old('jumlah_simpanan') }}" required min="0">
                            @error('jumlah_simpanan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="jenis_simpanan" class="block text-gray-700 font-bold mb-2">Jenis Simpanan</label>
                            <select name="jenis_simpanan" id="jenis_simpanan" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Jenis Simpanan</option>
                                <option value="pokok">Simpanan Pokok</option>
                                <option value="wajib">Simpanan Wajib</option>
                                <option value="wajib_khusus">Simpanan Wajib Khusus</option>
                                <option value="wajib_pinjam">Simpanan Wajib Pinjam</option>
                                <option value="manasuka">Simpanan Manasuka</option>
                                <option value="mandiri">Simpanan Mandiri</option>
                                <option value="voucher">Voucher</option>
                            </select>
                            @error('jenis_simpanan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="deskripsi" class="block text-gray-700">Deskripsi (Opsional)</label>
                            <input type="text" name="deskripsi" id="deskripsi" class="w-full border-gray-300 rounded-md" value="{{ old('deskripsi') }}">
                            @error('deskripsi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="tanggal_simpanan" class="block text-gray-700">Tanggal Simpanan</label>
                            <input type="date" name="tanggal_simpanan" id="tanggal_simpanan" class="w-full border-gray-300 rounded-md" value="{{ old('tanggal_simpanan') }}" required>
                            @error('tanggal_simpanan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>