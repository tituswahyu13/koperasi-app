{{-- resources/views/pinjaman/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pinjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Detail Pinjaman untuk: {{ $pinjaman->anggota->nama_lengkap }}</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p><strong>Jumlah Pinjaman:</strong> Rp {{ number_format($pinjaman->jumlah_pinjaman, 2, ',', '.') }}</p>
                            <p><strong>Bunga:</strong> Rp {{ number_format($pinjaman->bunga, 2, ',', '.') }}</p>
                            <p><strong>Total Pinjaman:</strong> Rp {{ number_format($pinjaman->jumlah_pinjaman + $pinjaman->bunga, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p><strong>Tenor:</strong> {{ $pinjaman->tenor }} bulan</p>
                            <p><strong>Sudah Dibayar:</strong> Rp {{ number_format($pinjaman->jumlah_bayar, 2, ',', '.') }}</p>
                            <p><strong>Sisa Pinjaman:</strong> <span class="font-bold text-lg text-red-600">Rp {{ number_format($pinjaman->sisa_pinjaman, 2, ',', '.') }}</span></p>
                        </div>
                    </div>

                    <h2 class="text-xl font-bold mt-8 mb-4">Riwayat Pembayaran Cicilan</h2>

                    <div class="overflow-x-auto rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Jumlah Pembayaran</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($cicilans as $cicilan)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ $cicilan->tanggal_simpanan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">Rp {{ number_format($cicilan->jumlah_simpanan, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cicilan->deskripsi }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>