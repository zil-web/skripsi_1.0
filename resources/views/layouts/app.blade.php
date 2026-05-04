<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIKEU MTs')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
@php
    $user = auth()->user();
    $pageTitle = trim($__env->yieldContent('page-title')) ?: trim($__env->yieldContent('title', 'Dashboard'));
    $pageSubtitle = trim($__env->yieldContent('page-subtitle'));
@endphp

<div class="flex h-screen overflow-hidden" style="--accent: #1D9E75;">
    <aside class="w-60 flex-shrink-0 flex flex-col bg-white border-r border-gray-200">
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-600 flex items-center justify-center text-white font-bold shadow-sm">SI</div>
                <div>
                    <div class="text-sm font-semibold text-gray-900">SIKEU MTs</div>
                    <div class="text-xs text-gray-500">Sistem Keuangan</div>
                </div>
            </div>
        </div>

        <div class="p-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold text-gray-700">{{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}</div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-semibold text-gray-900 truncate">{{ $user->name ?? 'Admin' }}</div>
                <div class="mt-1 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-50 text-green-700">Bendahara</div>
            </div>
        </div>

        <nav class="flex-1 p-3 overflow-y-auto space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.pemasukan.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.pemasukan.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span>Pemasukan</span>
            </a>
            <a href="{{ route('admin.pengeluaran.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.pengeluaran.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span>Pengeluaran</span>
            </a>
            <a href="{{ route('admin.transaksi.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.transaksi.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span>Transaksi</span>
            </a>
            @if(Route::has('admin.siswa.index'))
                <a href="{{ route('admin.siswa.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.siswa.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span>Data Siswa</span>
                </a>
            @endif
        </nav>

        <div class="p-4 border-t border-gray-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-lg px-3 py-2 text-sm font-medium text-white transition-colors bg-green-600">Keluar</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">{{ $pageTitle }}</h1>
                @if($pageSubtitle !== '')
                    <p class="text-sm text-gray-500 mt-1">{{ $pageSubtitle }}</p>
                @endif
            </div>

            <div class="flex items-center gap-3 text-sm text-gray-600">
                <div class="rounded-full bg-gray-100 px-3 py-1.5">{{ now()->format('d M Y') }}</div>
                <div class="flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5">
                    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-700">{{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}</div>
                    <span class="hidden sm:block font-medium text-gray-700">{{ $user->name ?? 'Admin' }}</span>
                </div>
            </div>
        </header>

        <main class="p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

</body>
</html>
