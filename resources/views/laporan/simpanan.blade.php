{{-- resources/views/laporan/simpanan.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Saldo Simpanan per Anggota') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6">Laporan Saldo Simpanan</h1>
                    <p class="text-lg text-gray-600 mb-6">Per Tanggal: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>

                    <div class="overflow-x-auto mt-6 rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-b">No.</th>
                                    <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-b">Nama Anggota</th>
                                    <th colspan="7" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-b">Saldo Simpanan</th>
                                    <th rowspan="2" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Total Saldo</th>
                                </tr>
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Pokok</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Wajib</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Wajib Khusus</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Manasuka</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Mandiri</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Jasa Anggota</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Voucher</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($laporanSimpanan as $index => $data)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">
                                        {{ $data['nama_lengkap'] }}
                                        @if ($data['status_aktif'] != 1)
                                            <span class="text-xs text-red-500 ml-2">(Non-Aktif)</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['saldo_pokok'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['saldo_wajib'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['saldo_wajib_khusus'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['saldo_manasuka'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['saldo_mandiri'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['saldo_jasa_anggota'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($data['voucher'], 2, ',', '.') }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-indigo-700 text-right">Rp {{ number_format($data['total_saldo'], 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-right text-base font-bold text-gray-900 border-r">GRAND TOTAL</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-gray-900 border-r">Rp {{ number_format($grandTotals['saldo_pokok'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-gray-900 border-r">Rp {{ number_format($grandTotals['saldo_wajib'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-gray-900 border-r">Rp {{ number_format($grandTotals['saldo_wajib_khusus'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-gray-900 border-r">Rp {{ number_format($grandTotals['saldo_manasuka'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-gray-900 border-r">Rp {{ number_format($grandTotals['saldo_mandiri'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-gray-900 border-r">Rp {{ number_format($grandTotals['saldo_jasa_anggota'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-gray-900 border-r">Rp {{ number_format($grandTotals['voucher'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-indigo-700">Rp {{ number_format($grandTotals['total_saldo'], 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>