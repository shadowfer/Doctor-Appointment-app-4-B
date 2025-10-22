@props([
    'title' => config('app.name', 'Laravel'),
    'breadcrumbs' => []])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://kit.fontawesome.com/a7de8752fc.js" crossorigin="anonymous"></script>

        <wireui:scripts />
        {{-- Livewire Scripts --}}
        @livewireScripts

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50">
        @include('layouts.includes.admin.navigation')

        @include('layouts.includes.admin.sidebar')

        <div class="p-4 sm:ml-64">
            <!-- Margin top 14px -->
            <div class="mt-14">

                {{-- Breadcrumbs --}}
                @if (count($breadcrumbs))
                    <nav class="flex px-5 py-3 text-gray-700 border border-gray-200 rounded-lg bg-white" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                    <i class="fa-solid fa-house mr-2"></i>
                                    Home
                                </a>
                            </li>
                            @foreach ($breadcrumbs as $breadcrumb)
                                <li>
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-chevron-right text-gray-400"></i>
                                        <a href="{{ $breadcrumb['href'] ?? '#' }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">{{ $breadcrumb['name'] }}</a>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif


            <div class="mt-14">
                {{ $slot }}
            </div>
        </div>

        @stack('modals')

        {{-- WireUI Scripts --}}

    </body>