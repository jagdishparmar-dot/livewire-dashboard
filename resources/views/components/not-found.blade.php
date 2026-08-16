<div {{ $attributes->class('flex flex-col items-center py-10 text-center') }}>
    <p class="text-sm font-medium tracking-wide text-zinc-400">404</p>
    <flux:heading size="xl" class="mt-2">Page not found</flux:heading>
    <flux:text class="mt-2 max-w-md">
        The path
        <code class="rounded bg-zinc-100 px-1.5 py-0.5 text-sm text-zinc-700">{{ request()->getRequestUri() }}</code>
        doesn't exist or may have been moved.
    </flux:text>

    <flux:button
        :href="auth()->check() ? route('dashboard') : route('login')"
        variant="primary"
        class="mt-6"
        wire:navigate
    >
        {{ auth()->check() ? 'Back to Home' : 'Back to login' }}
    </flux:button>
</div>
