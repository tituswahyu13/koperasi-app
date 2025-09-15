{{-- resources/views/simpanan/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Simpanan') }}
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

                    <a href="{{ route('simpanan.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md shadow-sm transition">Catat Simpanan Baru</a>

                    <table class="min-w-full bg-white border mt-4">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Tanggal</th>
                                <th class="py-2 px-4 border-b">Nama Anggota</th>
                                <th class="py-2 px-4 border-b">Jumlah Simpanan</th>
                                <th class="py-2 px-4 border-b">Jenis</th>
                                <th class="py-2 px-4 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($simpanans as $simpanan)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $simpanan->tanggal_simpanan }}</td>
                                <td class="py-2 px-4 border-b">{{ $simpanan->anggota->nama_lengkap }}</td>
                                <td class="py-2 px-4 border-b">Rp {{ number_format($simpanan->jumlah_simpanan, 2, ',', '.') }}</td>
                                <td class="py-2 px-4 border-b">{{ $simpanan->jenis_simpanan }}</td>
                                <td class="py-2 px-4 border-b">
                                    <a href="{{ route('simpanan.edit', $simpanan->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                    <form action="{{ route('simpanan.destroy', $simpanan->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $simpanans->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>