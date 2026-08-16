@props([
    'label' => null,
    'placeholder' => 'Select...',
    'options' => [],
])

@php
    $errorName = $attributes->wire('model')->value();
@endphp

<flux:field>
    @if ($label)
        <flux:label>{{ $label }}</flux:label>
    @endif

    <div
        x-data="{
            value: '',
            options: {{ \Illuminate\Support\Js::from($options) }},
            get selectedLabel() {
                return this.options[this.value] ?? @js($placeholder)
            },
        }"
        x-modelable="value"
        {{ $attributes->except('class') }}
        class="{{ $attributes->get('class') }}"
    >
        <flux:dropdown class="w-full">
            <flux:button
                type="button"
                variant="outline"
                icon:trailing="chevron-down"
                align="start"
                class="w-full"
            >
                <span class="min-w-0 flex-1 truncate text-start" x-text="selectedLabel"></span>
            </flux:button>

            <flux:menu class="w-full min-w-40">
                @foreach ($options as $optionValue => $optionLabel)
                    <flux:menu.item
                        as="button"
                        type="button"
                        @click="value = @js((string) $optionValue)"
                    >
                        <span class="flex w-full items-center gap-2">
                            <span class="grid size-4 place-items-center">
                                <span x-cloak x-show="value === @js((string) $optionValue)">
                                    <flux:icon name="check" variant="mini" class="size-4" />
                                </span>
                            </span>
                            {{ $optionLabel }}
                        </span>
                    </flux:menu.item>
                @endforeach
            </flux:menu>
        </flux:dropdown>
    </div>

    @if ($errorName)
        <flux:error :name="$errorName" />
    @endif
</flux:field>
