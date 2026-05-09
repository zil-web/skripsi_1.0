<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - SIKEU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root { --accent: #1D9E75; }

            /* Collapsed sidebar styles */
            .sidebar { transition: width .15s ease; overflow: hidden; }
            .sidebar a { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
            .sidebar .nav-icon{ display:inline-flex; width:1.6rem; justify-content:center; }

            /* when collapsed we hide the entire sidebar and show only the outer toggle */
            .sidebar-collapsed .sidebar { display: none !important; width: 0 !important; border-right: none !important; }
            /* center outer toggle vertically */
            #sidebar-toggle-outer { top: 50%; transform: translateY(-50%); left: 0.5rem; }
            .sidebar-collapsed .sidebar .brand,
            .sidebar-collapsed .sidebar .user,
            .sidebar-collapsed .sidebar .nav-text,
            .sidebar-collapsed .sidebar .nav-subtext { display: none !important; }

            /* reduce padding so no visible sliver remains when visible */
            .sidebar-collapsed .sidebar a { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
            .sidebar-collapsed .sidebar .nav-icon { width: 2rem; }
            .sidebar-collapsed #sidebar-toggle-outer { display: flex !important; }
            .sidebar-collapsed .flex-1 { transition: margin-left .15s ease; }
        </style>
</head>
<body class="bg-gray-50">
@php
    $user = auth()->user();
    $pageTitle = trim($__env->yieldContent('page-title')) ?: trim($__env->yieldContent('title', 'Dashboard'));
    $pageSubtitle = trim($__env->yieldContent('page-subtitle'));
@endphp

<div id="app-root" class="flex h-screen overflow-hidden" style="--accent: #1D9E75;">
    <!-- outer toggle visible when sidebar fully collapsed; centered and larger -->
    <button id="sidebar-toggle-outer" class="hidden fixed left-1 z-50 rounded-full bg-white border border-gray-200 shadow-sm text-gray-700 flex items-center justify-center" title="Toggle sidebar" aria-label="Toggle sidebar outer" style="width:56px; height:56px;">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path d="M3 5h14v2H3V5zm0 4h14v2H3V9zm0 4h14v2H3v-2z"/></svg>
    </button>
    <aside class="sidebar w-60 flex-shrink-0 flex flex-col bg-white border-r border-gray-200">
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="brand w-10 h-10 rounded-xl bg-[var(--accent)] flex items-center justify-center text-white font-bold shadow-sm">SK</div>
                <div>
                    <div class="font-semibold text-sm text-gray-900">SIKEU MTs</div>
                    <div class="text-xs text-gray-500">Sistem Keuangan</div>
                </div>
                <button id="sidebar-toggle" title="Toggle sidebar" class="ml-auto rounded-md p-1 text-gray-600 hover:bg-gray-100" aria-label="Toggle sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm1 5a1 1 0 100 2h12a1 1 0 100-2H4z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        </div>

        <div class="user p-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold text-gray-700">{{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}</div>
            <div class="min-w-0 flex-1">
                <div class="font-semibold text-sm text-gray-900 truncate">{{ $user->name ?? 'Admin' }}</div>

            </div>
        </div>

        <nav class="flex-1 p-3 overflow-y-auto space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="nav-icon" aria-hidden="true"></span>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.pemasukan.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.pemasukan.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="nav-icon" aria-hidden="true"></span>
                <span class="nav-text">Pemasukan</span>
            </a>
            <a href="{{ route('admin.pengeluaran.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.pengeluaran.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="nav-icon" aria-hidden="true"></span>
                <span class="nav-text">Pengeluaran</span>
            </a>
            <a href="{{ route('admin.transaksi.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.transaksi.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="nav-icon" aria-hidden="true"></span>
                <span class="nav-text">Transaksi</span>
            </a>
            <a href="{{ route('admin.siswa.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('admin.siswa.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="nav-icon" aria-hidden="true"></span>
                <span class="nav-text">Data Siswa</span>
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
            @if(session('error'))
                <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif
        </div>

        <main class="flex-1 overflow-y-auto p-6">@yield('content')</main>
    </div>
</div>

@stack('scripts')
<script>
    (function(){
        const root = document.getElementById('app-root');
        const toggle = document.getElementById('sidebar-toggle');
        const outerToggle = document.getElementById('sidebar-toggle-outer');
        const key = 'sikeu.sidebar.collapsed';

        function applyState(collapsed) {
            if(!root) return;
            if(collapsed) root.classList.add('sidebar-collapsed'); else root.classList.remove('sidebar-collapsed');
        }

        try{
            const stored = localStorage.getItem(key);
            applyState(stored === '1');
        }catch(e){}

        function bindToggle(btn){
            if(!btn) return;
            btn.addEventListener('click', function(){
                const collapsed = root.classList.toggle('sidebar-collapsed');
                try{ localStorage.setItem(key, collapsed ? '1' : '0'); }catch(e){}
            });
        }

        bindToggle(toggle);
        bindToggle(outerToggle);
    })();
</script>
</body>
</html>
