<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::guest')] #[Title('Sign up')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = User::create($validated);

        event(new Registered($user));

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }
};
?>

<flux:card>
    <flux:heading size="lg">Create an account</flux:heading>
    <flux:text class="mb-6 mt-2">Sign up to open your project dashboard.</flux:text>

    <form wire:submit="register" class="space-y-6">
        <flux:input
            label="Name"
            wire:model="name"
            autocomplete="name"
            autofocus
            placeholder="Your name"
        />

        <flux:input
            label="Email"
            type="email"
            wire:model="email"
            autocomplete="username"
            placeholder="you@example.com"
        />

        <flux:input
            label="Password"
            type="password"
            wire:model="password"
            autocomplete="new-password"
            placeholder="At least 8 characters"
            viewable
        />

        <flux:input
            label="Confirm password"
            type="password"
            wire:model="password_confirmation"
            autocomplete="new-password"
            placeholder="Repeat your password"
            viewable
        />

        <flux:button type="submit" variant="primary" class="w-full">
            Create account
        </flux:button>
    </form>

    <flux:text class="mt-6 text-center">
        Already have an account?
        <flux:link href="{{ route('login') }}" wire:navigate>Log in</flux:link>
    </flux:text>
</flux:card>
