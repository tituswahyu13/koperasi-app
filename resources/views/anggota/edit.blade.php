{{-- resources/views/anggota/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Anggota') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Edit Anggota: {{ $anggota->nama_lengkap ?? 'Data tidak ditemukan' }}</h1>

                    {{-- Debug information --}}
                    @if(config('app.debug'))
                    <div class="bg-yellow-100 p-3 rounded mb-4 text-sm">
                        <strong>Debug Info:</strong><br>
                        ID: {{ $anggota->id ?? 'null' }}<br>
                        Nama: {{ $anggota->nama_lengkap ?? 'null' }}<br>
                        Alamat: {{ $anggota->alamat ?? 'null' }}<br>
                        No HP: {{ $anggota->no_hp ?? 'null' }}<br>
                        User ID: {{ $anggota->user_id ?? 'null' }}<br>
                        Username: {{ $anggota->user->username ?? 'null' }}
                    </div>
                    @endif

                    <form action="{{ url('/anggota/' . $anggota->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-700">Username</label>
                            <p class="w-full bg-gray-200 p-2 rounded-md">{{ $anggota->user ? $anggota->user->username : 'Tidak ada username' }}</p>
                        </div>

                        <div class="mb-4">
                            <label for="nama_lengkap" class="block text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="w-full border-gray-300 rounded-md" value="{{ old('nama_lengkap', $anggota->nama_lengkap) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="alamat" class="block text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" class="w-full border-gray-300 rounded-md">{{ old('alamat', $anggota->alamat) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="no_hp" class="block text-gray-700">Nomor HP</label>
                            <input type="text" name="no_hp" id="no_hp" class="w-full border-gray-300 rounded-md" value="{{ old('no_hp', $anggota->no_hp) }}">
                        </div>

                        <div class="mb-4">
                            <label for="simpanan_wajib" class="block text-gray-700">Simpanan Wajib (per bulan)</label>
                            <input type="number" name="simpanan_wajib" id="simpanan_wajib" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_wajib', $anggota->simpanan_wajib) }}" required min="0">
                        </div>

                        <div class="mb-4">
                            <label for="simpanan_manasuka" class="block text-gray-700">Simpanan Manasuka (per bulan)</label>
                            <input type="number" name="simpanan_manasuka" id="simpanan_manasuka" class="w-full border-gray-300 rounded-md" value="{{ old('simpanan_manasuka', $anggota->simpanan_manasuka) }}" required min="0">
                        </div>

                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Perbarui Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>