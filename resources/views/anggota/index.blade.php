{{-- resources/views/anggota/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Anggota') }}
        </h2>
    </x-slot>

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

                    <div class="overflow-x-auto mt-6 rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Nama Lengkap</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Username</th>
                                    
                                    {{-- KOLOM SALDO --}}
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Saldo Pokok</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Saldo Wajib</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Saldo Wajib Khusus</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Voucher</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Saldo Manasuka</th>
                                    
                                    {{-- KOLOM IURAN BULANAN (Simpanan Wajib/Iuran) --}}
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Iuran Wajib</th>

                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($anggotas as $anggota)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border-r">
                                        @if ($anggota->status_aktif == 1)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">{{ $anggota->nama_lengkap }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ $anggota->user ? $anggota->user->username : 'N/A' }}</td>
                                    
                                    {{-- DATA SALDO BARU --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right font-semibold">Rp {{ number_format($anggota->saldo_pokok, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r text-right">Rp {{ number_format($anggota->saldo_wajib, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r text-right">Rp {{ number_format($anggota->saldo_wajib_khusus, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r text-right">Rp {{ number_format($anggota->voucher, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r text-right">Rp {{ number_format($anggota->saldo_manasuka, 2, ',', '.') }}</td>
                                    
                                    {{-- DATA IURAN BULANAN (Display Gabungan Wajib dan Khusus) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r text-right">
                                        Rp {{ number_format($anggota->simpanan_wajib + $anggota->simpanan_wajib_khusus, 2, ',', '.') }}
                                        <div class="text-xs text-gray-500">(W: {{ number_format($anggota->simpanan_wajib, 0, ',', '.') }}/ Kh: {{ number_format($anggota->simpanan_wajib_khusus, 0, ',', '.') }})</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                        <a href="{{ route('anggota.edit', $anggota->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <!-- <form action="{{ route('anggota.destroy', $anggota->id) }}" method="POST" class="inline ml-4">
                                            @csrf
                                            @method('DELETE')
                                            {{-- PERINGATAN: Menggunakan form konfirmasi native, pastikan ini sesuai dengan lingkungan Anda --}}
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini secara permanen? Penghapusan ini akan menghapus data Anggota dan User.')">Hapus</button>
                                        </form> -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $anggotas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>