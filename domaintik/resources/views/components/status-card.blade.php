@props(['title', 'count', 'icon' => null, 'color' => 'blue'])

@php
    $colorClasses = match($color) {
        'green' => 'bg-green-100 text-green-600',
        'red' => 'bg-red-100 text-red-600',
        'yellow' => 'bg-yellow-100 text-yellow-600',
        default => 'bg-blue-100 text-blue-600',
    };
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center space-x-4">
    <div class="p-3 rounded-full {{ $colorClasses }}">
        {{ $icon ?? '📦' }} 
    </div>
    <div>
        <div class="text-sm font-medium text-gray-500">{{ $title }}</div>
        <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
    </div>
</div>