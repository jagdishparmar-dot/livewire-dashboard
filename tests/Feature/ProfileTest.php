<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_the_profile_page(): void
    {
        $this->get(route('profile'))->assertRedirect(route('login'));
    }

    public function test_users_can_view_the_profile_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Personal details')
            ->assertSee($user->name);
    }

    public function test_users_can_update_profile_details(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::profile')
            ->set('name', 'Ava Patel')
            ->set('email', 'ava@example.com')
            ->set('phone', '+1 555 0100')
            ->set('job_title', 'Designer')
            ->set('company', 'Acme')
            ->set('location', 'Austin, TX')
            ->set('website', 'https://ava.example')
            ->set('date_of_birth', '1994-04-12')
            ->set('bio', 'Builds clean interfaces.')
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('Ava Patel', $user->name);
        $this->assertSame('ava@example.com', $user->email);
        $this->assertSame('+1 555 0100', $user->phone);
        $this->assertSame('Designer', $user->job_title);
        $this->assertSame('Acme', $user->company);
        $this->assertSame('Austin, TX', $user->location);
        $this->assertSame('https://ava.example', $user->website);
        $this->assertSame('1994-04-12', $user->date_of_birth?->format('Y-m-d'));
        $this->assertSame('Builds clean interfaces.', $user->bio);
    }

    public function test_users_can_upload_and_remove_a_profile_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::profile')
            ->set('photo', UploadedFile::fake()->image('avatar.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);

        Livewire::test('pages::profile')
            ->call('removePhoto');

        $user->refresh();

        $this->assertNull($user->profile_photo_path);
    }

    public function test_users_can_update_their_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $this->actingAs($user);

        Livewire::test('pages::profile')
            ->set('current_password', 'password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }
}
