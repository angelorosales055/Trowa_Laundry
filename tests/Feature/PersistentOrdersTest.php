<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PersistentOrdersTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_login_with_username_and_password(): void
    {
        $user = User::factory()->create([
            'username' => 'staff123',
            'password' => 'pass123',
            'role' => 'staff',
        ]);

        $response = $this->post(route('login.post'), [
            'username' => 'staff123',
            'password' => 'pass123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_staff_can_create_a_persistent_order(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create(['name' => 'Ana Cruz']);
        $service = Service::create([
            'name' => 'Wash & Fold',
            'icon' => '🧺',
            'pricing_type' => 'per_kg',
            'price' => 55,
        ]);

        $response = $this->actingAs($user)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight' => 4.5,
            'service_ids' => [$service->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'created_by' => $user->id,
            'customer_id' => $customer->id,
            'customer_name' => 'Ana Cruz',
            'weight_kg' => 4.5,
            'services' => 'Wash & Fold',
            'status' => 'pending',
        ]);
    }

    public function test_staff_cannot_update_another_staff_member_order(): void
    {
        $owner = User::factory()->create(['role' => 'staff']);
        $otherStaff = User::factory()->create(['role' => 'staff']);
        $order = Order::factory()->for($owner, 'creator')->create();

        $response = $this->actingAs($otherStaff)->post(route('orders.update'), [
            'id' => $order->id,
            'status' => 'ready',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_staff_order_total_uses_per_kg_and_flat_rate_prices(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create(['name' => 'Price Check']);
        $wash = Service::create([
            'name' => 'Wash Test',
            'icon' => '🧺',
            'pricing_type' => 'per_kg',
            'price' => 55,
        ]);
        $softener = Service::create([
            'name' => 'Softener Test',
            'icon' => '🌸',
            'pricing_type' => 'flat_rate',
            'price' => 25,
        ]);

        $response = $this->actingAs($user)->post(route('orders.add'), [
            'customer' => 'Price Check',
            'customer_id' => $customer->id,
            'weight' => 4,
            'service_ids' => [$wash->id, $softener->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Price Check',
            'services' => 'Wash Test, Softener Test',
            'total_price' => 245,
        ]);
    }

    public function test_non_admin_cannot_access_service_settings(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get(route('services.index'))->assertForbidden();
    }

    public function test_unknown_role_cannot_access_dashboard_or_order_endpoints(): void
    {
        $user = User::factory()->create(['role' => 'auditor']);

        $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($user)->post(route('orders.add'), [])->assertForbidden();
    }

    public function test_staff_can_add_customer_and_filter_schedule_by_status(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create(['name' => 'Schedule Customer']);
        $order = Order::factory()->for($staff, 'creator')->for($customer)->create(['status' => 'ready']);

        $this->actingAs($staff)->post(route('customers.store'), [
            'name' => 'New Customer',
            'phone' => '09170000000',
            'email' => 'new@example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('customers', ['name' => 'New Customer']);
        $this->actingAs($staff)->get(route('schedule.index', ['status' => 'ready']))
            ->assertOk()
            ->assertSee($order->services)
            ->assertSee('Schedule Customer');
    }

    public function test_staff_can_mark_an_order_delivered_and_admin_billing_only_lists_delivered_orders(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $admin = User::factory()->create(['role' => 'admin']);
        $delivered = Order::factory()->for($staff, 'creator')->create([
            'status' => 'delivered',
            'total_price' => 200,
            'services' => 'Wash & Fold',
        ]);
        $ready = Order::factory()->for($staff, 'creator')->create([
            'status' => 'ready',
            'total_price' => 480,
            'services' => 'Dry Clean',
        ]);

        $response = $this->actingAs($staff)->post(route('orders.update'), [
            'id' => $ready->id,
            'status' => 'delivered',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $ready->id,
            'status' => 'delivered',
        ]);

        $this->actingAs($admin)->get(route('billing'))
            ->assertOk()
            ->assertSee('₱680.00')
            ->assertSeeText('Wash & Fold')
            ->assertSee('Dry Clean');
    }
}
