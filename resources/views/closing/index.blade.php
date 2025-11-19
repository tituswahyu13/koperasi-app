<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tutup Bulan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-4">Proses Tutup Bulan Otomatis</h1>
                    <p class="mb-6 text-gray-600">Proses ini akan mencatat iuran bulanan anggota dan angsuran pinjaman otomatis untuk semua anggota aktif.</p>
                    <p class="text-lg font-semibold mb-4 text-indigo-700">Bulan Proses Berikutnya: {{ $nextMonth ?? 'N/A' }}</p>

                    @if (session('success'))
                    <div class="bg-green-200 text-green-800 p-3 rounded mb-4 whitespace-pre-wrap">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="bg-red-200 text-red-800 p-3 rounded mb-4 whitespace-pre-wrap">
                        {{ session('error') }}
                    </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            Terdapat kesalahan validasi.
                        </div>
                    @endif

                    <form action="{{ route('closing.process') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <label for="process_date" class="block text-gray-700 font-bold mb-2">Pilih Tanggal Proses (Tgl Transaksi)</label>
                            <input type="date" name="process_date" id="process_date" 
                                class="border-gray-300 rounded-md shadow-sm @error('process_date') border-red-500 @enderror" 
                                value="{{ old('process_date', $defaultDate->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                            
                            @error('process_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            <p class="text-sm text-gray-600 mt-2">PENTING: Pilih tanggal **1** atau tanggal **15** di bulan ini. Simpanan Wajib, Manasuka, dan Voucher akan diproses di Tgl 1. Simpanan Wajib Khusus diproses di Tgl 15. Angsuran Pinjaman Otomatis menyesuaikan tanggal yang Anda pilih (1 atau 15).</p>
                        </div>

                        <button type="submit" 
                                onclick="return confirm('APAKAH ANDA YAKIN? Proses Tutup Bulan hanya boleh dilakukan 1x setiap hari Tgl 1 dan 1x setiap hari Tgl 15. Ini akan mencatat semua iuran dan angsuran otomatis.')" 
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-md shadow-lg transition duration-200">
                            JALANKAN PROSES TUTUP BULAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>