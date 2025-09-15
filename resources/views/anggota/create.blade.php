{{-- resources/views/anggota/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Anggota Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Form Tambah Anggota</h1>

                    <form action="{{ route('anggota.store') }}" method="POST">
                        @csrf
                
                        <div class="mb-4">
                            <label for="username" class="block text-gray-700">Username</label>
                            <input type="text" name="username" id="username" class="w-full border-gray-300 rounded-md" required>
                        </div>
                
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700">Password</label>
                            <input type="password" name="password" id="password" class="w-full border-gray-300 rounded-md" required>
                        </div>
                
                        <div class="mb-4">
                            <label for="nama_lengkap" class="block text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="w-full border-gray-300 rounded-md" required>
                        </div>
                
                        <div class="mb-4">
                            <label for="alamat" class="block text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" class="w-full border-gray-300 rounded-md"></textarea>
                        </div>
                
                        <div class="mb-4">
                            <label for="no_hp" class="block text-gray-700">Nomor HP</label>
                            <input type="text" name="no_hp" id="no_hp" class="w-full border-gray-300 rounded-md">
                        </div>
                
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Simpan Anggota</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>