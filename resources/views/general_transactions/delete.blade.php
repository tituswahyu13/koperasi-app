{{-- resources/views/general_transactions/delete.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hapus Transaksi Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 text-gray-900">
                    <h1 class="text-2xl font-bold text-red-600 mb-4">Konfirmasi Penghapusan Transaksi</h1>
                    
                    <div class="p-4 mb-6 bg-red-50 border border-red-300 rounded-lg">
                        <p class="font-semibold text-lg text-red-700">PERINGATAN! Anda akan menghapus data transaksi ini secara permanen.</p>
                        <p class="text-sm text-red-600 mt-2">Penghapusan ini akan memengaruhi keakuratan Laporan Arus Kas dan Laporan Neraca koperasi.</p>
                    </div>

                    <h3 class="text-xl font-bold mb-4">Detail Transaksi yang Akan Dihapus:</h3>
                    
                    <div class="grid grid-cols-2 gap-4 text-gray-700 mb-6 border p-4 rounded-lg">
                        <p><span class="font-medium">Jenis Transaksi:</span> 
                            <span class="font-bold {{ $general_transaction->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $general_transaction->type === 'in' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </p>
                        <p><span class="font-medium">Tanggal:</span> {{ \Carbon\Carbon::parse($general_transaction->transaction_date)->format('d-m-Y') }}</p>
                        <p><span class="font-medium">Kategori:</span> {{ $general_transaction->category }}</p>
                        <p><span class="font-medium">Jumlah:</span> <span class="font-bold">Rp {{ number_format($general_transaction->amount, 2, ',', '.') }}</span></p>
                        <p class="col-span-2"><span class="font-medium">Deskripsi:</span> {{ $general_transaction->description }}</p>
                    </div>

                    <form action="{{ route('general_transactions.destroy', $general_transaction->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-md shadow transition">
                            YA, Hapus Transaksi Ini
                        </button>
                        <a href="{{ route('general_transactions.index') }}" class="ml-4 text-gray-600 hover:text-gray-900 px-6 py-3">
                            Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>