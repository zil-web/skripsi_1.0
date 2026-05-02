<x-guest-layout>
<div class="min-h-screen grid grid-cols-1 md:grid-cols-5">
    <div class="hidden md:block md:col-span-2 bg-[#085041] text-white p-10">
        <div class="h-full flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center">
                        <svg class="w-7 h-7 text-[#085041]" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 2l3 7h7l-5.5 4 2 7L12 16l-6.5 4 2-7L2 9h7l3-7z" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">SIKEU MTs</h2>
                        <p class="text-sm text-green-200">Sistem Informasi Keuangan Madrasah</p>
                    </div>
                </div>

                <div class="mt-8">
                    <svg class="w-full h-64 opacity-80" viewBox="0 0 600 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="0" y="0" width="600" height="400" rx="12" fill="#0A5A44" />
                        <circle cx="120" cy="120" r="50" fill="#0F7A63" />
                        <rect x="220" y="80" width="240" height="140" rx="12" fill="#0B6F55" />
                        <path d="M50 320 L150 220 L250 320" stroke="#0F7A63" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                    </svg>
                </div>
            </div>

            <div class="text-sm text-green-200">
                <p>Keamanan data terjaga. Jika bermasalah hubungi admin.</p>
            </div>
        </div>
    </div>

    <div class="col-span-1 md:col-span-3 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            @if(session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-800">
                    <strong class="block">Login gagal</strong>
                    <ul class="mt-1 text-sm">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Selamat Datang</h1>
            <p class="text-sm text-gray-500 mb-6">Masuk ke sistem keuangan sekolah</p>

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1"/></svg>
                        </span>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                               class="block w-full pl-10 pr-3 py-2 border rounded shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:border-[#1D9E75]" />
                    </div>
                    @error('username')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zM5 20v-2a7 7 0 017-7h0a7 7 0 017 7v2"/></svg>
                        </span>
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                               class="block w-full pl-10 pr-10 py-2 border rounded shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:border-[#1D9E75]" />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6">
                    <button type="submit" class="w-full py-2 px-4 bg-[#1D9E75] text-white rounded font-semibold hover:opacity-95">Masuk</button>
                </div>

                <p class="text-xs text-gray-500 text-center">Lupa password? Hubungi administrator</p>
            </form>
        </div>
    </div>
</div>
</x-guest-layout>