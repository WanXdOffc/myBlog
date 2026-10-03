<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_panel(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_seeder_creates_a_panel_admin_without_a_fixed_password(): void
    {
        config([
            'app.admin.email' => 'first-admin@example.test',
            'app.admin.name' => 'First Admin',
            'app.admin.password' => null,
        ]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'first-admin@example.test')->firstOrFail();

        $this->assertTrue($admin->is_admin);
        $this->assertNotEmpty($admin->password);
        $this->assertFalse(Hash::check('Admin@12345', $admin->password));
    }

    public function test_admin_seeder_does_not_reset_an_existing_account(): void
    {
        config([
            'app.admin.email' => 'existing-user@example.test',
            'app.admin.name' => 'First Admin',
            'app.admin.password' => null,
        ]);

        $user = User::factory()->create([
            'email' => 'existing-user@example.test',
            'password' => 'existing-user-password',
            'is_admin' => false,
        ]);

        $this->seed(AdminUserSeeder::class);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $user->name,
            'is_admin' => false,
        ]);
        $this->assertTrue(Hash::check('existing-user-password', $user->fresh()->password));
    }
}
