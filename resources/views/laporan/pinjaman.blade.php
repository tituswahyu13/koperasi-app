{{-- resources/views/laporan/pinjaman.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Pinjaman Detail') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6">Laporan Pinjaman Aktif</h1>
                    <p class="text-lg text-gray-600 mb-6">Menampilkan pinjaman yang masih memiliki sisa tagihan.</p>

                    <div class="overflow-x-auto mt-6 rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">No.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Anggota</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Jenis Pinjaman</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Pokok Pinjaman</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Angsuran Bulanan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Total Dibayar</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Sisa Pokok</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Sisa Bunga</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r font-extrabold">Total Sisa Tagihan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo Berikutnya</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($laporanPinjaman as $index => $data)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">{{ $data['anggota_nama'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 border-r">{{ $data['loan_type'] }} ({{ $data['tenor'] }} bln)</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['pokok_pinjaman'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['angsuran_per_bulan'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['total_dibayar'], 2, ',', '.') }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700 border-r text-right font-semibold">Rp {{ number_format($data['sisa_pokok'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700 border-r text-right font-semibold">Rp {{ number_format($data['sisa_bunga'], 2, ',', '.') }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-900 border-r text-right font-extrabold">Rp {{ number_format($data['sisa_tagihan'], 2, ',', '.') }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        @if ($data['next_due_date'])
                                            {{ \Carbon\Carbon::parse($data['next_due_date'])->format('d-m-Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-right text-base font-bold text-gray-900 border-r">GRAND TOTAL SISA PINJAMAN</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-red-700 border-r">Rp {{ number_format($grandTotalSisa, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>