@extends('frontend.layouts.app')

@section('title', 'Test Barang Livewire')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-4">Test Halaman</h1>
    
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Debug Info</h2>
        <p>Laravel Version: {{ app()->version() }}</p>
        <p>Environment: {{ app()->environment() }}</p>
        <p>Debug Mode: {{ config('app.debug') ? 'ON' : 'OFF' }}</p>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Livewire Component Test</h2>
        <p>Component akan dimuat di bawah ini:</p>
        
        @livewire('barang-list')
    </div>
</div>
@endsection
