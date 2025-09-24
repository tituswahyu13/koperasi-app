{{-- resources/views/pinjaman/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Pinjaman Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Form Pengajuan Pinjaman</h1>

                    <form action="{{ route('pinjaman.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="anggota_id" class="block text-gray-700 font-bold mb-2">Pilih Anggota</label>
                            <select name="anggota_id" id="anggota_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                @foreach ($anggotas as $anggota)
                                <option value="{{ $anggota->id }}">{{ $anggota->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="jenis_pinjaman" class="block text-gray-700 font-bold mb-2">Jenis Pinjaman</label>
                            <select name="jenis_pinjaman" id="jenis_pinjaman" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="uang">Uang (Bunga 1%)</option>
                                <option value="barang">Barang (Bunga 1.5%)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="tenor" class="block text-gray-700 font-bold mb-2">Tenor (Bulan)</label>
                            <input type="number" name="tenor" id="tenor" class="w-full border-gray-300 rounded-md shadow-sm" required min="1" max="60">
                        </div>

                        <div class="mb-4">
                            <label for="jumlah_pinjaman" class="block text-gray-700 font-bold mb-2">Jumlah Pinjaman</label>
                            <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" class="w-full border-gray-300 rounded-md shadow-sm" required min="100000">
                        </div>

                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md shadow-sm transition">Ajukan Pinjaman</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>