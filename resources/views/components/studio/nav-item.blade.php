@props([
    'href' => null,
    'active' => false,
    'icon',
    'label',
])

@php
    $classes = $active
        ? 'bg-white/[0.08] font-medium text-white'
        : ($href ? 'text-neutral-400 hover:bg-white/[0.05] hover:text-white' : 'cursor-default text-neutral-500');
@endphp

@if ($href)
    <a href="{{ $href }}" wire:navigate {{ $attributes->merge(['class' => "flex items-center gap-2.5 rounded-md px-2 py-1.5 text-[13px] {$classes}"]) }}>
        <x-icon :name="$icon" @class(['h-4 w-4 shrink-0', 'text-brand' => $active]) />
        <span class="truncate">{{ $label }}</span>
    </a>
@else
    <span {{ $attributes->merge(['class' => "flex items-center gap-2.5 rounded-md px-2 py-1.5 text-[13px] {$classes}"]) }}>
        <x-icon :name="$icon" class="h-4 w-4 shrink-0" />
        <span class="truncate">{{ $label }}</span>
    </span>
@endif
