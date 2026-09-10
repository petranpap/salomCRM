<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }"
      x-init="$watch('darkMode', v => { localStorage.setItem('darkMode', v); document.documentElement.classList.toggle('dark', v) })"
      :class="{ 'dark': darkMode }"
      x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Studio Kassandra') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans bg-ts-bg dark:bg-cream-dark text-ts-text dark:text-cream min-h-screen">

    <div class="flex min-h-screen">
        {{-- Mobile sidebar overlay --}}
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-20 bg-black/40 lg:hidden"></div>

        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Main area --}}
        <div class="flex-1 flex flex-col min-w-0">
            @include('components.topbar')

            <main class="flex-1 p-6 print:p-0">
                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="mb-4 px-4 py-3 rounded-card bg-green-50 border border-green-200 text-green-800 text-sm print:hidden">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 px-4 py-3 rounded-card bg-red-50 border border-red-200 text-red-800 text-sm print:hidden">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 px-4 py-3 rounded-card bg-red-50 border border-red-200 text-red-800 text-sm print:hidden">
                        <p class="font-semibold mb-1">Please fix the following:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
