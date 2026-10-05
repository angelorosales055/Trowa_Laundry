<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplyInventorySachetAndMedalTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_inventory_item_automatically_perceived_and_tagged_as_per_sachet(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // When creating "Surf Fabcon" without specifying unit, it defaults to sachet
        $response = $this->actingAs($admin)->post(route('inventory.store'), [
            'name' => 'Surf Fabcon Blossom',
            'unit' => '',
            'quantity_on_hand' => 50,
            'low_stock_threshold' => 10,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inventory_items', [
            'name' => 'Surf Fabcon Blossom',
            'unit' => 'sachet',
            'quantity_on_hand' => '50.000',
            'low_stock_threshold' => '10.000',
        ]);

        // Creating "Downy Sunrise" with unit omitted or sachet
        $this->actingAs($admin)->post(route('inventory.store'), [
            'name' => 'Downy Sunrise Fresh Sachet',
            'unit' => 'sachet',
            'quantity_on_hand' => 8,
            'low_stock_threshold' => 10,
        ])->assertRedirect();

        $this->assertDatabaseHas('inventory_items', [
            'name' => 'Downy Sunrise Fresh Sachet',
            'unit' => 'sachet',
        ]);
    }

    public function test_inventory_page_displays_what_is_inside_our_inventory_and_warning_medals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Healthy item
        InventoryItem::create([
            'name' => 'Surf Powder Detergent',
            'unit' => 'sachet',
            'quantity_on_hand' => 60,
            'low_stock_threshold' => 15,
            'is_active' => true,
        ]);

        // 2. Almost out item (warning medal)
        InventoryItem::create([
            'name' => 'Downy Fabcon',
            'unit' => 'sachet',
            'quantity_on_hand' => 5,
            'low_stock_threshold' => 10,
            'is_active' => true,
        ]);

        // 3. Depleted item (out of stock medal)
        InventoryItem::create([
            'name' => 'Ariel Stain Remover',
            'unit' => 'sachet',
            'quantity_on_hand' => 0,
            'low_stock_threshold' => 8,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('inventory.index', ['tab' => 'stock']));
        $response->assertOk();

        // Check "What Is Inside Our Inventory" section exists
        $response->assertSee('What Is Inside Our Inventory');
        $response->assertSee('Per Sachet');

        // Check exact quantities are visible
        $response->assertSee('60 sachet');
        $response->assertSee('5 sachet');
        $response->assertSee('0 sachet');

        // Check medals are displayed
        $response->assertSee('Almost Out Medal');
        $response->assertSee('Depleted Stock Medal');
        $response->assertSee('Stock Healthy Medal');

        // Medal counters in top summary
        $response->assertSee('1 Almost Out Medals');
        $response->assertSee('1 Depleted Medals');
        $response->assertSee('1 Healthy Stock Medals');
    }

    public function test_inventory_supplies_are_used_in_staff_transaction_and_automatically_deducted(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $service = Service::create([
            'name' => 'Wash & Fold',
            'pricing_type' => 'per_load',
            'price' => 70,
            'price_per_load' => 70,
            'is_active' => true,
        ]);

        $surfItem = InventoryItem::create([
            'name' => 'Surf Powder Sachet',
            'unit' => 'sachet',
            'quantity_on_hand' => 30,
            'low_stock_threshold' => 10,
            'is_active' => true,
        ]);

        $downyItem = InventoryItem::create([
            'name' => 'Downy Fabcon Sachet',
            'unit' => 'sachet',
            'quantity_on_hand' => 20,
            'low_stock_threshold' => 8,
            'is_active' => true,
        ]);

        // Staff visits orders queue, sees supplies in the intake wizard
        $queueResponse = $this->actingAs($staff)->get(route('orders.index'));
        $queueResponse->assertOk();
        $queueResponse->assertSee('Surf Powder Sachet');
        $queueResponse->assertSee('Downy Fabcon Sachet');
        $queueResponse->assertSee('Per Sachet');

        // Staff submits transaction using 2 sachets of Surf and 1 sachet of Downy
        $response = $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 8,
            'service_ids' => [$service->id],
            'amount_paid' => 70,
            'inventory_items' => [
                $surfItem->id => 2,
                $downyItem->id => 1,
            ],
        ]);

        $response->assertRedirect();
        $order = Order::query()->latest('id')->firstOrFail();

        // Assert stock in/stock out deduction logic:
        // Surf decreased from 30 to 28
        $this->assertDatabaseHas('inventory_items', [
            'id' => $surfItem->id,
            'quantity_on_hand' => '28.000',
        ]);

        // Downy decreased from 20 to 19
        $this->assertDatabaseHas('inventory_items', [
            'id' => $downyItem->id,
            'quantity_on_hand' => '19.000',
        ]);

        // Assert movements logged
        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $surfItem->id,
            'order_id' => $order->id,
            'recorded_by' => $staff->id,
            'movement_type' => 'usage',
            'quantity_change' => '-2.000',
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $downyItem->id,
            'order_id' => $order->id,
            'recorded_by' => $staff->id,
            'movement_type' => 'usage',
            'quantity_change' => '-1.000',
        ]);
    }

    public function test_staff_transaction_fails_if_insufficient_sachets_in_inventory(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $service = Service::create([
            'name' => 'Wash',
            'pricing_type' => 'per_load',
            'price' => 50,
            'price_per_load' => 50,
            'is_active' => true,
        ]);

        $surfItem = InventoryItem::create([
            'name' => 'Surf Fabcon Sachet',
            'unit' => 'sachet',
            'quantity_on_hand' => 1,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        // Trying to use 3 sachets when only 1 is available
        $response = $this->actingAs($staff)->post(route('orders.add'), [
            'customer_id' => $customer->id,
            'weight_kg' => 8,
            'service_ids' => [$service->id],
            'amount_paid' => 50,
            'inventory_items' => [
                $surfItem->id => 3,
            ],
        ]);

        $response->assertSessionHasErrors('inventory_items');

        // Quantity remains intact
        $this->assertDatabaseHas('inventory_items', [
            'id' => $surfItem->id,
            'quantity_on_hand' => '1.000',
        ]);
    }
}
