<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menu Laporan') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Pilih Laporan</h1>
                    <div class="space-y-4">
                        <a href="{{ route('laporan.simpanan') }}" class="block p-4 rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
                            Laporan Simpanan
                        </a>
                        <a href="{{ route('laporan.pinjaman') }}" class="block p-4 rounded-lg bg-green-100 text-green-800 hover:bg-green-200 transition">
                            Laporan Pinjaman
                        </a>
                        <a href="{{ route('laporan.arus-kas') }}" class="block p-4 rounded-lg bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition">
                            Laporan Arus Kas
                        </a>
                        <a href="{{ route('laporan.neraca') }}" class="block p-4 rounded-lg bg-red-100 text-red-800 hover:bg-red-200 transition">
                            Laporan Neraca
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>