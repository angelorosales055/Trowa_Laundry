<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaundryQueueUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_laundry_queue_with_pulse_kpis_and_ticket_cards(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'name' => 'Carla Staff']);
        $customer = Customer::create([
            'name' => 'Roberto Cruz',
            'phone' => '09170001122',
            'address' => '123 Sampaguita St',
            'is_active' => true,
        ]);

        $service = Service::create([
            'name' => 'Wash & Fold',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 60,
            'price_per_load' => 60,
            'is_active' => true,
        ]);

        $order = Order::create([
            'created_by' => $staff->id,
            'order_number' => 'TL-2026-0001',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'weight_kg' => 8.0,
            'number_of_loads' => 1,
            'services' => 'Wash & Fold',
            'total_price' => 60.00,
            'amount_paid' => 60.00,
            'payment_status' => 'paid',
            'status' => 'received',
        ]);

        $response = $this->actingAs($staff)->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee('Laundry Queue Management');
        $response->assertSee('TL-2026-0001');
        $response->assertSee('Roberto Cruz');
        $response->assertSee('8 kg');
        $response->assertSee('1 commercial load');
        $response->assertSee('Paid');
        $response->assertSee('Basic Service');
        $response->assertSee('Fast Laundry Intake');
    }

    public function test_staff_can_filter_queue_by_stage_and_payment_status(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create(['name' => 'Filter Target']);

        $receivedOrder = Order::factory()->for($staff, 'creator')->for($customer)->create([
            'order_number' => 'TL-FILTER-RCV',
            'status' => 'received',
            'payment_status' => 'unpaid',
        ]);

        $washingOrder = Order::factory()->for($staff, 'creator')->for($customer)->create([
            'order_number' => 'TL-FILTER-WSH',
            'status' => 'washing',
            'payment_status' => 'paid',
        ]);

        // Filter by stage
        $response = $this->actingAs($staff)->get(route('orders.index', ['status' => 'washing']));
        $response->assertOk();
        $response->assertSee('TL-FILTER-WSH');
        $response->assertDontSee('TL-FILTER-RCV');

        // Filter by payment status
        $paymentResponse = $this->actingAs($staff)->get(route('orders.index', ['payment_status' => 'unpaid']));
        $paymentResponse->assertOk();
        $paymentResponse->assertSee('TL-FILTER-RCV');
        $paymentResponse->assertDontSee('TL-FILTER-WSH');
    }

    public function test_staff_can_advance_order_stage_with_redirect_back_to_queue(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $service = Service::create([
            'name' => 'Wash Only',
            'pricing_type' => 'per_load',
            'price' => 55,
            'price_per_load' => 55,
        ]);

        $order = Order::factory()->for($staff, 'creator')->create([
            'status' => 'received',
            'services' => 'Wash Only',
        ]);

        $response = $this->actingAs($staff)->post(route('orders.status.update', $order), [
            'status' => 'washing',
            'redirect_to' => 'orders.index',
        ]);

        $response->assertRedirect(route('orders.index', ['status' => 'washing']));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'washing',
        ]);
    }

    public function test_staff_can_record_payment_from_quick_settlement(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $order = Order::factory()->for($staff, 'creator')->create([
            'total_price' => 150.00,
            'amount_paid' => 0.00,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($staff)->post(route('orders.payments.store', $order), [
            'amount' => 150.00,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();
        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertEquals(150.00, (float) $order->amount_paid);
    }

    public function test_unpaid_order_cannot_be_marked_as_claimed_without_settling_payment(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $order = Order::factory()->for($staff, 'creator')->create([
            'status' => 'ready_for_pickup',
            'total_price' => 200.00,
            'amount_paid' => 50.00,
            'payment_status' => 'partially_paid',
        ]);

        $response = $this->actingAs($staff)->post(route('orders.status.update', $order), [
            'status' => 'claimed',
            'redirect_to' => 'orders.index',
        ]);

        $response->assertSessionHasErrors('status');
        $order->refresh();
        $this->assertNotSame('claimed', $order->status);
    }

    public function test_unpaid_order_is_settled_and_marked_as_claimed_when_payment_is_provided_in_claim_request(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $order = Order::factory()->for($staff, 'creator')->create([
            'status' => 'ready_for_pickup',
            'total_price' => 150.00,
            'amount_paid' => 0.00,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($staff)->post(route('orders.status.update', $order), [
            'status' => 'claimed',
            'redirect_to' => 'orders.index',
            'amount' => 150.00,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect(route('orders.index', ['status' => 'claimed']));
        $order->refresh();
        $this->assertSame('claimed', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertEquals(150.00, (float) $order->amount_paid);
    }

    public function test_already_paid_order_can_be_marked_as_claimed_directly(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $order = Order::factory()->for($staff, 'creator')->create([
            'status' => 'ready_for_pickup',
            'total_price' => 180.00,
            'amount_paid' => 180.00,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($staff)->post(route('orders.status.update', $order), [
            'status' => 'claimed',
            'redirect_to' => 'orders.index',
        ]);

        $response->assertRedirect(route('orders.index', ['status' => 'claimed']));
        $order->refresh();
        $this->assertSame('claimed', $order->status);
    }

    public function test_staff_can_access_intake_wizard_modal_in_laundry_queue(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        Customer::create([
            'name' => 'Aling Nena',
            'phone' => '09181112233',
            'address' => 'Corner St',
            'is_active' => true,
        ]);
        Service::create([
            'name' => 'Wash & Fold Premium',
            'pricing_type' => 'per_load',
            'price' => 75,
            'price_per_load' => 75,
            'is_active' => true,
        ]);

        // Accessing orders.create redirects to orders.index?open_intake=1
        $createResponse = $this->actingAs($staff)->get(route('orders.create'));
        $createResponse->assertRedirect(route('orders.index', ['open_intake' => 1]));

        // Visiting queue line renders the interactive step-by-step wizard modal
        $response = $this->actingAs($staff)->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee('Fast Laundry Intake Wizard');
        $response->assertSee('Customer Dossier & Identification', false);
        $response->assertSee('Aling Nena');
        $response->assertSeeText('Wash & Fold Premium');
        $response->assertSeeText('Wash, Dry & Fold');
        $response->assertSee('Gross Laundry Scale Weight');
        $response->assertSee('Garment Inventory & Counts', false);
        $response->assertSee('Select Washing Detergent Soap');
        $response->assertSeeText('Proceed to Start It All');
    }

    public function test_staff_can_submit_new_intake_order_from_dedicated_create_view(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $service = Service::create([
            'name' => 'Standard Wash & Dry',
            'pricing_type' => 'per_load',
            'price' => 65,
            'price_per_load' => 65,
            'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->post(route('orders.add'), [
            'customer_name' => 'Walk-in Guest Juan',
            'contact_number' => '09198887766',
            'weight_kg' => 7.5,
            'service_ids' => [$service->id],
            'amount_paid' => 65.00,
            'payment_method' => 'cash',
            'redirect_to' => 'orders.index',
            'item_details' => [
                ['item_name' => 'Shirts & Polos', 'quantity' => 5],
                ['item_name' => 'Bath Towels', 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect(route('orders.index'));
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Walk-in Guest Juan',
            'weight_kg' => 7.5,
            'total_price' => 65.00,
            'amount_paid' => 65.00,
            'payment_status' => 'paid',
        ]);
        $this->assertDatabaseHas('order_item_details', [
            'item_name' => 'Shirts & Polos',
            'quantity' => 5,
        ]);
    }

    public function test_staff_submitting_intake_redirects_to_orders_show_with_tender_and_change(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $service = Service::create([
            'name' => 'Commercial Wash & Dry',
            'pricing_type' => 'per_load',
            'price' => 80,
            'price_per_load' => 80,
            'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->post(route('orders.add'), [
            'customer_name' => 'Carla Diaz',
            'contact_number' => '09201112233',
            'weight_kg' => 8.0,
            'service_ids' => [$service->id],
            'tendered_amount' => 100.00,
            'amount_paid' => 80.00,
            'payment_method' => 'cash',
            'redirect_to' => 'orders.show',
            'item_details' => [
                ['item_name' => 'Tops / Shirts', 'quantity' => 6],
            ],
        ]);

        $order = Order::query()->latest('id')->firstOrFail();
        $response->assertRedirect(route('orders.show', $order));

        $this->assertSame('Carla Diaz', $order->customer_name);
        $this->assertEquals(80.00, (float) $order->total_price);
        $this->assertEquals(80.00, (float) $order->amount_paid);
        $this->assertEquals(20.00, (float) $order->change);
        $this->assertSame('paid', $order->payment_status);

        // Verify the order ticket page displays thermal receipt and print action
        $showResponse = $this->actingAs($staff)->get(route('orders.show', $order));
        $showResponse->assertOk();
        $showResponse->assertSee('Counter Ticket Slip');
        $showResponse->assertSee('Print Receipt Slip');
        $showResponse->assertSee('Print Customer Receipt');
        $showResponse->assertSee('TROWA LAUNDRY');
        $showResponse->assertSee('Carla Diaz');
        $showResponse->assertSee('₱80.00');
        $showResponse->assertSee('Tops / Shirts');
        $showResponse->assertSee('New Intake Register');
    }

    public function test_laundry_queue_service_bundles_settlement_and_done_button_flow(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        // Order 1: 1 service (Basic Service), Paid
        $basicOrder = Order::factory()->for($staff, 'creator')->create([
            'order_number' => 'TL-BASIC-01',
            'services' => 'Wash Only',
            'status' => 'ready_for_pickup',
            'total_price' => 50.00,
            'amount_paid' => 50.00,
            'payment_status' => 'paid',
        ]);

        // Order 2: 3 services (Deluxe Service), Unpaid
        $deluxeOrder = Order::factory()->for($staff, 'creator')->create([
            'order_number' => 'TL-DELUXE-01',
            'services' => 'Wash, Dry, Fold',
            'status' => 'ready_for_pickup',
            'total_price' => 150.00,
            'amount_paid' => 0.00,
            'payment_status' => 'unpaid',
        ]);

        // Order 3: 5 services (Premium Service), Partially Paid
        $premiumOrder = Order::factory()->for($staff, 'creator')->create([
            'order_number' => 'TL-PREM-01',
            'services' => 'Wash, Dry, Iron, Fold, Delivery',
            'status' => 'washing',
            'total_price' => 300.00,
            'amount_paid' => 100.00,
            'payment_status' => 'partially_paid',
        ]);

        $response = $this->actingAs($staff)->get(route('orders.index'));
        $response->assertOk();

        // Check tier labels
        $response->assertSee('Basic Service');
        $response->assertSee('Deluxe Service');
        $response->assertSee('Premium Service');

        // Check settlement format: paid order has strike-through price and clean Paid mark
        $response->assertSee('line-through', false);
        $response->assertSee('Paid');

        // Check Quick Dispatch for ready_for_pickup:
        // Basic order is paid: should show "Done" direct button
        $response->assertSee('Done');

        // Deluxe order is unpaid: should show "Pay (₱150.00) ➔"
        $response->assertSee('Pay (₱150.00) ➔', false);

        // Staff can click Done to directly mark the paid order as claimed
        $doneResponse = $this->actingAs($staff)->post(route('orders.status.update', $basicOrder), [
            'status' => 'claimed',
            'redirect_to' => 'orders.index',
        ]);
        $doneResponse->assertRedirect(route('orders.index', ['status' => 'claimed']));
        $basicOrder->refresh();
        $this->assertSame('claimed', $basicOrder->status);
    }

    public function test_laundry_queue_paginates_orders_with_page_controls_and_custom_per_page(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();

        // Create 12 orders for this staff
        for ($i = 1; $i <= 12; $i++) {
            Order::create([
                'created_by' => $staff->id,
                'order_number' => sprintf('TL-PAGE-%02d', $i),
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'weight_kg' => 5.0,
                'number_of_loads' => 1,
                'services' => 'Wash & Fold',
                'total_price' => 50.00,
                'amount_paid' => 50.00,
                'payment_status' => 'paid',
                'status' => 'received',
            ]);
        }

        // Default pagination: 8 items per page -> Page 1 has 8, Page 2 has 4
        $page1Response = $this->actingAs($staff)->get(route('orders.index'));
        $page1Response->assertOk();
        $page1Response->assertSeeText('Page 1 of 2');
        $page1Response->assertSeeText('Showing 1–8 of 12 orders');
        $page1Response->assertSee('TL-PAGE-12'); // latest first
        $page1Response->assertDontSee('TL-PAGE-04'); // on page 2

        // Page 2
        $page2Response = $this->actingAs($staff)->get(route('orders.index', ['page' => 2]));
        $page2Response->assertOk();
        $page2Response->assertSeeText('Page 2 of 2');
        $page2Response->assertSeeText('Showing 9–12 of 12 orders');
        $page2Response->assertSee('TL-PAGE-04');
        $page2Response->assertDontSee('TL-PAGE-12');

        // Custom per_page: 5 items per page -> Page 1 has 5, Total 3 pages
        $customPerPageResponse = $this->actingAs($staff)->get(route('orders.index', ['per_page' => 5]));
        $customPerPageResponse->assertOk();
        $customPerPageResponse->assertSeeText('Page 1 of 3');
        $customPerPageResponse->assertSeeText('Showing 1–5 of 12 orders');
    }

    public function test_laundry_queue_always_renders_pagination_controls_even_on_single_page_and_clamps_pages(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();

        // Create 3 orders (< 8 default per page)
        for ($i = 1; $i <= 3; $i++) {
            Order::create([
                'created_by' => $staff->id,
                'order_number' => sprintf('TL-SINGLE-%02d', $i),
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'weight_kg' => 4.0,
                'number_of_loads' => 1,
                'services' => 'Wash & Fold',
                'total_price' => 50.00,
                'amount_paid' => 50.00,
                'payment_status' => 'paid',
                'status' => 'received',
            ]);
        }

        $response = $this->actingAs($staff)->get(route('orders.index'));
        $response->assertOk();
        $response->assertSeeText('Page 1 of 1');
        $response->assertSeeText('Showing 1–3 of 3 orders');
        $response->assertSeeText('Prev');
        $response->assertSeeText('Next');
        $response->assertSeeText('Per page:');

        // Check out of range page clamps automatically
        $outOfRangeResponse = $this->actingAs($staff)->get(route('orders.index', ['page' => 99]));
        $outOfRangeResponse->assertRedirect(route('orders.index', ['page' => 1]));

        // Check stage tab links do not retain ?page=X
        $responseWithPage = $this->actingAs($staff)->get(route('orders.index', ['page' => 2]));
        // Since there are only 3 orders, page 2 clamps to page 1
        $responseWithPage->assertRedirect(route('orders.index', ['page' => 1]));
    }
}

