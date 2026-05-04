<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kepala Sekolah') - SIKEU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --accent: #1D9E75; }
    </style>
</head>
<body class="bg-gray-50">
@php
    $kepsek = auth()->guard('kepsek')->user();
    $pageTitle = trim($__env->yieldContent('page-title')) ?: trim($__env->yieldContent('title', 'Dashboard'));
    $pageSubtitle = trim($__env->yieldContent('page-subtitle'));
@endphp

<div class="flex h-screen overflow-hidden" style="--accent: #1D9E75;">
    <aside class="w-60 flex-shrink-0 flex flex-col bg-white border-r border-gray-200">
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[var(--accent)] flex items-center justify-center text-white font-bold shadow-sm">SK</div>
                <div>
                    <div class="font-semibold text-sm text-gray-900">SIKEU MTs</div>
                    <div class="text-xs text-gray-500">Sistem Keuangan</div>
                </div>
            </div>
        </div>

        <div class="p-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold text-gray-700">{{ strtoupper(substr($kepsek->nama ?? 'K', 0, 1)) }}</div>
            <div class="min-w-0 flex-1">
                <div class="font-semibold text-sm text-gray-900 truncate">{{ $kepsek->nama ?? 'Kepala' }}</div>
                <div class="mt-1 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" style="background:rgba(29,158,117,0.12); color:var(--accent);">Kepala Sekolah</div>
            </div>
        </div>

        <nav class="flex-1 p-3 overflow-y-auto space-y-1">
            <a href="{{ route('kepsek.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('kepsek.dashboard') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span>Dashboard</span>
            </a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors text-gray-700 hover:bg-gray-100">
                <span>Validasi Transaksi</span>
            </a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors text-gray-700 hover:bg-gray-100">
                <span>Profil</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-lg px-3 py-2 text-sm font-medium text-white transition-colors" style="background:var(--accent);">Keluar</button>
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
                    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-700">{{ strtoupper(substr($kepsek->nama ?? 'K', 0, 1)) }}</div>
                    <span class="hidden sm:block font-medium text-gray-700">{{ $kepsek->nama ?? 'Kepala Sekolah' }}</span>
                </div>
            </div>
        </header>

        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="mb-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif
        </div>

        <main class="flex-1 overflow-y-auto p-6">@yield('content')</main>
    </div>
</div>
</body>
</html>
