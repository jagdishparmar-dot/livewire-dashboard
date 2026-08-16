<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PlaceholderPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function placeholderPages(): array
    {
        return [
            'table editor' => ['table-editor', 'Table Editor'],
            'sql editor' => ['sql-editor', 'SQL Editor'],
            'database' => ['database', 'Database'],
            'authentication' => ['authentication', 'Authentication'],
            'storage' => ['storage', 'Storage'],
            'edge functions' => ['edge-functions', 'Edge Functions'],
            'realtime' => ['realtime', 'Realtime'],
            'reports' => ['reports', 'Reports'],
            'logs' => ['logs', 'Logs'],
            'project settings' => ['project-settings', 'Project Settings'],
        ];
    }

    #[DataProvider('placeholderPages')]
    public function test_guests_are_redirected_from_placeholder_pages(string $route, string $title): void
    {
        $this->get(route($route))->assertRedirect(route('login'));
    }

    #[DataProvider('placeholderPages')]
    public function test_authenticated_users_can_view_placeholder_pages(string $route, string $title): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route($route))
            ->assertOk()
            ->assertSee($title)
            ->assertSee('Coming soon');
    }
}
