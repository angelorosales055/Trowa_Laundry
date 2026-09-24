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

    public function test_merged_manager_account_uses_the_admin_interface(): void
    {
        $user = User::factory()->create([
            'username' => 'manager123',
            'password' => 'pass123',
            'role' => 'admin',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Admin Dashboard');
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
            'status' => 'received',
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

    public function test_staff_order_calculates_loads_and_normalized_service_subtotals(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $services = collect([
            ['name' => 'Wash', 'price' => 60],
            ['name' => 'Dry', 'price' => 70],
            ['name' => 'Detergent', 'price' => 15],
            ['name' => 'Fabcon', 'price' => 8],
        ])->map(fn (array $service): Service => Service::create([
            ...$service,
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price_per_load' => $service['price'],
        ]));

        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 16,
            'service_ids' => $services->pluck('id')->all(),
            'amount_paid' => 306,
        ])->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();
        $this->assertSame(2, $order->number_of_loads);
        $this->assertSame('306.00', $order->total_price);
        $this->assertSame('paid', $order->payment_status);
        $this->assertDatabaseCount('order_services', 4);
        $this->assertDatabaseHas('order_services', [
            'order_id' => $order->id,
            'service_id' => $services->first()->id,
            'loads' => 2,
            'price_per_load' => '60.00',
            'subtotal' => '120.00',
        ]);
    }

    public function test_per_load_service_uses_loads_instead_of_raw_weight(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $service = Service::create([
            'name' => 'Wash & Fold',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 55,
            'price_per_load' => 55,
        ]);

        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 15,
            'service_ids' => [$service->id],
        ])->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();
        $this->assertSame(2, $order->number_of_loads);
        $this->assertSame('110.00', $order->total_price);
        $this->assertDatabaseHas('order_services', [
            'order_id' => $order->id,
            'price_per_load' => '55.00',
            'loads' => 2,
            'subtotal' => '110.00',
        ]);
    }

    public function test_order_payment_status_and_overpayment_validation_are_enforced(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $service = Service::create([
            'name' => 'Wash',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 60,
            'price_per_load' => 60,
        ]);

        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 8,
            'service_ids' => [$service->id],
            'amount_paid' => 20,
        ])->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();
        $this->assertSame('partially_paid', $order->payment_status);
        $this->assertSame('0.00', $order->change);

        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 8,
            'service_ids' => [$service->id],
            'amount_paid' => 61,
        ])->assertStatus(422);
    }

    public function test_new_customer_is_created_and_inactive_services_are_rejected(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $service = Service::create([
            'name' => 'Wash',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 60,
            'price_per_load' => 60,
            'is_active' => false,
        ]);

        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_name' => 'New Laundry Customer',
            'contact_number' => '09170000000',
            'address' => 'Main Street',
            'weight_kg' => 8,
            'service_ids' => [$service->id],
        ])->assertStatus(422);

        $this->assertDatabaseMissing('customers', ['name' => 'New Laundry Customer']);
    }

    public function test_service_price_changes_do_not_modify_existing_order_snapshot(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        $service = Service::create([
            'name' => 'Wash',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 60,
            'price_per_load' => 60,
        ]);

        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 8,
            'service_ids' => [$service->id],
        ])->assertRedirect();

        $this->actingAs($admin)->put(route('services.update', $service), [
            'name' => $service->name,
            'icon' => $service->icon,
            'pricing_type' => 'per_load',
            'price' => 90,
            'price_per_load' => 90,
        ])->assertRedirect();

        $this->assertDatabaseHas('order_services', [
            'service_id' => $service->id,
            'price_per_load' => '60.00',
            'subtotal' => '60.00',
        ]);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'price_per_load' => '90.00',
        ]);
    }

    public function test_status_changes_create_history_and_payment_records_update_balance(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $service = Service::create([
            'name' => 'Wash',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 60,
            'price_per_load' => 60,
        ]);

        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 8,
            'service_ids' => [$service->id],
        ])->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => 'received',
            'changed_by' => $staff->id,
        ]);

        $this->actingAs($staff)->post(route('orders.status.update', $order), [
            'status' => 'washing',
            'notes' => 'Machine cycle started.',
        ])->assertRedirect();

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => 'washing',
            'notes' => 'Machine cycle started.',
        ]);

        $this->actingAs($staff)->post(route('orders.payments.store', $order), [
            'amount' => 20,
            'payment_method' => 'cash',
        ])->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => '20.00',
            'payment_method' => 'cash',
            'received_by' => $staff->id,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'amount_paid' => '20.00',
            'payment_status' => 'partially_paid',
        ]);
    }

    public function test_payment_overpayment_returns_to_order_with_validation_error(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $service = Service::create([
            'name' => 'Wash',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 60,
            'price_per_load' => 60,
        ]);

        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 8,
            'service_ids' => [$service->id],
        ])->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();
        $response = $this->actingAs($staff)->post(route('orders.payments.store', $order), [
            'amount' => 61,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['amount']);
        $this->assertDatabaseMissing('payments', [
            'order_id' => $order->id,
            'amount' => '61.00',
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
