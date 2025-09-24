{{-- resources/views/pinjaman/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pinjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Daftar Pengajuan Pinjaman</h1>

                    @if (session('success'))
                    <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                    @endif

                    <a href="{{ route('pinjaman.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm transition">Ajukan Pinjaman Baru</a>

                    <div class="overflow-x-auto mt-6 rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Nama Anggota</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Tanggal Pengajuan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Jumlah Pinjaman</th>
                                    <th class="px-6 py-3 ... border-r">Jenis Pinjaman</th>
                                    <th class="px-6 py-3 ... border-r">Tenor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($pinjamans as $pinjaman)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">{{ $pinjaman->anggota->nama_lengkap }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ $pinjaman->tanggal_pengajuan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">Rp {{ number_format($pinjaman->jumlah_pinjaman, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 ... border-r">{{ ucfirst($pinjaman->jenis_pinjaman) }}</td>
                                    <td class="px-6 py-4 ... border-r">{{ $pinjaman->tenor }} bulan</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $pinjaman->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($pinjaman->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($pinjaman->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                        @if ($pinjaman->status == 'pending')
                                        <form action="{{ route('pinjaman.update', $pinjaman->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="text-green-600 hover:text-green-900">Setujui</button>
                                        </form>
                                        <form action="{{ route('pinjaman.update', $pinjaman->id) }}" method="POST" class="inline ml-4">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Tolak</button>
                                        </form>
                                        @else
                                        <a href="{{ route('pinjaman.show', $pinjaman->id) }}" class="text-blue-600 hover:text-blue-900">Lihat Detail</a>
                                        @endif
                                    </td>ß
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pinjamans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>