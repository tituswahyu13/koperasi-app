{{-- resources/views/general_transactions/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Transaksi Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Form Transaksi Umum</h1>
                    
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

                    <form action="{{ route('general_transactions.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="type" class="block text-gray-700">Jenis Transaksi</label>
                            <select name="type" id="type" class="w-full border-gray-300 rounded-md" required>
                                <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Pengeluaran (Debit)</option>
                                <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Pemasukan (Kredit)</option>
                            </select>
                            @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block text-gray-700">Jumlah (Rp)</label>
                            <input type="number" name="amount" id="amount" class="w-full border-gray-300 rounded-md" value="{{ old('amount') }}" required min="1" step="0.01">
                            @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="category" class="block text-gray-700">Kategori (Contoh: Gaji, Sewa, Bunga Bank)</label>
                            <input type="text" name="category" id="category" class="w-full border-gray-300 rounded-md" value="{{ old('category') }}" required>
                            @error('category') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-gray-700">Deskripsi/Keterangan</label>
                            <textarea name="description" id="description" class="w-full border-gray-300 rounded-md">{{ old('description') }}</textarea>
                            @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="transaction_date" class="block text-gray-700">Tanggal Transaksi</label>
                            <input type="date" name="transaction_date" id="transaction_date" class="w-full border-gray-300 rounded-md" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                            @error('transaction_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow transition">Simpan Transaksi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>