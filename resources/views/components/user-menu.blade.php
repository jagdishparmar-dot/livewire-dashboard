@props([
    'sidebar' => false,
    'named' => true,
])

@php
    $user = auth()->user();
@endphp

<flux:dropdown position="top" align="start" {{ $attributes }}>
    @if ($sidebar)
        <flux:sidebar.profile
            :avatar="$user->profile_photo_url"
            :name="$named ? $user->name : null"
            :initials="$user->initials()"
        />
    @else
        <flux:profile
            :avatar="$user->profile_photo_url"
            :name="$named ? $user->name : null"
            :initials="$user->initials()"
        />
    @endif

    <flux:menu>
        <flux:menu.item :href="route('profile')" icon="user" wire:navigate>
            Profile
        </flux:menu.item>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item
                as="button"
                type="submit"
                icon="arrow-right-start-on-rectangle"
                class="w-full cursor-pointer"
            >
                Log out
            </flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
