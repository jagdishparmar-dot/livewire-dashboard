@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" style="color-scheme: light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Welcome' }} · {{ config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-zinc-50 antialiased">
        <div class="flex min-h-screen items-center justify-center p-6">
            <div class="w-full max-w-md space-y-6">
                <div class="flex justify-center">
                    <flux:brand :href="route('login')" :name="config('app.name')" wire:navigate>
                        <x-slot:logo class="bg-zinc-900 text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M13.6 2.2L3.2 14.6h7.2L9.2 21.8l11.6-13.8h-7.4l.2-5.8z" />
                            </svg>
                        </x-slot:logo>
                    </flux:brand>
                </div>

                {{ $slot }}
            </div>
        </div>

        @livewireScripts
        @fluxScripts
    </body>
</html>
