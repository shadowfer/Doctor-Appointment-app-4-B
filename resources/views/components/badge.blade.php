@props(['color' => 'gray', 'text'])

@php
    $colors = [
        'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    ];

    $classes = "text-xs font-medium me-2 px-2.5 py-0.5 rounded " . ($colors[$color] ?? $colors['gray']);
@endphp

<span class="{{ $classes }}">{{ $text }}</span>