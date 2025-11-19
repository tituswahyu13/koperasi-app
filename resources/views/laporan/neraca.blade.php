{{-- resources/views/laporan/neraca.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Neraca') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6 text-center border-b pb-2">LAPORAN NERACA (POSISI KEUANGAN)</h1>
                    <p class="text-lg text-gray-600 mb-6 text-center">Per Tanggal: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        {{-- KOLOM KIRI: ASET (Harta) --}}
                        <div>
                            <h2 class="text-2xl font-extrabold text-indigo-700 mb-4 border-b-4 border-indigo-700 pb-1">ASET (HARTA)</h2>
                            
                            {{-- Aset Lancar --}}
                            <h3 class="text-xl font-bold text-gray-700 mt-6 mb-3">Aset Lancar</h3>
                            <div class="border p-4 rounded-lg bg-gray-50">
                                <div class="flex justify-between py-2 border-b">
                                    <span>Piutang Pinjaman Anggota (Sisa Tagihan Bersih)</span>
                                    <span class="font-semibold">Rp {{ number_format($neracaData['aset']['Piutang Pinjaman'], 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b text-sm text-gray-500">
                                    <span>Kas & Bank (Nilai Asumsi)</span>
                                    <span class="font-semibold">Rp 0.00</span> 
                                </div>
                                
                                <div class="flex justify-between pt-4 font-extrabold text-lg border-t-2 mt-4 border-gray-400">
                                    <span>TOTAL ASET LANCAR</span>
                                    <span class="text-indigo-700">Rp {{ number_format($neracaData['aset']['Piutang Pinjaman'], 2, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between pt-8 font-extrabold text-xl">
                                <span>TOTAL ASET</span>
                                <span class="text-indigo-700 border-b-4 border-double border-indigo-700">Rp {{ number_format($neracaData['total_aset'], 2, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: LIABILITAS DAN EKUITAS (Kewajiban dan Modal) --}}
                        <div>
                            <h2 class="text-2xl font-extrabold text-red-700 mb-4 border-b-4 border-red-700 pb-1">LIABILITAS & EKUITAS</h2>

                            {{-- Liabilitas (Kewajiban) --}}
                            <h3 class="text-xl font-bold text-gray-700 mt-6 mb-3">Liabilitas Jangka Pendek (Kewajiban)</h3>
                            <div class="border p-4 rounded-lg bg-gray-50">
                                <h4 class="font-semibold mb-2">Simpanan Anggota (Wajib Dikembalikan)</h4>
                                @foreach ($neracaData['liabilitas_ekuitas'] as $label => $jumlah)
                                    @if (str_contains($label, 'Simpanan'))
                                        <div class="flex justify-between py-1 text-sm">
                                            <span>{{ $label }}</span>
                                            <span class="font-semibold">Rp {{ number_format($jumlah, 2, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="flex justify-between pt-4 font-extrabold text-lg border-t mt-4">
                                    <span>TOTAL LIABILITAS</span>
                                    <span class="text-red-700">Rp {{ number_format($neracaData['liabilitas_ekuitas']['Simpanan Anggota'], 2, ',', '.') }}</span>
                                </div>
                            </div>

                            {{-- Ekuitas (Modal) --}}
                            <h3 class="text-xl font-bold text-gray-700 mt-6 mb-3">Ekuitas (Modal dan Laba)</h3>
                            <div class="border p-4 rounded-lg bg-gray-50">
                                @foreach ($neracaData['liabilitas_ekuitas'] as $label => $jumlah)
                                    @if (!str_contains($label, 'Simpanan'))
                                        <div class="flex justify-between py-2 border-b">
                                            <span>{{ $label }}</span>
                                            <span class="font-semibold">Rp {{ number_format($jumlah, 2, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="flex justify-between pt-4 font-extrabold text-lg border-t mt-4">
                                    <span>TOTAL EKUITAS</span>
                                    <span class="text-red-700">Rp {{ number_format($neracaData['liabilitas_ekuitas']['Voucher Modal'] + $neracaData['liabilitas_ekuitas']['Sisa Hasil Usaha (SHU)'], 2, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between pt-8 font-extrabold text-xl">
                                <span>TOTAL LIABILITAS & EKUITAS</span>
                                <span class="text-red-700 border-b-4 border-double border-red-700">Rp {{ number_format($neracaData['total_liabilitas_ekuitas'], 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>