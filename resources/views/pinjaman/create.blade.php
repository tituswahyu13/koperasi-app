{{-- resources/views/pinjaman/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Pinjaman Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Form Pengajuan Pinjaman</h1>

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

                    <form action="{{ route('pinjaman.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Kolom Kiri: Data Anggota & Jumlah --}}
                            <div>
                                <h2 class="text-xl font-semibold mb-3 border-b pb-1">Detail Pinjaman</h2>

                                <div class="mb-4">
                                    <label for="anggota_id" class="block text-gray-700">Pilih Anggota</label>
                                    <select name="anggota_id" id="anggota_id" class="w-full border-gray-300 rounded-md">
                                        @foreach ($anggotas as $anggota)
                                            <option value="{{ $anggota->id }}" {{ old('anggota_id') == $anggota->id ? 'selected' : '' }}>
                                                {{ $anggota->nama_lengkap }} ({{ $anggota->user->username ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('anggota_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="jumlah_pinjaman" class="block text-gray-700">Jumlah Pinjaman (Rp)</label>
                                    <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" class="w-full border-gray-300 rounded-md" value="{{ old('jumlah_pinjaman') }}" required min="100000">
                                    @error('jumlah_pinjaman') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="tenor" class="block text-gray-700">Tenor / Jangka Waktu (Bulan)</label>
                                    <input type="number" name="tenor" id="tenor" class="w-full border-gray-300 rounded-md" value="{{ old('tenor') }}" required min="1" max="60">
                                    @error('tenor') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="tanggal_pinjaman" class="block text-gray-700">Tanggal Pengajuan/Pinjaman</label>
                                    <input type="date" name="tanggal_pinjaman" id="tanggal_pinjaman" class="w-full border-gray-300 rounded-md" value="{{ old('tanggal_pinjaman', date('Y-m-d')) }}" required>
                                    @error('tanggal_pinjaman') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                            </div>

                            {{-- Kolom Kanan: Jenis & Pembayaran --}}
                            <div>
                                <h2 class="text-xl font-semibold mb-3 border-b pb-1">Jenis & Metode Pembayaran</h2>

                                <div class="mb-4">
                                    <label for="loan_type" class="block text-gray-700">Jenis Pinjaman</label>
                                    <select name="loan_type" id="loan_type" class="w-full border-gray-300 rounded-md" required>
                                        <option value="">Pilih Jenis Pinjaman</option>
                                        {{-- $loanTypes berasal dari PinjamanController --}}
                                        @foreach ($loanTypes as $key => $config)
                                            <option value="{{ $key }}" {{ old('loan_type') == $key ? 'selected' : '' }}>
                                                {{ $config['label'] }} (Bunga: {{ $config['bunga']*100 }}%, Admin: {{ $config['admin']*100 }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('loan_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="payment_date_type" class="block text-gray-700">Tanggal Angsuran Jatuh Tempo</label>
                                    <select name="payment_date_type" id="payment_date_type" class="w-full border-gray-300 rounded-md" required>
                                        <option value="">Pilih Metode Pembayaran</option>
                                        <option value="tgl_1" {{ old('payment_date_type') == 'tgl_1' ? 'selected' : '' }}>Tanggal 1 Bulanan</option>
                                        <option value="tgl_15" {{ old('payment_date_type') == 'tgl_15' ? 'selected' : '' }}>Tanggal 15 Bulanan</option>
                                        <option value="manual" {{ old('payment_date_type') == 'manual' ? 'selected' : '' }}>Bayar Manual (Non-Otomatis)</option>
                                    </select>
                                    <p class="text-sm text-gray-500 mt-1">Tanggal 1 dan 15 akan digunakan untuk menentukan jatuh tempo angsuran pertama.</p>
                                    @error('payment_date_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="deskripsi" class="block text-gray-700">Deskripsi/Keterangan (Opsional)</label>
                                    <textarea name="deskripsi" id="deskripsi" class="w-full border-gray-300 rounded-md">{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 border-t pt-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md shadow transition">
                                {{ __('Ajukan Pinjaman') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>