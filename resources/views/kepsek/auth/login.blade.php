@extends('layouts.kepsek-guest')

@section('content')
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: #534AB7;">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">SIKEU</h1>
        <p class="text-sm text-gray-600 mt-1">Sistem Keuangan Sekolah</p>
        <p class="text-xs text-gray-500 mt-2">Login Kepala Sekolah</p>
    </div>

    <!-- Display Errors -->
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg" style="background-color: #FEE2E2; border-left: 4px solid #EF4444;">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-red-800">Login Gagal</h3>
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-700 mt-1">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('kepsek.login') }}" class="space-y-6">
        @csrf

        <!-- Username Field -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                Username
            </label>
            <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                placeholder="Masukkan username Anda"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition"
                style="focus:ring-color: #534AB7;"
                required
            >
            @error('username')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password
            </label>
            <div class="relative" x-data="{ showPassword: false }">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    name="password"
                    placeholder="Masukkan password Anda"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition"
                    style="focus:ring-color: #534AB7;"
                    required
                >
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition"
                >
                    <svg
                        x-show="!showPassword"
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg
                        x-show="showPassword"
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.604-1.888A3.375 3.375 0 1015.75 9.75 3.375 3.375 0 0012 6.375zm6.9 1.035A10.05 10.05 0 0112 5c-4.477 0-8.268 2.943-9.543 7a9.97 9.97 0 011.921 3.46m5.944-5.456A4.5 4.5 0 1015.75 9m-4.5 3c.967.025 1.922.104 2.857.23m5.385-1.993a10.05 10.05 0 01-9.284 5.99m9.284-5.99c-.464.04-.932.082-1.404.128"></path>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Login Button -->
        <button
            type="submit"
            class="w-full py-2 px-4 rounded-lg font-semibold text-white transition duration-200 hover:opacity-90"
            style="background-color: #534AB7;"
        >
            Masuk sebagai Kepala Sekolah
        </button>
    </form>

    <!-- Footer -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-center text-sm text-gray-600">
            Bukan Kepala Sekolah?
            <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color: #534AB7;">
                Login sebagai Admin
            </a>
        </p>
    </div>
@endsection
