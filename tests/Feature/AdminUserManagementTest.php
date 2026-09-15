<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_team_members(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'citizen', 'email' => 'citizen@example.com']);

        $response = $this->actingAs($admin)->get(route('admin.team'));

        $response->assertOk();
        $response->assertSee('citizen@example.com');
    }

    public function test_admin_can_change_a_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $citizen = User::factory()->create(['role' => 'citizen']);

        $response = $this->actingAs($admin)
            ->patch(route('admin.users.update-role', $citizen), ['role' => 'admin']);

        $response->assertRedirect(route('admin.team'));
        $this->assertSame('admin', $citizen->fresh()->role);
    }
}
