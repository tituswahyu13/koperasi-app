{{-- resources/views/closing/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tutup Bulan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Proses Tutup Bulan</h1>
                    <p class="mb-4">Klik tombol di bawah untuk mencatat simpanan wajib dan manasuka secara otomatis untuk semua anggota.</p>

                    @if (session('success'))
                        <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-200 text-red-800 p-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('closing.process') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm">
                            Proses Tutup Bulan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>