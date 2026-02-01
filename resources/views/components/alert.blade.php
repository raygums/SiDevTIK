@props(['type' => 'info', 'message' => ''])

@php
$configs = [
    'success' => [
        'border' => 'border-success',
        'bg' => 'bg-success-light',
        'text' => 'text-success',
        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    ],
    'error' => [
        'border' => 'border-error',
        'bg' => 'bg-error-light',
        'text' => 'text-error',
        'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    ],
    'warning' => [
        'border' => 'border-warning',
        'bg' => 'bg-warning-light',
        'text' => 'text-warning',
        'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
    ],
    'info' => [
        'border' => 'border-info',
        'bg' => 'bg-info-light',
        'text' => 'text-info',
        'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    ],
];

$config = $configs[$type] ?? $configs['info'];
@endphp

<div class="overflow-hidden rounded-xl border {{ $config['border'] }} {{ $config['bg'] }} shadow-sm">
    <div class="flex items-start gap-4 p-4">
        <svg class="h-6 w-6 flex-shrink-0 {{ $config['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
        </svg>
        <p class="text-sm font-medium text-gray-900">{{ $message ?: $slot }}</p>
    </div>
</div>
