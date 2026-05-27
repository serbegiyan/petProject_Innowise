<?php

namespace Tests\Feature\Services;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_services_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Service::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('service.index'))
            ->assertOk()
            ->assertSee('Услуги');
    }
}
