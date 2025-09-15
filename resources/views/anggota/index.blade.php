{{-- resources/views/anggota/index.blade.php --}}

<x-app-layout>
    {{-- You can optionally add a header slot for the page title --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Anggota') }}
        </h2>
    </x-slot>

    {{-- The rest of your content goes here --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Daftar Anggota</h1>

                    @if (session('success'))
                    <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                    @endif

                    <a href="{{ route('anggota.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md shadow-sm transition">Tambah Anggota</a>

                    <table class="min-w-full bg-white border mt-4">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Nama Lengkap</th>
                                <th class="py-2 px-4 border-b">Username</th>
                                <th class="py-2 px-4 border-b">Saldo Simpanan</th>
                                <th class="py-2 px-4 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($anggotas as $anggota)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $anggota->nama_lengkap }}</td>
                                <td class="py-2 px-4 border-b">{{ $anggota->user ? $anggota->user->username : 'N/A' }}</td>
                                <td class="py-2 px-4 border-b">Rp {{ number_format($anggota->saldo_simpanan, 2, ',', '.') }}</td>
                                <td class="py-2 px-4 border-b">
                                    <a href="{{ url('/anggota/' . $anggota->id . '/edit') }}" class="text-blue-500 hover:underline">Edit</a>
                                    <form action="{{ url('/anggota/' . $anggota->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $anggotas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>