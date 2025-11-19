{{-- resources/views/pinjaman/pay.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bayar Pinjaman Manual') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Catat Pembayaran Cicilan Pinjaman</h1>
                    
                    {{-- Detail Pinjaman Sederhana --}}
                    <div class="mb-6 p-4 border rounded-lg bg-indigo-50">
                        <p class="text-sm">Anggota: <span class="font-semibold">{{ $pinjaman->anggota->nama_lengkap ?? 'N/A' }}</span></p>
                        <p class="text-sm">Pinjaman ID: <span class="font-semibold">#{{ $pinjaman->id }} ({{ ucwords(str_replace('_', ' ', $pinjaman->loan_type)) }})</span></p>
                        <p class="text-sm">Angsuran Ideal Per Bulan: <span class="font-semibold">Rp {{ number_format($angsuranPerBulan, 2, ',', '.') }}</span></p>
                        <p class="text-sm mt-2">Sisa Tagihan Pokok: <span class="font-bold text-red-600">Rp {{ number_format($sisaPokok, 2, ',', '.') }}</span></p>
                        <p class="text-sm">Sisa Tagihan Bunga: <span class="font-bold text-red-600">Rp {{ number_format($sisaBunga, 2, ',', '.') }}</span></p>
                        <p class="text-sm border-t mt-2 pt-2">Sisa Total Tagihan Bersih: <span class="text-lg font-extrabold text-red-800">Rp {{ number_format($sisaPokok + $sisaBunga, 2, ',', '.') }}</span></p>
                    </div>

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pinjaman.process_payment', $pinjaman->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="jumlah_bayar" class="block text-gray-700 font-bold mb-2">Jumlah Pembayaran (Rp)</label>
                            <input type="number" name="jumlah_bayar" id="jumlah_bayar" 
                                class="w-full border-gray-300 rounded-md shadow-sm text-lg font-bold" 
                                value="{{ old('jumlah_bayar', $angsuranPerBulan) }}" 
                                required min="1" step="0.01">
                            @error('jumlah_bayar') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            <p class="text-sm text-gray-500 mt-1">Anda dapat membayar lebih atau kurang dari angsuran ideal, selama tidak melebihi sisa total tagihan.</p>
                        </div>

                        <div class="mb-4">
                            <label for="tanggal_bayar" class="block text-gray-700">Tanggal Pembayaran</label>
                            <input type="date" name="tanggal_bayar" id="tanggal_bayar" 
                                class="w-full border-gray-300 rounded-md" 
                                value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                            @error('tanggal_bayar') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="block text-gray-700">Keterangan (Opsional)</label>
                            <textarea name="deskripsi" id="deskripsi" 
                                class="w-full border-gray-300 rounded-md">{{ old('deskripsi', 'Pembayaran tunai cicilan pinjaman.') }}</textarea>
                            @error('deskripsi') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end pt-4 border-t">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow-lg transition">
                                {{ __('Catat Pembayaran') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>