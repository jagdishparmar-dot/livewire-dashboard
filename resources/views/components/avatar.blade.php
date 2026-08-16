@props([
    'user',
    'size' => 'h-9 w-9',
])

@if ($user->profile_photo_path)
    <img
        src="{{ $user->profile_photo_url }}"
        alt="{{ $user->name }}"
        {{ $attributes->merge(['class' => $size.' rounded-full object-cover']) }}
    >
@else
    <span {{ $attributes->merge(['class' => $size.' inline-flex items-center justify-center rounded-full bg-brand/20 text-sm font-semibold text-brand']) }}>
        {{ $user->initials() }}
    </span>
@endif
