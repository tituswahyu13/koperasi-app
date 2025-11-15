{{-- resources/views/pinjaman/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pinjaman') . ' #' . $pinjaman->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6 border-b pb-2">Rincian Pinjaman</h1>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Kartu Status Pinjaman --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow-md col-span-1">
                            <h3 class="text-lg font-semibold mb-3 border-b pb-2">Status</h3>
                            @php
                                $statusClass = [
                                    'pending' => 'bg-yellow-200 text-yellow-800',
                                    'approved' => 'bg-green-200 text-green-800',
                                    'rejected' => 'bg-red-200 text-red-800',
                                ][$pinjaman->status] ?? 'bg-gray-200 text-gray-800';
                            @endphp
                            <p class="text-center font-bold text-2xl py-4 rounded-lg {{ $statusClass }}">
                                {{ ucwords($pinjaman->status) }}
                            </p>
                            @if ($pinjaman->status == 'approved')
                                <p class="text-sm text-gray-600 mt-2">Jatuh Tempo Cicilan Pertama: <span class="font-semibold">{{ \Carbon\Carbon::parse($pinjaman->tanggal_jatuh_tempo)->format('d-m-Y') }}</span></p>
                            @endif
                        </div>

                        {{-- Kartu Anggota dan Jenis Pinjaman --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow-md col-span-2">
                            <h3 class="text-lg font-semibold mb-3 border-b pb-2">Informasi Dasar</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <p><span class="font-medium">Anggota:</span> {{ $pinjaman->anggota->nama_lengkap ?? 'N/A' }}</p>
                                <p><span class="font-medium">Tgl. Pengajuan:</span> {{ \Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d-m-Y') }}</p>
                                <p><span class="font-medium">Jenis Pinjaman:</span> {{ ucwords($pinjaman->config['label'] ?? $pinjaman->loan_type) }}</p>
                                <p><span class="font-medium">Tenor:</span> {{ $pinjaman->tenor }} Bulan</p>
                                <p class="col-span-2"><span class="font-medium">Metode Bayar:</span> {{ ucwords(str_replace('tgl_', 'Tanggal ', $pinjaman->payment_date_type)) }}</p>
                                <p class="col-span-2"><span class="font-medium">Deskripsi:</span> {{ $pinjaman->deskripsi ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Detail Perhitungan Keuangan --}}
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold mb-4 border-b pb-2">Perhitungan Keuangan</h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center font-bold">
                            <div class="p-4 bg-indigo-50 rounded-lg shadow">
                                <p class="text-sm text-indigo-700">Pokok Pinjaman</p>
                                <p class="text-xl">Rp {{ number_format($pinjaman->jumlah_pinjaman, 2, ',', '.') }}</p>
                            </div>
                            <div class="p-4 bg-red-50 rounded-lg shadow">
                                <p class="text-sm text-red-700">Bunga Total ({{ $pinjaman->config['bunga'] * 100 ?? 0 }}%)</p>
                                <p class="text-xl">Rp {{ number_format($pinjaman->bunga, 2, ',', '.') }}</p>
                            </div>
                            <div class="p-4 bg-red-50 rounded-lg shadow">
                                <p class="text-sm text-red-700">Biaya Administrasi ({{ $pinjaman->config['admin'] * 100 ?? 0 }}%)</p>
                                <p class="text-xl">Rp {{ number_format($pinjaman->biaya_admin, 2, ',', '.') }}</p>
                            </div>
                            <div class="p-4 bg-green-100 rounded-lg shadow">
                                <p class="text-sm text-green-700">Total Tagihan Pokok + Bunga</p>
                                <p class="text-xl">Rp {{ number_format($pinjaman->total_tagihan, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-blue-100 rounded-lg shadow text-center font-bold">
                            <p class="text-lg text-blue-700">Angsuran Pokok + Bunga Per Bulan</p>
                            <p class="text-3xl">Rp {{ number_format($pinjaman->angsuran_per_bulan, 2, ',', '.') }}</p>
                        </div>

                        <div class="mt-4 p-4 border border-red-300 rounded-lg shadow text-center font-bold bg-white">
                            <p class="text-lg text-red-700">Sisa Pinjaman Bersih</p>
                            <p class="text-3xl text-red-900">Rp {{ number_format($pinjaman->sisa_pinjaman, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Riwayat Cicilan --}}
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold mb-4 border-b pb-2">Riwayat Pembayaran Cicilan</h2>
                        
                        @if ($cicilans->isEmpty())
                            <p class="text-gray-600">Belum ada pembayaran cicilan yang tercatat untuk pinjaman ini.</p>
                        @else
                            <div class="overflow-x-auto rounded-lg border">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Bayar</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Bayar</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($cicilans as $cicilan)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($cicilan->tanggal_simpanan)->format('d-m-Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($cicilan->jumlah_simpanan, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $cicilan->deskripsi }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>