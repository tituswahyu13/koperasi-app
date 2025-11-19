{{-- resources/views/laporan/arus-kas.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Arus Kas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-4">Laporan Arus Kas</h1>
                    <p class="text-lg text-gray-600 mb-6">Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>

                    {{-- Form Filter Tanggal --}}
                    <form method="GET" action="{{ route('laporan.arus-kas') }}" class="mb-6 flex space-x-4 p-4 border rounded-lg bg-gray-50">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ \Carbon\Carbon::parse($start_date)->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" value="{{ \Carbon\Carbon::parse($end_date)->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm">Filter</button>
                        </div>
                    </form>

                    <div class="overflow-x-auto mt-6 rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Transaksi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pemasukan (Kredit)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pengeluaran (Debit)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Kas</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $saldoKas = 0;
                                @endphp
                                @foreach ($allTransactions as $transaksi)
                                    @php
                                        $masuk = $transaksi['jenis'] === 'Pemasukan' ? $transaksi['jumlah'] : 0;
                                        $keluar = $transaksi['jenis'] === 'Pengeluaran' ? $transaksi['jumlah'] : 0;
                                        $saldoKas += $masuk - $keluar;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($transaksi['tanggal'])->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaksi['jenis'] === 'Pemasukan' ? 'text-green-600' : 'text-red-600' }}">{{ $transaksi['jenis'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaksi['kategori'] }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $transaksi['deskripsi'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-semibold">Rp {{ number_format($masuk, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-semibold">Rp {{ number_format($keluar, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-extrabold text-gray-900">Rp {{ number_format($saldoKas, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-right text-base font-bold text-gray-900">SALDO AKHIR KAS</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-indigo-700">Rp {{ number_format($saldoKas, 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>