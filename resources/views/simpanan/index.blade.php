{{-- resources/views/simpanan/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Transaksi Simpanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Daftar Transaksi Simpanan</h1>

                    @if (session('success'))
                    <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="bg-red-200 text-red-800 p-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="flex space-x-3 mb-6">
                        {{-- Tombol Catat Setoran --}}
                        <a href="{{ route('simpanan.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md shadow-sm transition">Catat Setoran Baru</a>
                        
                        {{-- Tombol Catat Penarikan Individu --}}
                        <a href="{{ route('simpanan.withdraw') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md shadow-sm transition">Catat Penarikan (Individu)</a>

                        {{-- TOMBOL BARU: Penarikan Massal --}}
                        <a href="{{ route('simpanan.mass_withdraw') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md shadow-sm transition">Penarikan Massal Manasuka</a>
                    </div>


                    <div class="overflow-x-auto mt-6 rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Nama Anggota</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Jenis Simpanan</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Jumlah (Rp)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Deskripsi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($simpanans as $simpanan)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ \Carbon\Carbon::parse($simpanan->tanggal_simpanan)->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">{{ $simpanan->anggota->nama_lengkap ?? 'Anggota Dihapus' }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">
                                        {{ ucwords(str_replace('_', ' ', $simpanan->jenis_simpanan)) }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold border-r text-right 
                                        @if ($simpanan->jumlah_simpanan < 0) 
                                            text-red-600 
                                        @else 
                                            text-green-600 
                                        @endif"
                                    >
                                        Rp {{ number_format(abs($simpanan->jumlah_simpanan), 2, ',', '.') }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ $simpanan->deskripsi ?? '-' }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                        {{-- Hanya izinkan edit/hapus untuk transaksi yang bukan otomatis --}}
                                        @if (in_array($simpanan->jenis_simpanan, ['mandiri', 'jasa_anggota', 'penarikan_manasuka', 'penarikan_mandiri', 'penarikan_jasa_anggota']))
                                            <a href="{{ route('simpanan.edit', $simpanan->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('simpanan.destroy', $simpanan->id) }}" method="POST" class="inline ml-4">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Saldo anggota akan dikoreksi.')">Hapus</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs">Otomatis</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $simpanans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>