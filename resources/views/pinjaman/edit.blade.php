{{-- resources/views/pinjaman/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Persetujuan Pinjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Persetujuan Pinjaman #{{ $pinjaman->id }}</h1>

                    @if ($pinjaman->status != 'pending')
                        <div class="bg-blue-100 text-blue-800 p-3 rounded mb-4 font-semibold">
                            Pinjaman ini sudah berstatus: {{ ucwords($pinjaman->status) }}. Tidak dapat diubah lagi.
                        </div>
                        <a href="{{ route('pinjaman.index') }}" class="text-indigo-600 hover:text-indigo-900">Kembali ke Daftar Pinjaman</a>
                    @else
                        {{-- Detail Pinjaman untuk review --}}
                        <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                            <h3 class="text-lg font-semibold mb-3">Detail Pengajuan</h3>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><span class="font-medium">Anggota:</span> {{ $pinjaman->anggota->nama_lengkap ?? 'N/A' }}</div>
                                <div><span class="font-medium">Tgl. Pengajuan:</span> {{ \Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d-m-Y') }}</div>
                                <div><span class="font-medium">Jenis Pinjaman:</span> {{ ucwords(str_replace('_', ' ', $pinjaman->loan_type)) }}</div>
                                <div><span class="font-medium">Tenor:</span> {{ $pinjaman->tenor }} Bulan</div>
                                <div><span class="font-medium">Jumlah Pinjaman:</span> Rp {{ number_format($pinjaman->jumlah_pinjaman, 2, ',', '.') }}</div>
                            </div>
                            <div class="mt-3"><span class="font-medium">Keterangan:</span> {{ $pinjaman->deskripsi ?? '-' }}</div>
                        </div>

                        {{-- Form Persetujuan --}}
                        <form action="{{ route('pinjaman.update', $pinjaman->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="status" class="block text-gray-700 font-bold mb-2">Ubah Status</label>
                                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="approved">Setujui (Approved)</option>
                                    <option value="rejected">Tolak (Rejected)</option>
                                </select>
                            </div>

                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow transition">Proses Persetujuan</button>
                            <a href="{{ route('pinjaman.index') }}" class="ml-4 text-gray-600 hover:text-gray-900">Batal</a>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>