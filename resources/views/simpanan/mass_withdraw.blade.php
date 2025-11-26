{{-- resources/views/simpanan/mass_withdraw.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penarikan Simpanan Manasuka Massal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4 text-red-600">Proses Penarikan Massal (Manasuka)</h1>
                    <p class="mb-4 text-gray-600">Proses ini akan menarik jumlah yang sama dari saldo Manasuka **semua anggota aktif** yang saldonya mencukupi.</p>
                    
                    @isset($totalManasuka)
                        <div class="mb-6 p-4 border border-blue-300 rounded-lg bg-blue-50">
                            <p class="font-semibold">Total Saldo Manasuka Seluruh Anggota: 
                            <span class="text-blue-800">Rp {{ number_format($totalManasuka, 2, ',', '.') }}</span></p>
                        </div>
                    @endisset

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

                    <form action="{{ route('simpanan.process_mass_withdrawal') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="jumlah_per_anggota" class="block text-gray-700 font-bold mb-2">Jumlah Penarikan per Anggota (Rp)</label>
                            <input type="number" name="jumlah_per_anggota" id="jumlah_per_anggota" class="w-full border-gray-300 rounded-md" value="{{ old('jumlah_per_anggota') }}" required min="1" step="0.01">
                            <p class="text-sm text-gray-500 mt-1">Hanya anggota yang memiliki saldo Manasuka >= jumlah ini yang akan diproses.</p>
                            @error('jumlah_per_anggota') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tanggal_penarikan" class="block text-gray-700">Tanggal Transaksi Penarikan</label>
                            <input type="date" name="tanggal_penarikan" id="tanggal_penarikan" class="w-full border-gray-300 rounded-md" value="{{ old('tanggal_penarikan', date('Y-m-d')) }}" required>
                            @error('tanggal_penarikan') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="deskripsi" class="block text-gray-700">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" id="deskripsi" class="w-full border-gray-300 rounded-md">{{ old('deskripsi', 'Penarikan Manasuka Massal Hari Raya.') }}</textarea>
                            @error('deskripsi') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" 
                                onclick="return confirm('APAKAH ANDA YAKIN? Ini akan memproses penarikan untuk banyak anggota sekaligus.')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition">
                            Proses Penarikan Massal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>