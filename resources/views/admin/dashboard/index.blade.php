npx newman run docs/postman/SIKEU.postman_collection.json -e docs/postman/SIKEU.local.postman_environment.json --reporters cli@extends('layouts.app')

@section('title','Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Statistik cards --}}
    @include('admin.dashboard._stat-cards')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Bar chart --}}
        @include('admin.dashboard._chart-bar')

        {{-- Donut chart --}}
        @include('admin.dashboard._chart-donut')
    </div>

    {{-- Latest transaksi table --}}
    @include('admin.dashboard._latest-transaksi')

</div>
@endsection
