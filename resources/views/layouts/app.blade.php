<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIKEU MTs')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">

<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-50 border-r border-gray-200 flex flex-col justify-between">
        <div>
            <!-- Header sidebar -->
            <div class="flex items-center gap-3 p-4">
                <div class="w-10 h-10 bg-green-600 rounded-md flex items-center justify-center text-white font-bold">SI</div>
                <div>
                    <div class="text-sm font-semibold">SIKEU MTs</div>
                    <div class="text-xs text-gray-500">Sistem Keuangan</div>
                </div>
            </div>

            <!-- Main menu -->
            <nav class="mt-4 px-2 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-green-100 text-green-700 font-semibold' : 'hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <!-- Transaksi -->
                <a href="{{ route('admin.transaksi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('admin.transaksi.*') ? 'bg-green-100 text-green-700 font-semibold' : 'hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6"/></svg>
                    <span>Transaksi</span>
                    @if(isset($pending_count) && $pending_count > 0)
                        <span class="ml-auto inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-400 text-xs font-medium text-yellow-800">{{ $pending_count }}</span>
                    @endif
                </a>

                <!-- Data Siswa -->
                @if (\Illuminate\Support\Facades\Route::has('admin.siswa.index'))
                    <a href="{{ route('admin.siswa.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('admin.siswa.*') ? 'bg-green-100 text-green-700 font-semibold' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.646 4 4 0 010-8.646zM7 14c-1.656 0-3 1.343-3 3v3h14v-3c0-1.657-1.343-3-3-3H7z"/></svg>
                        <span>Data Siswa</span>
                    </a>
                @endif

                <!-- Laporan section -->
                <div class="mt-6 px-3 text-xs text-gray-500 uppercase font-semibold">Laporan</div>

                @if (\Illuminate\Support\Facades\Route::has('admin.laporan.index'))
                    <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('admin.laporan.*') ? 'bg-green-100 text-green-700 font-semibold' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5 2-2 3 3 7-7 2 2-9 9z"/></svg>
                        <span>Laporan Keuangan</span>
                    </a>
                @endif

            </nav>
        </div>

        <!-- Sidebar footer (user) -->
        <div class="p-4 border-t mt-auto">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ auth()->user()->role === 'kepsek' ? 'Kepala Sekolah' : 'Bendahara' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-red-500 hover:text-red-700 text-sm font-medium transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
        <!-- Topbar -->
        <header class="h-16 border-b border-gray-200 flex items-center justify-between px-6">
            <div>
                <div class="text-lg font-semibold">@yield('title', 'Dashboard')</div>
                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
            </div>

            <div class="flex items-center gap-3">
                <button class="p-2 rounded-full hover:bg-gray-100" title="Notifikasi">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </button>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button" class="flex items-center gap-2 rounded-full hover:bg-gray-100 px-2 py-1 transition-colors">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-green-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A8 8 0 1118.879 6.196 8 8 0 015.121 17.804zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        <svg class="hidden sm:block h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-cloak x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-56 overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5 z-50">
                        <div class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->role === 'kepsek' ? 'Kepala Sekolah' : 'Bendahara' }}</p>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-3 text-left text-sm text-red-500 hover:bg-gray-50 hover:text-red-700 transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content area -->
        <main class="p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

</body>
</html>
