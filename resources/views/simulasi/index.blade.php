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
                    
                    {{-- TAMPILAN BEBAN HUTANG AKTIF ANGGOTA --}}
                    @auth
                        @if ($totalActiveLoan > 0)
                            <div class="mb-6 p-4 bg-red-100 border border-red-400 rounded-lg shadow-md">
                                <h3 class="text-xl font-bold text-red-800">PERINGATAN: Hutang Pinjaman Aktif Ditemukan!</h3>
                                <p class="text-sm text-red-700">Anggota ini masih memiliki **Total Sisa Tagihan Pinjaman** sebesar:</p>
                                <p class="text-3xl font-extrabold text-red-900 mt-2">Rp {{ number_format($totalActiveLoan, 2, ',', '.') }}</p>
                                <p class="text-xs mt-2 text-red-700">Pinjaman baru **TIDAK DAPAT** diajukan sebelum pinjaman lama dilunasi. Jumlah pinjaman bersih yang diterima di simulasi ini **SUDAH dikurangi** hutang ini.</p>
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-green-100 border border-green-400 rounded-lg shadow-md text-green-800">
                                Anggota ini tidak memiliki hutang pinjaman aktif yang tercatat. Pengajuan pinjaman baru diizinkan.
                            </div>
                        @endif
                    @else
                        <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 rounded-lg shadow-md text-yellow-800">
                            Masuk sebagai anggota untuk melihat status hutang pinjaman aktif Anda.
                        </div>
                    @endauth
                    
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
                                @php
                                    // Hitungan Subtotal setelah potongan wajib dan admin
                                    $subtotalPotongan = $results['input']['jumlah_pinjaman'] - $results['biaya_admin'] - $results['potongan_wajib_pinjam'];
                                @endphp
                                
                                <div class="space-y-4">
                                    <div class="p-4 bg-indigo-50 rounded-lg shadow">
                                        <p class="text-sm text-indigo-700">Jenis Pinjaman:</p>
                                        <p class="text-xl font-bold">{{ $results['config_label'] }} ({{ $results['input']['tenor'] }} Bulan)</p>
                                    </div>

                                    <div class="p-4 border rounded-lg bg-gray-50">
                                        <p class="text-lg font-bold mb-2">Rincian Pencairan Dana</p>
                                        <div class="flex justify-between py-1 border-b">
                                            <span>1. Pokok Pinjaman Baru</span>
                                            <span class="font-semibold text-gray-900">Rp {{ number_format($results['input']['jumlah_pinjaman'], 2, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 text-red-600">
                                            <span>2. Biaya Admin & Wajib Pinjam</span>
                                            <span class="font-semibold">- Rp {{ number_format($results['biaya_admin'] + $results['potongan_wajib_pinjam'], 2, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between py-2 border-t mt-2 font-bold text-gray-800">
                                            <span>Subtotal Setelah Potongan Koperasi</span>
                                            <span>Rp {{ number_format($subtotalPotongan, 2, ',', '.') }}</span>
                                        </div>
                                        
                                        {{-- PENGURANGAN HUTANG LAMA (BARU DITAMBAHKAN) --}}
                                        @if ($totalActiveLoan > 0)
                                            <div class="flex justify-between py-1 text-red-800 border-t pt-2 mt-2">
                                                <span class="font-bold">3. Pelunasan Hutang Pinjaman Lama</span>
                                                <span class="font-extrabold">- Rp {{ number_format($totalActiveLoan, 2, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        <div class="flex justify-between pt-4 mt-4 bg-green-100 p-2 rounded-md font-extrabold text-green-900 border-t-4 border-green-500">
                                            <span>Uang Bersih Diterima Anggota</span>
                                            <span>Rp {{ number_format($results['uang_diterima'], 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    
                                    {{-- RINCIAN TAGIHAN BULANAN --}}
                                    <div class="p-4 bg-indigo-100 rounded-lg shadow text-center font-bold mt-6">
                                        <p class="text-lg text-indigo-700">TOTAL ANGSURAN POKOK + BUNGA PER BULAN</p>
                                        <p class="text-4xl text-indigo-900">Rp {{ number_format($results['angsuran_per_bulan'], 2, ',', '.') }}</p>
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