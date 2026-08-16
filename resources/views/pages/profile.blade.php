<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Profile')] class extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $job_title = '';

    public string $company = '';

    public string $location = '';

    public string $website = '';

    public string $date_of_birth = '';

    public string $bio = '';

    public $photo = null;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->fillFromUser(Auth::user());
    }

    public function updatedPhoto(): void
    {
        $this->validateOnly('photo');
    }

    public function rules(): array
    {
        return $this->profileRules(Auth::user());
    }

    public function save(): void
    {
        $user = Auth::user();

        $validated = $this->validate();

        if ($this->photo instanceof TemporaryUploadedFile) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $validated['profile_photo_path'] = $this->photo->store('avatars', 'public');
        }

        unset($validated['photo']);

        foreach (['phone', 'job_title', 'company', 'location', 'website', 'date_of_birth', 'bio'] as $field) {
            $validated[$field] = ($validated[$field] ?? '') === '' ? null : $validated[$field];
        }

        $user->update($validated);

        $this->photo = null;
        $this->fillFromUser($user->fresh());

        session()->flash('status', 'Profile updated successfully.');
    }

    public function removePhoto(): void
    {
        if ($this->photo) {
            $this->photo = null;

            return;
        }

        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }

        session()->flash('status', 'Profile photo removed.');
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        session()->flash('password_status', 'Password updated successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function profileRules(User $user): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'website' => array_values(array_filter([
                'nullable',
                'string',
                'max:255',
                $this->website !== '' ? 'url' : null,
            ])),
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected function fillFromUser(User $user): void
    {
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->job_title = $user->job_title ?? '';
        $this->company = $user->company ?? '';
        $this->location = $user->location ?? '';
        $this->website = $user->website ?? '';
        $this->date_of_birth = $user->date_of_birth?->format('Y-m-d') ?? '';
        $this->bio = $user->bio ?? '';
    }
};
?>

<div class="space-y-8">
    @if (session('status'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('status') }}
        </flux:callout>
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <flux:card>
            <flux:heading size="lg">Profile photo</flux:heading>
            <flux:text class="mt-2">JPG, PNG, or GIF up to 2MB.</flux:text>

            <div class="mt-6 flex flex-col items-center text-center">
                @if ($photo)
                    <flux:avatar size="xl" circle :src="$photo->temporaryUrl()" alt="New profile photo" />
                @elseif (auth()->user()->profile_photo_path)
                    <flux:avatar size="xl" circle :src="auth()->user()->profile_photo_url" :name="auth()->user()->name" />
                @else
                    <flux:avatar size="xl" circle :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                @endif

                <div class="mt-4 flex w-full flex-col items-center gap-3">
                    <flux:input type="file" wire:model="photo" accept="image/*" />
                    @if ($photo || auth()->user()->profile_photo_path)
                        <flux:button wire:click="removePhoto">Remove</flux:button>
                    @endif
                </div>

                <flux:text wire:loading wire:target="photo" class="mt-3">Uploading preview...</flux:text>
                @error('photo')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </div>
        </flux:card>

        <form wire:submit="save" class="xl:col-span-2">
            <flux:card class="space-y-6">
                <div>
                    <flux:heading size="lg">Personal details</flux:heading>
                    <flux:text class="mt-2">These details appear on your account and dashboard.</flux:text>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <flux:input label="Full name" wire:model="name" />
                    </div>
                    <flux:input label="Email" type="email" wire:model="email" />
                    <flux:input label="Contact number" type="tel" wire:model="phone" placeholder="+1 555 0100" />
                    <flux:input label="Job title" wire:model="job_title" placeholder="Product manager" />
                    <flux:input label="Company" wire:model="company" placeholder="Acme Inc." />
                    <flux:input label="Location" wire:model="location" placeholder="Austin, TX" />
                    <flux:input label="Website" type="url" wire:model="website" placeholder="https://example.com" />
                    <x-date-picker label="Date of birth" wire:model="date_of_birth" placeholder="Select a date" />
                    <div class="sm:col-span-2">
                        <flux:textarea label="Bio" rows="3" wire:model="bio" placeholder="A short introduction..." />
                    </div>
                </div>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary">Save changes</flux:button>
                </div>
            </flux:card>
        </form>
    </div>

    <form wire:submit="updatePassword">
        <flux:card class="space-y-6">
            @if (session('password_status'))
                <flux:callout variant="success" icon="check-circle">
                    {{ session('password_status') }}
                </flux:callout>
            @endif

            <div>
                <flux:heading size="lg">Update password</flux:heading>
                <flux:text class="mt-2">Use a strong password of at least 8 characters.</flux:text>
            </div>

            <div class="grid gap-6 sm:grid-cols-3">
                <flux:input label="Current password" type="password" wire:model="current_password" autocomplete="current-password" viewable />
                <flux:input label="New password" type="password" wire:model="password" autocomplete="new-password" viewable />
                <flux:input label="Confirm password" type="password" wire:model="password_confirmation" autocomplete="new-password" viewable />
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Update password</flux:button>
            </div>
        </flux:card>
    </form>
</div>
