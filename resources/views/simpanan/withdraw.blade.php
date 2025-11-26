{{-- resources/views/simpanan/withdraw.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Penarikan Simpanan Individu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4 text-red-600">Form Penarikan Simpanan Individu</h1>

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
                    
                    {{-- KIRIM DATA SALDO ANGGOTA KE JAVASCRIPT --}}
                    @php
                        // Map data anggota ke format yang mudah dicari JS: {anggota_id: {saldo_manasuka: X, ...}}
                        $anggotaBalances = collect($anggotas)->keyBy('id')->map(function ($anggota) {
                            return [
                                'saldo_manasuka' => $anggota->saldo_manasuka,
                                'saldo_mandiri' => $anggota->saldo_mandiri,
                                'saldo_jasa_anggota' => $anggota->saldo_jasa_anggota,
                            ];
                        });
                    @endphp
                    <script>
                        const memberBalances = @json($anggotaBalances);
                        
                        // Helper untuk format rupiah
                        function formatRupiah(number) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            }).format(number);
                        }

                        function updateBalanceDisplay() {
                            const anggotaId = document.getElementById('anggota_id').value;
                            const jenisSimpanan = document.getElementById('jenis_simpanan').value;
                            const displayElement = document.getElementById('available_balance');
                            const withdrawInput = document.getElementById('jumlah_penarikan');
                            
                            displayElement.textContent = 'Rp 0,00';
                            withdrawInput.max = 0; // Reset batas maksimal

                            if (!anggotaId || !jenisSimpanan || !memberBalances[anggotaId]) {
                                return;
                            }

                            let balance = 0;
                            let balanceKey = '';

                            switch (jenisSimpanan) {
                                case 'manasuka':
                                    balanceKey = 'saldo_manasuka';
                                    break;
                                case 'mandiri':
                                    balanceKey = 'saldo_mandiri';
                                    break;
                                case 'jasa_anggota':
                                    balanceKey = 'saldo_jasa_anggota';
                                    break;
                                default:
                                    return; // Jika jenis simpanan tidak relevan
                            }

                            // Pastikan objek anggota dan key saldonya ada
                            balance = memberBalances[anggotaId][balanceKey];
                            
                            if (balance !== undefined && balance !== null) {
                                displayElement.textContent = formatRupiah(balance);
                                withdrawInput.max = balance; // Set batas maksimal penarikan
                            }
                        }

                        // Tambahkan event listener saat DOM dimuat
                        document.addEventListener('DOMContentLoaded', () => {
                            document.getElementById('anggota_id').addEventListener('change', updateBalanceDisplay);
                            document.getElementById('jenis_simpanan').addEventListener('change', updateBalanceDisplay);
                            // Panggil saat halaman pertama dimuat
                            updateBalanceDisplay();
                        });
                    </script>


                    <form action="{{ route('simpanan.process_withdrawal') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="anggota_id" class="block text-gray-700">Pilih Anggota</label>
                            <select name="anggota_id" id="anggota_id" class="w-full border-gray-300 rounded-md">
                                <option value="">--- Pilih Anggota ---</option>
                                @foreach ($anggotas as $anggota)
                                <option value="{{ $anggota->id }}" {{ old('anggota_id') == $anggota->id ? 'selected' : '' }}>{{ $anggota->nama_lengkap }} ({{ $anggota->user->username ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            @error('anggota_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="jenis_simpanan" class="block text-gray-700 font-bold mb-2">Jenis Simpanan yang Ditarik</label>
                            <select name="jenis_simpanan" id="jenis_simpanan" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">--- Pilih Jenis Simpanan ---</option>
                                <option value="manasuka" {{ old('jenis_simpanan') == 'manasuka' ? 'selected' : '' }}>Simpanan Manasuka</option>
                                <option value="mandiri" {{ old('jenis_simpanan') == 'mandiri' ? 'selected' : '' }}>Simpanan Mandiri</option>
                                <option value="jasa_anggota" {{ old('jenis_simpanan') == 'jasa_anggota' ? 'selected' : '' }}>Simpanan Jasa Anggota</option>
                            </select>
                            <p class="text-sm text-red-500 mt-1">Penarikan Wajib dan Wajib Khusus hanya dilakukan saat anggota berhenti.</p>
                            @error('jenis_simpanan') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        {{-- DISPLAY SALDO YANG TERSEDIA (FITUR BARU) --}}
                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 rounded-lg">
                            Saldo Tersedia: <span id="available_balance" class="font-extrabold text-lg text-yellow-800">Rp 0,00</span>
                            <p class="text-xs text-gray-500 mt-1">Jumlah penarikan maksimal akan dibatasi oleh saldo yang tersedia.</p>
                        </div>


                        <div class="mb-4">
                            <label for="jumlah_penarikan" class="block text-gray-700">Jumlah Penarikan (Rp)</label>
                            {{-- Input memiliki MAX attribute yang diisi oleh JavaScript untuk mencegah penarikan > saldo --}}
                            <input type="number" name="jumlah_penarikan" id="jumlah_penarikan" class="w-full border-gray-300 rounded-md" value="{{ old('jumlah_penarikan') }}" required min="1" step="0.01">
                            @error('jumlah_penarikan') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tanggal_simpanan" class="block text-gray-700">Tanggal Penarikan</label>
                            <input type="date" name="tanggal_simpanan" id="tanggal_simpanan" class="w-full border-gray-300 rounded-md" value="{{ old('tanggal_simpanan', date('Y-m-d')) }}" required>
                            @error('tanggal_simpanan') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="deskripsi" class="block text-gray-700">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" id="deskripsi" class="w-full border-gray-300 rounded-md">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition">Proses Penarikan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>