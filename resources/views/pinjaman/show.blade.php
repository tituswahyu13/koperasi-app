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
                    
                    {{-- Pesan Sukses/Error --}}
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
                    
                    {{-- HEADER DAN TOMBOL AKSI --}}
                    <div class="flex justify-between items-center mb-6 border-b pb-2">
                        <h1 class="text-3xl font-bold text-gray-900">Rincian Pinjaman</h1>
                        {{-- Tombol Bayar Manual hanya tampil jika Approved dan belum Lunas --}}
                        @if ($pinjaman->status == 'approved' && $pinjaman->sisa_pinjaman_bersih > 0)
                            <a href="{{ route('pinjaman.pay', $pinjaman->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow transition">
                                Bayar Cicilan Manual
                            </a>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Kartu Status Pinjaman --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow-md col-span-1">
                            <h3 class="text-lg font-semibold mb-3 border-b pb-2">Status</h3>
                            @php
                                $statusMap = [
                                    'pending' => ['class' => 'bg-yellow-200 text-yellow-800', 'label' => 'Menunggu Persetujuan'],
                                    'approved' => ['class' => 'bg-green-200 text-green-800', 'label' => 'Disetujui'],
                                    'rejected' => ['class' => 'bg-red-200 text-red-800', 'label' => 'Ditolak'],
                                    'lunas' => ['class' => 'bg-indigo-200 text-indigo-800', 'label' => 'Lunas'],
                                ];
                                $statusInfo = $statusMap[$pinjaman->status] ?? $statusMap['pending'];
                            @endphp
                            <p class="text-center font-bold text-2xl py-4 rounded-lg {{ $statusInfo['class'] }}">
                                {{ $statusInfo['label'] }}
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
                                <p class="text-sm text-red-700">Bunga Total ({{ ($pinjaman->config['bunga'] ?? 0) * 100 }}%)</p>
                                <p class="text-xl">Rp {{ number_format($pinjaman->bunga, 2, ',', '.') }}</p>
                            </div>
                            <div class="p-4 bg-red-50 rounded-lg shadow">
                                <p class="text-sm text-red-700">Biaya Administrasi ({{ ($pinjaman->config['admin'] ?? 0) * 100 }}%)</p>
                                <p class="text-xl">Rp {{ number_format($pinjaman->biaya_admin, 2, ',', '.') }}</p>
                            </div>
                            <div class="p-4 bg-yellow-50 rounded-lg shadow">
                                <p class="text-sm text-yellow-700">Potongan Wajib Pinjam (1%)</p>
                                <p class="text-xl">Rp {{ number_format($pinjaman->potongan_wajib_pinjam, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                             <div class="p-4 bg-green-100 rounded-lg shadow text-center font-bold">
                                <p class="text-lg text-green-700">Total Tagihan Pokok + Bunga</p>
                                <p class="text-3xl">Rp {{ number_format($pinjaman->total_tagihan, 2, ',', '.') }}</p>
                            </div>

                            <div class="p-4 bg-blue-100 rounded-lg shadow text-center font-bold">
                                <p class="text-lg text-blue-700">Angsuran Pokok + Bunga Per Bulan</p>
                                <p class="text-3xl">Rp {{ number_format($pinjaman->angsuran_per_bulan, 2, ',', '.') }}</p>
                            </div>

                            <div class="p-4 border border-red-300 rounded-lg shadow text-center font-bold bg-white">
                                <p class="text-lg text-red-700">Sisa Tagihan Bersih</p>
                                <p class="text-3xl text-red-900">Rp {{ number_format($pinjaman->sisa_pinjaman_bersih, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat Cicilan --}}
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold mb-4 border-b pb-2">Riwayat Pembayaran Cicilan (Payments)</h2>
                        
                        @if ($payments->isEmpty())
                            <p class="text-gray-600">Belum ada pembayaran cicilan yang tercatat untuk pinjaman ini.</p>
                        @else
                            <div class="overflow-x-auto rounded-lg border">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Bayar</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pokok</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Bunga</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider font-bold">Total Bayar</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($payments as $payment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($payment->tanggal_bayar)->format('d-m-Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($payment->pokok, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($payment->bunga, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-bold">Rp {{ number_format($payment->total_bayar, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->sumber_pembayaran }}</td>
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