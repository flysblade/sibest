<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_mahasiswa_cannot_access_admin_dashboard(): void
    {
        $user = User::where('role', 'mahasiswa')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_penyeleksi_dashboard(): void
    {
        $user = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($user)
            ->get(route('penyeleksi.dashboard'))
            ->assertForbidden();
    }

    public function test_penyeleksi_can_open_assigned_dashboard(): void
    {
        $user = User::where('role', 'penyeleksi')->firstOrFail();

        $this->actingAs($user)
            ->get(route('penyeleksi.dashboard'))
            ->assertOk();
    }
}
