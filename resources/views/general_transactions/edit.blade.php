{{-- resources/views/general_transactions/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaksi Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Edit Transaksi Umum #{{ $general_transaction->id }}</h1>
                    
                    {{-- Menampilkan pesan error atau sukses --}}
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

                    <form action="{{ route('general_transactions.update', $general_transaction->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="type" class="block text-gray-700">Jenis Transaksi</label>
                            <select name="type" id="type" class="w-full border-gray-300 rounded-md" required>
                                <option value="out" {{ old('type', $general_transaction->type) == 'out' ? 'selected' : '' }}>Pengeluaran (Debit)</option>
                                <option value="in" {{ old('type', $general_transaction->type) == 'in' ? 'selected' : '' }}>Pemasukan (Kredit)</option>
                            </select>
                            @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="category" class="block text-gray-700">Kategori Transaksi</label>
                            <select name="category" id="category" class="w-full border-gray-300 rounded-md" required>
                                <option value="">--- Pilih Kategori ---</option>
                                
                                @php
                                    // Definisikan categories secara lokal (sama seperti di view Create)
                                    $categories = [
                                        'pendapatan_toko' => 'Pendapatan Penjualan Toko',
                                        'bunga_pinjaman_bank' => 'Pemasukan Bunga Bank',
                                        'hutang_usaha' => 'Hutang Usaha',
                                        'pemasukan_lain' => 'Pemasukan Lain-lain',
                                        'belanja_toko' => 'Belanja Stok Toko',
                                        'gaji_karyawan' => 'Gaji Karyawan Operasional',
                                        'biaya_sewa' => 'Biaya Sewa Kantor/Toko',
                                        'pembayaran_hutang' => 'Pembayaran Utang Usaha',
                                        'pengeluaran_lain' => 'Pengeluaran Lain-lain'
                                    ];
                                @endphp
                                
                                <optgroup label="Pemasukan Koperasi">
                                @foreach (['pendapatan_toko', 'bunga_pinjaman_bank', 'pemasukan_lain'] as $key)
                                    <option value="{{ $key }}" {{ old('category', $general_transaction->category) == $key ? 'selected' : '' }}>{{ $categories[$key] }}</option>
                                @endforeach
                                </optgroup>
                                
                                <optgroup label="Pengeluaran Koperasi">
                                @foreach (['belanja_toko', 'gaji_karyawan', 'biaya_sewa', 'pembayaran_hutang', 'pengeluaran_lain'] as $key)
                                    <option value="{{ $key }}" {{ old('category', $general_transaction->category) == $key ? 'selected' : '' }}>{{ $categories[$key] }}</option>
                                @endforeach
                                </optgroup>
                                
                            </select>
                            @error('category') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block text-gray-700">Jumlah (Rp)</label>
                            <input type="number" name="amount" id="amount" class="w-full border-gray-300 rounded-md" value="{{ old('amount', $general_transaction->amount) }}" required min="1" step="0.01">
                            @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-gray-700">Deskripsi/Keterangan</label>
                            <textarea name="description" id="description" class="w-full border-gray-300 rounded-md">{{ old('description', $general_transaction->description) }}</textarea>
                            @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="transaction_date" class="block text-gray-700">Tanggal Transaksi</label>
                            {{-- Karena kita sudah menambahkan $casts di Model, format() akan berfungsi --}}
                            <input type="date" name="transaction_date" id="transaction_date" class="w-full border-gray-300 rounded-md" 
                                value="{{ old('transaction_date', $general_transaction->transaction_date->format('Y-m-d')) }}" required>
                            @error('transaction_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">Perbarui Transaksi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>