@props([
    'title',
    'description',
    'icon' => 'squares-2x2',
])

<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ $title }}</flux:heading>
        <flux:text class="mt-2">{{ $description }}</flux:text>
    </div>

    <flux:card class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-4 flex size-12 items-center justify-center rounded-xl bg-zinc-100">
            <flux:icon :name="$icon" class="size-6 text-zinc-500" />
        </div>
        <flux:heading size="lg">Coming soon</flux:heading>
        <flux:text class="mt-2 max-w-md">
            This page is a placeholder. The {{ strtolower($title) }} workspace will land here.
        </flux:text>
    </flux:card>
</div>
