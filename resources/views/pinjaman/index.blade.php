{{-- resources/views/pinjaman/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pinjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Daftar Pengajuan Pinjaman</h1>

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

                    <a href="{{ route('pinjaman.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm transition">Ajukan Pinjaman Baru</a>

                    <div class="overflow-x-auto mt-6 rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Tgl. Pengajuan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Anggota</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Jenis Pinjaman</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Pokok Pinjaman (Rp)</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Tenor (Bulan)</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Total Bunga (Rp)</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Sisa Pinjaman (Rp)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($pinjamans as $pinjaman)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ \Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">{{ $pinjaman->anggota->nama_lengkap ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">{{ ucwords(str_replace('_', ' ', $pinjaman->loan_type)) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r text-right">Rp {{ number_format($pinjaman->jumlah_pinjaman, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r text-right">{{ $pinjaman->tenor }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r text-right">Rp {{ number_format($pinjaman->bunga, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 border-r text-right">Rp {{ number_format($pinjaman->sisa_pinjaman, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r">
                                        @php
                                            $statusClass = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ][$pinjaman->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucwords($pinjaman->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if ($pinjaman->status == 'pending')
                                            @if(Auth::user()->isAdmin())
                                                <a href="{{ route('pinjaman.edit', $pinjaman->id) }}" class="text-indigo-600 hover:text-indigo-900">Setujui/Tolak</a>
                                            @else
                                                <a href="{{ route('pinjaman.show', $pinjaman->id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                            @endif
                                        @else
                                            <a href="{{ route('pinjaman.show', $pinjaman->id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pinjamans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>