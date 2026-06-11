<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ERP Umroh' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ sidebarOpen: window.innerWidth >= 1024 }" x-cloak
    class="min-h-screen bg-slate-50 text-slate-900 font-sans antialiased overflow-x-hidden">

    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-40 w-64 shrink-0 lg:z-30">

        @include('components.layouts.partials.sidebar')
    </div>

    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        class="fixed inset-0 bg-slate-900/20 backdrop-blur-xs z-30 lg:hidden" x-transition:opacity></div>

    <div class="min-h-screen flex flex-col transition-all duration-200" :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-0'">

        @include('components.layouts.partials.header-mobile')
        @include('components.layouts.partials.header-desktop')

        <main class="w-full p-6 flex-1 box-border">
            <div class="max-w-7xl mx-auto space-y-6">

                @if (isset($title))
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-2 border-b border-slate-100">
                        <div>
                            <flux:heading size="xl" level="1"
                                class="font-extrabold text-slate-900 tracking-tight">
                                {{ $title }}
                            </flux:heading>

                            @if (isset($subtitle) && !empty($subtitle))
                                <flux:subheading class="text-xs text-slate-500 mt-0.5">
                                    {{ $subtitle }}
                                </flux:subheading>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="w-full block clear-both">
                    {{ $slot }}
                </div>

            </div>
        </main>
    </div>

    <x-ui.toast />
</body>

</html>
