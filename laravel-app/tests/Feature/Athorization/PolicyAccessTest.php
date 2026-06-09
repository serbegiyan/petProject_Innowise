<?php

namespace Tests\Feature\Authorization;

use App\Models\Category;
use App\Models\Export;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_is_denied_by_policies(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $product = Product::factory()->create();
        $this->assertFalse($user->can('create', Product::class));
        $this->assertFalse($user->can('update', $product));
        $this->assertFalse($user->can('delete', $product));

        $category = Category::factory()->create();
        $this->assertFalse($user->can('create', Category::class));
        $this->assertFalse($user->can('update', $category));
        $this->assertFalse($user->can('delete', $category));

        $service = Service::factory()->create();
        $this->assertFalse($user->can('create', Service::class));
        $this->assertFalse($user->can('update', $service));
        $this->assertFalse($user->can('delete', $service));

        $export = Export::factory()->create();
        $this->assertFalse($user->can('create', Export::class));
        $this->assertFalse($user->can('delete', $export));

        $order = Order::factory()->create();
        $this->assertFalse($user->can('update', $order));
        $this->assertFalse($user->can('delete', $order));
    }

    public function test_admin_is_allowed_by_policies(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $product = Product::factory()->create();
        $this->assertTrue($admin->can('create', Product::class));
        $this->assertTrue($admin->can('update', $product));

        $category = Category::factory()->create();
        $this->assertTrue($admin->can('create', Category::class));
        $this->assertTrue($admin->can('update', $category));
        $this->assertTrue($admin->can('delete', $category));

        $service = Service::factory()->create();
        $this->assertTrue($admin->can('create', Service::class));
        $this->assertTrue($admin->can('update', $service));
        $this->assertTrue($admin->can('delete', $service));

        $export = Export::factory()->create();
        $this->assertTrue($admin->can('create', Export::class));
        $this->assertTrue($admin->can('delete', $export));

        $order = Order::factory()->create();
        $this->assertTrue($admin->can('update', $order));
        $this->assertTrue($admin->can('delete', $order));
    }
}
