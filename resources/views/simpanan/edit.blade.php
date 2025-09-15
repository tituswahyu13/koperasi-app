{{-- resources/views/simpanan/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaksi Simpanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Edit Transaksi Simpanan</h1>

                    <form action="{{ route('simpanan.update', $simpanan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="anggota_id" class="block text-gray-700">Pilih Anggota</label>
                            <select name="anggota_id" id="anggota_id" class="w-full border-gray-300 rounded-md">
                                @foreach ($anggotas as $anggota)
                                    <option value="{{ $anggota->id }}" {{ $anggota->id == $simpanan->anggota_id ? 'selected' : '' }}>
                                        {{ $anggota->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="jumlah_simpanan" class="block text-gray-700">Jumlah Simpanan</label>
                            <input type="number" name="jumlah_simpanan" id="jumlah_simpanan" class="w-full border-gray-300 rounded-md" value="{{ old('jumlah_simpanan', $simpanan->jumlah_simpanan) }}" required min="0">
                        </div>

                        <div class="mb-4">
                            <label for="jenis_simpanan" class="block text-gray-700">Jenis Simpanan</label>
                            <select name="jenis_simpanan" id="jenis_simpanan" class="w-full border-gray-300 rounded-md">
                                <option value="harian" {{ $simpanan->jenis_simpanan == 'harian' ? 'selected' : '' }}>Harian</option>
                                <option value="bulanan" {{ $simpanan->jenis_simpanan == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="tanggal_simpanan" class="block text-gray-700">Tanggal Simpanan</label>
                            <input type="date" name="tanggal_simpanan" id="tanggal_simpanan" class="w-full border-gray-300 rounded-md" value="{{ old('tanggal_simpanan', $simpanan->tanggal_simpanan) }}" required>
                        </div>

                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Perbarui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>