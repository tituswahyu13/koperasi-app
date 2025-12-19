<div :class="{'w-64': sidebarOpen, 'w-20': !sidebarOpen}" class="bg-gray-800 text-white min-h-screen p-4 flex flex-col shadow-lg transition-all duration-300 ease-in-out">
    <div class="text-2xl font-bold text-center mb-6 overflow-hidden">
        <div :class="{'opacity-100': sidebarOpen, 'opacity-0': !sidebarOpen}">
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-green-400">Tirta Raharja</span>
        </div>
        <div :class="{'opacity-0': sidebarOpen, 'opacity-100': !sidebarOpen, 'mt-[-30px]': !sidebarOpen}" class="transition-all duration-300 ease-in-out">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto mx-auto drop-shadow-lg">
        </div>
    </div>

    <nav class="flex-1 space-y-2">
        <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition duration-200">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-10v10a1 1 0 01-1 1h-3m-10 0h16"></path>
            </svg>
            <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Dashboard</span>
        </a>
        @if(Auth::user()->hasPermission('manage_anggota'))
        <a href="{{ route('anggota.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition duration-200">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Manajemen Anggota</span>
        </a>
        @endif

        @if(Auth::user()->hasPermission('manage_simpanan'))
        <a href="{{ route('simpanan.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition duration-200">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8zM7 8V6a2 2 0 012-2h6a2 2 0 012 2v2"></path>
            </svg>
            <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Manajemen Simpanan</span>
        </a>
        @endif

        @if(Auth::user()->hasPermission('manage_pinjaman'))
        <a href="{{ route('pinjaman.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition duration-200">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zM12 16a7.7 7.7 0 01-5-1.78l-4 4V13a9 9 0 1118 0v4l-4-4a7.7 7.7 0 01-5 1.78z"></path>
            </svg>
            <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Manajemen Pinjaman</span>
        </a>
        @endif

        @if(Auth::user()->hasPermission('manage_transaksi'))
        {{-- LINK BARU: Jurnal Umum Operasional --}}
        <a href="{{ route('general_transactions.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition duration-200">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10h-6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Jurnal Umum Operasional</span>
        </a>
        {{-- END LINK BARU --}}
        @endif

        @if(Auth::user()->hasPermission('manage_closing'))
        <a href="{{ route('closing.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition duration-200">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h.01M17 11h.01M12 7v4m0 0v4m0-4h4m-4 0h-4m4 0h4m-4 0h-4M7 16h.01M17 16h.01M12 16h.01"></path>
            </svg>
            <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Tutup Bulan</span>
        </a>
        @endif

        @if(Auth::user()->hasPermission('manage_laporan'))
        <div class="relative">
            <button @click="laporanOpen = !laporanOpen" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-700 transition duration-200 focus:outline-none">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6"></path>
                    </svg>
                    <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Laporan</span>
                </div>
                <svg :class="{'rotate-180': laporanOpen, 'hidden': !sidebarOpen}" class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="laporanOpen && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" class="pl-11 space-y-1 mt-1">
                <a href="{{ route('laporan.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-700 transition duration-200">Index Laporan</a>
                <a href="{{ route('laporan.simpanan') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-700 transition duration-200">Simpanan</a>
                <a href="{{ route('laporan.pinjaman') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-700 transition duration-200">Pinjaman</a>
                <a href="{{ route('laporan.arus-kas') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-700 transition duration-200">Arus Kas</a>
                <a href="{{ route('laporan.neraca') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-700 transition duration-200">Neraca</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->hasPermission('manage_simulasi'))
        <a href="{{ route('simulasi.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition duration-200">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zM12 16a7.7 7.7 0 01-5-1.78l-4 4V13a9 9 0 1118 0v4l-4-4a7.7 7.7 0 01-5 1.78z"></path>
            </svg>
            <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Simulasi Pinjaman</span>
        </a>
        @endif

        @if(Auth::user()->hasPermission('manage_roles'))
        <a href="{{ route('roles.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition duration-200">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Manajemen Akses</span>
        </a>
        @endif
    </nav>

    <div class="mt-auto">
        <a href="{{ route('profile.edit') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition duration-200">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A11.968 11.968 0 0112 15c2.956 0 5.672.932 7.779 2.455M12 12a3 3 0 100-6 3 3 0 000 6z"></path>
            </svg>
            <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Profil</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left p-3 rounded-lg flex items-center hover:bg-gray-700 transition duration-200 mt-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span :class="{'hidden': !sidebarOpen, 'flex': sidebarOpen}" class="ml-3 transition-all duration-300 ease-in-out whitespace-nowrap overflow-hidden">Log Out</span>
            </button>
        </form>
    </div>
</div>