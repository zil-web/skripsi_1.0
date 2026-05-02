<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - SIKEU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --accent: #1D9E75; }
    </style>
</head>
<body>
<div class="flex h-screen overflow-hidden bg-gray-50" style="--accent: #1D9E75;">
    <!-- Sidebar -->
    <aside class="w-56 flex-shrink-0 flex flex-col bg-white border-r">
        <!-- Brand -->
        <div class="p-4 flex items-center gap-3 border-b">
            <div class="w-10 h-10 rounded bg-[var(--accent)] flex items-center justify-center text-white font-bold">SK</div>
            <div>
                <div class="font-bold">SIKEU MTs</div>
                <div class="text-xs text-gray-500">Sistem Keuangan</div>
            </div>
        </div>

        <!-- User identity -->
        @php $user = auth()->user(); @endphp
        <div class="p-4 border-b flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-lg font-semibold text-gray-700">{{ strtoupper(substr($user->name ?? 'A',0,1)) }}</div>
            <div class="flex-1">
                <div class="font-semibold text-sm">{{ $user->name ?? 'Admin' }}</div>
                <div class="text-xs mt-1 inline-flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-xs font-medium" style="background:rgba(29,158,117,0.12); color:var(--accent);">Bendahara</span>
                </div>
            </div>
        </div>

        <!-- Menu -->
        <nav class="flex-1 p-2 overflow-y-auto">
            @section('sidebar-menu')
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded mb-1" style="{{ request()->routeIs('admin.dashboard') ? 'background:var(--accent); color:white;' : '' }}">Dashboard</a>
                <a href="{{ route('admin.pemasukan.index') }}" class="block px-3 py-2 rounded mb-1" style="{{ request()->routeIs('admin.pemasukan.*') ? 'background:var(--accent); color:white;' : '' }}">Input Pemasukan</a>
                <a href="{{ route('admin.pengeluaran.index') }}" class="block px-3 py-2 rounded mb-1" style="{{ request()->routeIs('admin.pengeluaran.*') ? 'background:var(--accent); color:white;' : '' }}">Input Pengeluaran</a>
                <a href="{{ route('admin.transaksi.index') }}" class="block px-3 py-2 rounded mb-1">Transaksi</a>
                <a href="#" class="block px-3 py-2 rounded mb-1">Data Siswa</a>
                <a href="#" class="block px-3 py-2 rounded mb-1">Laporan</a>
            @show
        </nav>

        <!-- Logout -->
        <div class="p-3 border-t">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full px-3 py-2 rounded" style="background:var(--accent); color:white;">Keluar</button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Topbar -->
        <header class="bg-white border-b px-6 py-3 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold">@yield('page-title')</h1>
                <p class="text-sm text-gray-400">@yield('page-subtitle')</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button class="p-2 rounded bg-gray-100">🔔</button>
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full text-xs px-1">3</span>
                </div>
                <div class="text-sm text-gray-600">{{ now()->format('d M Y') }}</div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">{{ strtoupper(substr($user->name ?? 'A',0,1)) }}</div>
                    <div class="text-sm">{{ $user->name ?? 'Admin' }}</div>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="p-4">
            @if(session('success'))
                <div class="mb-3 p-3 rounded bg-green-50 border border-green-200 text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-3 p-3 rounded bg-red-50 border border-red-200 text-red-800">{{ session('error') }}</div>
            @endif
        </div>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto p-6">@yield('content')</main>
    </div>
</div>
</body>
</html>
