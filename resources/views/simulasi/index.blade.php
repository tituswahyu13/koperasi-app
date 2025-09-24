{{-- resources/views/simulasi/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Simulasi Pinjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Form Simulasi</h1>

                    <form action="{{ route('simulasi.calculate') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="jumlah_pinjaman" class="block text-gray-700">Jumlah Pinjaman</label>
                            <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" class="w-full border-gray-300 rounded-md" value="{{ old('jumlah_pinjaman', $jumlahPinjaman ?? '') }}" required min="100000">
                        </div>

                        <div>
                            <label for="jenis_pinjaman" class="block text-gray-700">Jenis Pinjaman</label>
                            <select name="jenis_pinjaman" id="jenis_pinjaman" class="w-full border-gray-300 rounded-md">
                                <option value="uang" {{ ($jenisPinjaman ?? '') === 'uang' ? 'selected' : '' }}>Uang (Bunga 1% per bulan)</option>
                                <option value="barang" {{ ($jenisPinjaman ?? '') === 'barang' ? 'selected' : '' }}>Barang (Bunga 1.5% per bulan)</option>
                            </select>
                        </div>

                        <div>
                            <label for="tenor" class="block text-gray-700">Tenor (Bulan)</label>
                            <input type="number" name="tenor" id="tenor" class="w-full border-gray-300 rounded-md" value="{{ old('tenor', $tenor ?? '') }}" required min="1" max="60">
                        </div>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Hitung Simulasi</button>
                    </form>

                    @isset($jadwalAngsuran)
                    <div class="mt-8">
                        <h2 class="text-xl font-bold mb-4">Tabel Angsuran</h2>
                        <div class="overflow-x-auto rounded-lg border">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Bulan ke-</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Tanggal Jatuh Tempo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Cicilan Pokok</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Bunga Angsuran</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Total Angsuran</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Pokok</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($jadwalAngsuran as $jadwal)
                                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">{{ $jadwal['bulan'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ $jadwal['tanggal_jatuh_tempo'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">Rp {{ number_format($jadwal['cicilan_pokok'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">Rp {{ number_format($jadwal['bunga_angsuran'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">Rp {{ number_format($jadwal['cicilan_total'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($jadwal['sisa_pokok'], 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endisset
                </div>
            </div>
        </div>
    </div>
</x-app-layout>