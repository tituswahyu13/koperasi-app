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

                    {{-- Menampilkan pesan error --}}
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <form action="{{ route('simpanan.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="anggota_id" class="block text-gray-700">Pilih Anggota</label>
                            <select name="anggota_id" id="anggota_id" class="w-full border-gray-300 rounded-md">
                                @foreach ($anggotas as $anggota)
                                <option value="{{ $anggota->id }}" {{ old('anggota_id') == $anggota->id ? 'selected' : '' }}>{{ $anggota->nama_lengkap }} ({{ $anggota->user->username ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            @error('anggota_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="jumlah_simpanan" class="block text-gray-700">Jumlah Simpanan (Rp)</label>
                            <input type="number" name="jumlah_simpanan" id="jumlah_simpanan" class="w-full border-gray-300 rounded-md" value="{{ old('jumlah_simpanan') }}" required min="1">
                            @error('jumlah_simpanan') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="jenis_simpanan" class="block text-gray-700 font-bold mb-2">Jenis Simpanan</label>
                            <select name="jenis_simpanan" id="jenis_simpanan" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="mandiri" {{ old('jenis_simpanan') == 'mandiri' ? 'selected' : '' }}>Simpanan Mandiri</option>
                                <option value="jasa_anggota" {{ old('jenis_simpanan') == 'jasa_anggota' ? 'selected' : '' }}>Simpanan Jasa Anggota</option>
                            </select>
                            @error('jenis_simpanan') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tanggal_simpanan" class="block text-gray-700">Tanggal Simpanan</label>
                            <input type="date" name="tanggal_simpanan" id="tanggal_simpanan" class="w-full border-gray-300 rounded-md" value="{{ old('tanggal_simpanan', date('Y-m-d')) }}" required>
                            @error('tanggal_simpanan') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="deskripsi" class="block text-gray-700">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" id="deskripsi" class="w-full border-gray-300 rounded-md">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow transition">Simpan Transaksi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>