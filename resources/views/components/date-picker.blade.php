@props([
    'label' => null,
    'placeholder' => 'Select a date',
])

@php
    $errorName = $attributes->wire('model')->value();
@endphp

<flux:field>
    @if ($label)
        <flux:label>{{ $label }}</flux:label>
    @endif

    <div
        x-data="datePicker"
        x-modelable="value"
        {{ $attributes->except('class') }}
        class="relative {{ $attributes->get('class') }}"
        @keydown.escape.window="open = false"
    >
        <button
            type="button"
            @click="open = !open"
            class="relative flex h-10 w-full items-center rounded-lg border border-zinc-200 border-b-zinc-300/80 bg-white px-3 text-start text-sm shadow-xs outline-none hover:bg-zinc-50 focus:ring-2 focus:ring-accent focus:ring-offset-2"
        >
            <span class="min-w-0 flex-1 truncate" :class="value ? 'text-zinc-700' : 'text-zinc-400'">
                <span x-text="display || @js($placeholder)"></span>
            </span>
            <flux:icon name="calendar" variant="mini" class="size-4 text-zinc-400" />
        </button>

        <div
            x-show="open"
            x-cloak
            @click.outside="open = false"
            class="absolute z-50 mt-2 w-72 rounded-xl border border-zinc-200 bg-white p-4 shadow-lg ring-1 ring-black/5"
        >
            <div class="mb-3 flex items-center justify-between">
                <flux:button type="button" variant="subtle" icon="chevron-left" size="sm" @click="prev()" />
                <p class="text-sm font-medium text-zinc-800" x-text="title"></p>
                <flux:button type="button" variant="subtle" icon="chevron-right" size="sm" @click="next()" />
            </div>

            <div class="mb-1 grid grid-cols-7 gap-1">
                <template x-for="day in weekdays" :key="day">
                    <div class="py-1 text-center text-xs font-medium text-zinc-400" x-text="day"></div>
                </template>
            </div>

            <div class="grid grid-cols-7 gap-1">
                <template x-for="(date, index) in cells" :key="index">
                    <div class="h-9">
                        <button
                            type="button"
                            x-show="date"
                            @click="pick(date)"
                            class="flex h-9 w-full items-center justify-center rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-100"
                            :class="{
                                'bg-zinc-800 text-white hover:bg-zinc-800': isSelected(date),
                                'ring-1 ring-zinc-300': isToday(date) && ! isSelected(date),
                            }"
                            x-text="date ? date.getDate() : ''"
                        ></button>
                    </div>
                </template>
            </div>

            <div class="mt-3 flex justify-end border-t border-zinc-100 pt-3">
                <flux:button type="button" variant="ghost" size="sm" @click="clear()">Clear</flux:button>
            </div>
        </div>
    </div>

    @if ($errorName)
        <flux:error :name="$errorName" />
    @endif
</flux:field>
