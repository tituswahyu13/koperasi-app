{{-- resources/views/simulasi/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Simulasi Pinjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6 border-b pb-2">Kalkulator Simulasi Pinjaman</h1>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- KOLOM KIRI: FORM INPUT --}}
                        <div class="md:col-span-1 border-r pr-6">
                            <h3 class="text-xl font-semibold mb-4">Masukkan Detail Pinjaman</h3>
                            
                            @if ($errors->any())
                                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('simulasi.calculate') }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label for="loan_type" class="block text-gray-700">Jenis Pinjaman</label>
                                    <select name="loan_type" id="loan_type" class="w-full border-gray-300 rounded-md" required>
                                        <option value="">Pilih Jenis Pinjaman</option>
                                        @foreach ($loanTypes as $key => $config)
                                            <option value="{{ $key }}" {{ old('loan_type', $results['input']['loan_type'] ?? '') == $key ? 'selected' : '' }}>
                                                {{ $config['label'] }} (Bunga: {{ $config['bunga']*100 }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="jumlah_pinjaman" class="block text-gray-700">Jumlah Pinjaman (Rp)</label>
                                    <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" class="w-full border-gray-300 rounded-md" value="{{ old('jumlah_pinjaman', $results['input']['jumlah_pinjaman'] ?? '') }}" required min="100000">
                                </div>

                                <div class="mb-6">
                                    <label for="tenor" class="block text-gray-700">Tenor / Jangka Waktu (Bulan)</label>
                                    <input type="number" name="tenor" id="tenor" class="w-full border-gray-300 rounded-md" value="{{ old('tenor', $results['input']['tenor'] ?? '') }}" required min="1" max="120">
                                </div>

                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow transition">Hitung Simulasi</button>
                            </form>
                        </div>
                        
                        {{-- KOLOM KANAN: OUTPUT HASIL --}}
                        <div class="md:col-span-2">
                            <h3 class="text-xl font-semibold mb-4">Hasil Perhitungan</h3>

                            @if ($results)
                                <div class="space-y-4">
                                    <div class="p-4 bg-indigo-50 rounded-lg shadow">
                                        <p class="text-sm text-indigo-700">Jenis Pinjaman:</p>
                                        <p class="text-xl font-bold">{{ $results['config_label'] }} ({{ $results['input']['tenor'] }} Bulan)</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="p-4 bg-gray-50 rounded-lg shadow">
                                            <p class="text-sm text-gray-600">Total Bunga Pinjaman:</p>
                                            <p class="text-lg font-semibold text-red-600">Rp {{ number_format($results['bunga_total'], 2, ',', '.') }}</p>
                                        </div>
                                        <div class="p-4 bg-gray-50 rounded-lg shadow">
                                            <p class="text-sm text-gray-600">Biaya Administrasi:</p>
                                            <p class="text-lg font-semibold text-red-600">Rp {{ number_format($results['biaya_admin'], 2, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="p-4 bg-yellow-50 rounded-lg shadow">
                                            <p class="text-sm text-yellow-800">Potongan Wajib Pinjam (1%):</p>
                                            <p class="text-lg font-semibold text-yellow-900">Rp {{ number_format($results['potongan_wajib_pinjam'], 2, ',', '.') }}</p>
                                        </div>
                                        <div class="p-4 bg-green-100 rounded-lg shadow">
                                            <p class="text-sm text-green-700">Uang Bersih Diterima Anggota:</p>
                                            <p class="text-xl font-bold text-green-900">Rp {{ number_format($results['uang_diterima'], 2, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 bg-indigo-100 rounded-lg shadow text-center font-bold mt-6">
                                        <p class="text-lg text-indigo-700">TOTAL ANGSURAN POKOK + BUNGA PER BULAN</p>
                                        <p class="text-4xl text-indigo-900">Rp {{ number_format($results['angsuran_per_bulan'], 2, ',', '.') }}</p>
                                    </div>
                                    
                                    <div class="p-4 bg-gray-100 rounded-lg shadow">
                                        <p class="text-sm text-gray-600">Total Tagihan (Pokok + Bunga):</p>
                                        <p class="text-lg font-semibold">Rp {{ number_format($results['total_tagihan'], 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="p-8 text-center text-gray-500 border-dashed border-2 rounded-lg">
                                    Masukkan detail pinjaman di sebelah kiri untuk melihat simulasi.
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>