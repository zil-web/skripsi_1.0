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
    $kepsek = auth()->guard('kepsek')->user() ?? auth()->user();
    $kepsekName = $kepsek->nama ?? $kepsek->name ?? 'Kepala Sekolah';
    $authUser = auth()->user();
    $pendingCount = \App\Models\EditRequest::where('status', 'pending')->count()
        + \App\Models\Transaksi::where('tipe', 'pengeluaran')->where('status', 'pending')->count();
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
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold text-gray-700">{{ strtoupper(substr($kepsekName, 0, 1)) }}</div>
            <div class="min-w-0 flex-1">
                <div class="font-semibold text-sm text-gray-900 truncate">{{ $kepsekName }}</div>
            </div>
        </div>

        <nav class="flex-1 p-3 overflow-y-auto space-y-1">
            <a href="{{ route('kepsek.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('kepsek.dashboard') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span>Dashboard</span>
            </a>
            <a href="{{ route('kepsek.transaksi') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('kepsek.transaksi') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span>Transaksi</span>
            </a>
            <a href="{{ route('kepsek.approvals') }}" class="flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('kepsek.approvals') || request()->routeIs('kepsek.edit-request.*') || request()->routeIs('kepsek.pengeluaran.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span>Approval</span>
                <span class="relative inline-flex items-center justify-center w-5 h-5 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if((($authUser && $authUser->role === 'kepsek') || auth()->guard('kepsek')->check()) && $pendingCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 min-w-[16px] h-4 px-1 rounded-full bg-red-600 text-white text-[10px] leading-4 text-center">{{ $pendingCount }}</span>
                    @endif
                </span>
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
            </div>
        </header>

        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="mb-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('info'))
                <div class="mb-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
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
