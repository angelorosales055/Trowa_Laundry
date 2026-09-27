<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceInventoryUsage;
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
            'status' => 'claimed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $ready->id,
            'status' => 'claimed',
        ]);

        $this->actingAs($admin)->get(route('billing'))
            ->assertOk()
            ->assertSee('₱680.00')
            ->assertSeeText('Wash & Fold')
            ->assertSee('Dry Clean');
    }

    public function test_status_workflow_allows_only_next_service_aware_stage(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $wash = Service::create([
            'name' => 'Wash & Fold',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 55,
            'price_per_load' => 55,
        ]);
        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 9,
            'service_ids' => [$wash->id],
        ])->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();
        $this->assertSame(['washing', 'cancelled'], $order->nextStatuses());
        $this->actingAs($staff)->get(route('schedule.index', ['status' => 'received']))
            ->assertOk()
            ->assertSee('Move to Washing')
            ->assertSee('Cancel Order')
            ->assertDontSee('<select name="status"', false);
        $this->actingAs($staff)->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Move to Washing')
            ->assertSee('Cancel Order')
            ->assertDontSee('<select name="status"', false);
        $this->actingAs($staff)->post(route('orders.status.update', $order), [
            'status' => 'ready_for_pickup',
        ])->assertSessionHasErrors('status');
        $this->actingAs($staff)->post(route('orders.status.update', $order), [
            'status' => 'washing',
        ])->assertRedirect(route('schedule.index', ['status' => 'washing']));

        $order->refresh();
        $this->assertSame(['folding', 'cancelled'], $order->nextStatuses());
        $this->actingAs($staff)->get(route('schedule.index', ['status' => 'washing']))
            ->assertOk()
            ->assertSee($order->order_number);
        $this->actingAs($staff)->post(route('orders.status.update', $order), [
            'status' => 'received',
        ])->assertSessionHasErrors('status');
    }

    public function test_drying_tab_lists_orders_and_workflow_skips_unselected_stages(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $dryService = Service::create([
            'name' => 'Dry (40 minutes)',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 70,
            'price_per_load' => 70,
        ]);
        $foldService = Service::create([
            'name' => 'Fold/Load',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 20,
            'price_per_load' => 20,
        ]);
        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 5,
            'service_ids' => [$dryService->id, $foldService->id],
        ])->assertRedirect();

        $dryingOrder = Order::query()->latest('id')->firstOrFail();
        $this->actingAs($staff)->post(route('orders.status.update', $dryingOrder), [
            'status' => 'washing',
        ])->assertRedirect(route('schedule.index', ['status' => 'washing']));
        $dryingOrder->refresh();
        $this->assertSame(['drying', 'cancelled'], $dryingOrder->nextStatuses());
        $this->actingAs($staff)->post(route('orders.status.update', $dryingOrder), [
            'status' => 'drying',
        ])->assertRedirect(route('schedule.index', ['status' => 'drying']));
        $this->actingAs($staff)->get(route('schedule.index', ['status' => 'drying']))
            ->assertOk()
            ->assertSee($dryingOrder->order_number);
        $dryingOrder->refresh();
        $this->assertSame(['folding', 'cancelled'], $dryingOrder->nextStatuses());

        $noFoldService = Service::create([
            'name' => 'Wash Only',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 55,
            'price_per_load' => 55,
        ]);
        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 5,
            'service_ids' => [$noFoldService->id],
        ])->assertRedirect();

        $washOnlyOrder = Order::query()->latest('id')->firstOrFail();
        $this->assertSame(['washing', 'cancelled'], $washOnlyOrder->nextStatuses());
        $this->actingAs($staff)->post(route('orders.status.update', $washOnlyOrder), [
            'status' => 'washing',
        ])->assertRedirect();
        $washOnlyOrder->refresh();
        $this->assertSame(['ready_for_pickup', 'cancelled'], $washOnlyOrder->nextStatuses());
        $this->actingAs($staff)->get(route('schedule.index', ['status' => 'washing']))
            ->assertOk()
            ->assertSee('Move to Ready for Pickup');
    }

    public function test_ironing_tab_and_transitions_follow_selected_services(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $dryService = Service::create([
            'name' => 'Dry (40 minutes)',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 70,
            'price_per_load' => 70,
        ]);
        $ironService = Service::create([
            'name' => 'Iron',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 25,
            'price_per_load' => 25,
        ]);
        $foldService = Service::create([
            'name' => 'Fold/Load',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 20,
            'price_per_load' => 20,
        ]);
        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 5,
            'service_ids' => [$dryService->id, $ironService->id, $foldService->id],
        ])->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();
        $this->actingAs($staff)->post(route('orders.status.update', $order), ['status' => 'washing'])
            ->assertRedirect();
        $order->refresh();
        $this->assertSame(['drying', 'cancelled'], $order->nextStatuses());
        $this->actingAs($staff)->post(route('orders.status.update', $order), ['status' => 'drying'])
            ->assertRedirect();
        $order->refresh();
        $this->assertSame(['ironing', 'cancelled'], $order->nextStatuses());
        $this->actingAs($staff)->post(route('orders.status.update', $order), ['status' => 'ironing'])
            ->assertRedirect(route('schedule.index', ['status' => 'ironing']));
        $this->actingAs($staff)->get(route('schedule.index', ['status' => 'ironing']))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Iron');
        $order->refresh();
        $this->assertSame(['folding', 'cancelled'], $order->nextStatuses());
    }

    public function test_refunds_are_audited_and_payment_summary_is_reconciled(): void
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
        $order = Order::query()->latest('id')->firstOrFail();
        $this->actingAs($staff)->post(route('orders.payments.store', $order), [
            'amount' => 60,
            'payment_method' => 'gcash',
        ])->assertRedirect();
        $payment = Payment::query()->where('order_id', $order->id)->firstOrFail();
        $this->assertMatchesRegularExpression('/^PAY-\d{8}-[0-9A-F-]{36}$/', $payment->reference_number);

        $this->actingAs($admin)->post(route('orders.payments.refund', [$order, $payment]), [
            'amount' => 15,
            'notes' => 'Partial refund.',
        ])->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'amount_paid' => '45.00',
            'payment_status' => 'partially_paid',
        ]);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'related_payment_id' => $payment->id,
            'payment_status' => 'refunded',
            'amount' => '15.00',
        ]);
        $refund = Payment::query()->where('related_payment_id', $payment->id)->firstOrFail();
        $this->assertMatchesRegularExpression('/^PAY-\d{8}-[0-9A-F-]{36}$/', $refund->reference_number);
        $this->actingAs($admin)->post(route('orders.payments.refund', [$order, $payment]), [
            'amount' => 46,
            'notes' => 'Exceeds remaining refundable balance.',
        ])->assertSessionHasErrors('amount');
        $order->update(['status' => 'claimed']);
        Expense::query()->create([
            'category' => 'supplies',
            'description' => 'Packaging',
            'amount' => 10,
            'expense_date' => now()->toDateString(),
            'recorded_by' => $admin->id,
        ]);
        $this->actingAs($admin)->get(route('reports'))
            ->assertOk()
            ->assertSee('Net profit')
            ->assertSee('₱35.00');
    }

    public function test_service_usage_decrements_inventory_with_order_linked_movement(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $service = Service::create([
            'name' => 'Detergent Service',
            'icon' => '🧴',
            'pricing_type' => 'per_load',
            'price' => 10,
            'price_per_load' => 10,
        ]);
        $item = InventoryItem::query()->create([
            'name' => 'Detergent',
            'unit' => 'L',
            'quantity_on_hand' => 2,
            'low_stock_threshold' => 1.5,
        ]);
        ServiceInventoryUsage::query()->create([
            'service_id' => $service->id,
            'inventory_item_id' => $item->id,
            'quantity_per_load' => 0.4,
        ]);

        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 15,
            'service_ids' => [$service->id],
        ])->assertRedirect();

        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'quantity_on_hand' => '1.200',
        ]);
        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $item->id,
            'movement_type' => 'usage',
            'quantity_change' => '-0.800',
            'recorded_by' => $staff->id,
        ]);
        $this->assertSame(0, Expense::query()->count());
        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Detergent');
    }

    public function test_admin_inventory_page_loads_service_usage_relations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::create([
            'name' => 'Wash',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 60,
            'price_per_load' => 60,
        ]);
        $item = InventoryItem::query()->create([
            'name' => 'Detergent',
            'unit' => 'L',
            'quantity_on_hand' => 20,
            'low_stock_threshold' => 5,
        ]);
        ServiceInventoryUsage::query()->create([
            'service_id' => $service->id,
            'inventory_item_id' => $item->id,
            'quantity_per_load' => 0.2,
        ]);

        $this->actingAs($admin)->get(route('inventory.index'))
            ->assertOk()
            ->assertSee('Automatic Service Consumption')
            ->assertSee('Wash uses 0.200 L Detergent / load');
    }

    public function test_admin_can_log_expense_and_customer_merge_preserves_order_snapshot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $source = Customer::factory()->create(['name' => 'Duplicate Customer']);
        $target = Customer::factory()->create(['name' => 'Primary Customer']);
        $order = Order::factory()->for($staff, 'creator')->for($source)->create([
            'customer_name' => 'Duplicate Customer',
        ]);

        $this->actingAs($admin)->post(route('expenses.store'), [
            'category' => 'utilities',
            'description' => 'Electricity bill',
            'amount' => 1500,
            'expense_date' => now()->toDateString(),
        ])->assertRedirect();
        $this->assertDatabaseHas('expenses', ['category' => 'utilities', 'amount' => '1500.00']);
        $this->actingAs($admin)->get(route('expenses.index', [
            'from' => now()->toDateString(),
            'to' => now()->toDateString(),
        ]))->assertOk()->assertSee('Electricity bill');
        $this->actingAs($admin)->get(route('reports'))
            ->assertOk()
            ->assertSee('Net profit');

        $this->actingAs($admin)->post(route('customers.merge', $source), [
            'target_customer_id' => $target->id,
        ])->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'customer_id' => $target->id,
            'customer_name' => 'Duplicate Customer',
        ]);
        $this->assertDatabaseHas('customers', [
            'id' => $source->id,
            'is_active' => false,
            'merged_into_id' => $target->id,
        ]);
    }

    public function test_admin_order_correction_recalculates_and_keeps_audit_snapshot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $service = Service::create([
            'name' => 'Wash',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 55,
            'price_per_load' => 55,
        ]);
        $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 8,
            'service_ids' => [$service->id],
        ])->assertRedirect();
        $order = Order::query()->latest('id')->firstOrFail();

        $this->actingAs($admin)->put(route('orders.update-details', $order), [
            'customer_id' => $customer->id,
            'weight_kg' => 15,
            'service_ids' => [$service->id],
            'item_details' => [['item_name' => 'Shirt', 'quantity' => 4]],
            'reason' => 'Corrected weight after recount.',
        ])->assertRedirect(route('orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'weight_kg' => '15.00',
            'number_of_loads' => 2,
            'total_price' => '110.00',
        ]);
        $this->assertDatabaseHas('order_edit_histories', [
            'order_id' => $order->id,
            'changed_by' => $admin->id,
            'reason' => 'Corrected weight after recount.',
        ]);
        $this->assertDatabaseHas('order_item_details', [
            'order_id' => $order->id,
            'item_name' => 'Shirt',
            'quantity' => 4,
        ]);
    }

    public function test_staff_cannot_access_another_staffs_order_or_admin_inventory(): void
    {
        $owner = User::factory()->create(['role' => 'staff']);
        $otherStaff = User::factory()->create(['role' => 'staff']);
        $order = Order::factory()->for($owner, 'creator')->create([
            'total_price' => 100,
            'amount_paid' => 0,
        ]);

        $this->actingAs($otherStaff)->get(route('orders.show', $order))->assertForbidden();
        $this->actingAs($otherStaff)->post(route('orders.payments.store', $order), [
            'amount' => 50,
            'payment_method' => 'cash',
        ])->assertForbidden();
        $this->actingAs($otherStaff)->get(route('inventory.index'))->assertForbidden();
    }

    public function test_admin_dashboard_kpis_and_chart_drill_down_to_filtered_records(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $order = Order::factory()->for($staff, 'creator')->create([
            'order_number' => 'TL-DASHBOARD-TEST',
            'status' => 'received',
            'created_at' => now()->startOfDay(),
            'updated_at' => now()->startOfDay(),
        ]);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Daily Order Volume')
            ->assertSee('Orders by Stage')
            ->assertSee(route('orders.index'), false)
            ->assertSee(route('schedule.index', ['status' => 'received']), false)
            ->assertSee(route('schedule.index', ['date' => now()->toDateString()]), false);

        $this->actingAs($admin)->get(route('schedule.index', ['date' => now()->toDateString()]))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_payments_by_method_dashboard_link_filters_payment_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $gcashOrder = Order::factory()->for($staff, 'creator')->create([
            'customer_name' => 'GCash Customer',
            'total_price' => 100,
        ]);
        $cashOrder = Order::factory()->for($staff, 'creator')->create([
            'customer_name' => 'Cash Customer',
            'total_price' => 100,
        ]);
        foreach ([[$gcashOrder, 'gcash'], [$cashOrder, 'cash']] as [$order, $method]) {
            Payment::query()->create([
                'order_id' => $order->id,
                'amount' => 100,
                'payment_method' => $method,
                'payment_status' => 'paid',
                'received_by' => $staff->id,
                'paid_at' => now(),
            ]);
        }

        $this->actingAs($admin)->get(route('payments.index', ['method' => 'gcash']))
            ->assertOk()
            ->assertSee('TL-'.$gcashOrder->id)
            ->assertDontSee('TL-'.$cashOrder->id);
    }

    public function test_dashboard_and_reports_filter_metrics_by_selected_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $todayOrder = Order::factory()->for($staff, 'creator')->create([
            'order_number' => 'TL-TODAY',
            'status' => 'claimed',
            'total_price' => 120,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $yesterdayOrder = Order::factory()->for($staff, 'creator')->create([
            'order_number' => 'TL-YESTERDAY',
            'status' => 'claimed',
            'total_price' => 80,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);
        Expense::query()->create([
            'category' => 'supplies',
            'description' => 'Today supplies',
            'amount' => 30,
            'expense_date' => $today,
            'recorded_by' => $admin->id,
        ]);
        Expense::query()->create([
            'category' => 'supplies',
            'description' => 'Yesterday supplies',
            'amount' => 20,
            'expense_date' => $yesterday,
            'recorded_by' => $admin->id,
        ]);
        Payment::query()->create([
            'order_id' => $todayOrder->id,
            'amount' => 120,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'received_by' => $staff->id,
            'paid_at' => now(),
        ]);
        Payment::query()->create([
            'order_id' => $yesterdayOrder->id,
            'amount' => 80,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'received_by' => $staff->id,
            'paid_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)->get(route('dashboard', ['from' => $today, 'to' => $today]))
            ->assertOk()
            ->assertSee('Daily Order Volume')
            ->assertSee('TL-TODAY')
            ->assertDontSee('TL-YESTERDAY')
            ->assertSee('<polyline', false)
            ->assertSee('name="from"', false)
            ->assertSee('name="to"', false);

        $this->actingAs($admin)->get(route('reports', ['from' => $today, 'to' => $today]))
            ->assertOk()
            ->assertSee('₱120.00')
            ->assertSee('₱30.00')
            ->assertSee('₱90.00')
            ->assertSee('Orders in selected range');

        $this->actingAs($admin)->get(route('reports.print', ['from' => $today, 'to' => $today]))
            ->assertOk()
            ->assertSee('Today supplies')
            ->assertDontSee('Yesterday supplies');
    }
}
