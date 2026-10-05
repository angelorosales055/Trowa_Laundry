<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPortalAndInsightsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_portal_has_strict_role_separation_from_staff_operations(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Manager']);
        $staff = User::factory()->create(['role' => 'staff', 'name' => 'Counter Staff']);

        // Check Admin View
        $adminResponse = $this->actingAs($admin)->get(route('dashboard'));
        $adminResponse->assertOk();
        $adminResponse->assertSee('Executive Management');
        $adminResponse->assertSee('Customer Insights');
        $adminResponse->assertSee('Business Reports & Plans');
        // Admin should NOT see floor operational navigation or quick intake button
        $adminResponse->assertDontSee('Staff Operations');
        $adminResponse->assertDontSee('New Laundry Intake');

        // Check Staff View
        $staffResponse = $this->actingAs($staff)->get(route('dashboard'));
        $staffResponse->assertOk();
        $staffResponse->assertSee('Staff Operations');
        $staffResponse->assertSee('New Laundry Intake');
        $staffResponse->assertSee('Laundry Queue');
        $staffResponse->assertSee('Machine Bay Showroom');
        $staffResponse->assertDontSee('Executive Management');
        $staffResponse->assertDontSee('Customer Insights');
    }

    public function test_admin_can_access_customer_insights_with_rfm_tiers_and_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        $vipCustomer = Customer::create([
            'name' => 'Maria Clara',
            'phone' => '09171112233',
            'is_active' => true,
        ]);

        $newCustomer = Customer::create([
            'name' => 'Juan Luna',
            'phone' => '09182223344',
            'is_active' => true,
        ]);

        // Maria Clara has 5 orders worth 2500
        for ($i = 1; $i <= 5; $i++) {
            Order::create([
                'created_by' => $staff->id,
                'order_number' => "TL-VIP-00{$i}",
                'customer_id' => $vipCustomer->id,
                'customer_name' => $vipCustomer->name,
                'weight_kg' => 7.0,
                'number_of_loads' => 1,
                'services' => 'Wash & Fold',
                'total_price' => 500.00,
                'amount_paid' => 500.00,
                'payment_status' => 'paid',
                'status' => 'claimed',
            ]);
        }

        // Juan Luna has 1 order worth 120
        Order::create([
            'created_by' => $staff->id,
            'order_number' => 'TL-NEW-0001',
            'customer_id' => $newCustomer->id,
            'customer_name' => $newCustomer->name,
            'weight_kg' => 5.0,
            'number_of_loads' => 1,
            'services' => 'Wash & Dry',
            'total_price' => 120.00,
            'amount_paid' => 120.00,
            'payment_status' => 'paid',
            'status' => 'claimed',
        ]);

        // Staff cannot view Customer Insights
        $this->actingAs($staff)->get(route('customers.insights'))->assertForbidden();

        // Admin can view Customer Insights
        $response = $this->actingAs($admin)->get(route('customers.insights'));
        $response->assertOk();
        $response->assertSee('Customer Insights');
        $response->assertSee('Avg Customer LTV');
        $response->assertSee('Repeat Visit Rate');
        $response->assertSee('Maria Clara');
        $response->assertSee('VIP Champions');
        $response->assertSee('Top Spending Customer Leaderboard');
        $response->assertSee('Order Frequency Cohorts');
        $response->assertSee('Retention & Growth Advisor');
    }

    public function test_admin_can_view_supply_inventory_smart_helper_and_burn_rates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $item = InventoryItem::create([
            'name' => 'Hypoallergenic Detergent',
            'unit' => 'L',
            'quantity_on_hand' => 2.0,
            'low_stock_threshold' => 5.0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('inventory.index'));
        $response->assertOk();
        $response->assertSee('Trowa Smart Inventory Advisor');
        $response->assertSee('Hypoallergenic Detergent');
        $response->assertSee('Stock Alert: Hypoallergenic Detergent');
        $response->assertSee('Low Stock Alert');
        $response->assertSee('L Available');
    }

    public function test_admin_dashboard_has_presets_and_capacity_telemetry(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard', ['preset' => '7d']));
        $response->assertOk();
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Daily Order Volume');
        $response->assertSee('Plant Capacity &amp; Fleet Load', false);
        $response->assertSee('Washer Bay (8 Units');
        $response->assertSee('Dryer Bay (8 Units');
    }

    public function test_admin_can_view_business_plan_projections_and_unit_economics_in_reports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        $customer = Customer::create(['name' => 'Plan Customer', 'is_active' => true]);

        Order::create([
            'created_by' => $staff->id,
            'order_number' => 'TL-PLAN-001',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'weight_kg' => 10.0,
            'number_of_loads' => 2,
            'services' => 'Wash & Fold',
            'total_price' => 300.00,
            'amount_paid' => 300.00,
            'payment_status' => 'paid',
            'status' => 'claimed',
        ]);

        Expense::create([
            'recorded_by' => $admin->id,
            'category' => 'supplies',
            'description' => 'Bulk Detergent Gallons',
            'amount' => 100.00,
            'expense_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->get(route('reports'));
        $response->assertOk();
        $response->assertSee('Reports & Business Plan');
        $response->assertSee('Strategic Business Plan & Forward Projections');
        $response->assertSee('Next 30 Days Forecast');
        $response->assertSee('Quarterly (90-Day) Outlook');
        $response->assertSee('Monthly Break-Even Target');
        $response->assertSee('Unit Economics Breakdown');
        $response->assertSee('Average Revenue per Kilogram');
        $response->assertSee('Operating Cost per Kilogram');

        // Print Report
        $printResponse = $this->actingAs($admin)->get(route('reports.print'));
        $printResponse->assertOk();
        $printResponse->assertSee('Executive Business Plan & 30-Day Projections');
        $printResponse->assertSee('Unit Economics & Fleet Capacity');
    }

    public function test_billing_is_combined_into_reports_module_and_has_interactive_tabs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create(['name' => 'Commercial VIP']);

        Order::create([
            'created_by' => $admin->id,
            'order_number' => 'TL-BILL-100',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'weight_kg' => 12.0,
            'number_of_loads' => 2,
            'services' => 'Wash & Fold, Steam Press',
            'total_price' => 540.00,
            'amount_paid' => 540.00,
            'payment_status' => 'paid',
            'status' => 'claimed',
        ]);

        // 1. Sidebar does not have separate Billing & Invoices navigation link
        $dashboardResponse = $this->actingAs($admin)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertDontSee('Billing & Invoices');

        // 2. Reports page has interactive tab buttons and directives with Take Action
        $reportsResponse = $this->actingAs($admin)->get(route('reports'));
        $reportsResponse->assertOk();
        $reportsResponse->assertSee('id="tab-btn-plan"', false);
        $reportsResponse->assertSee('id="tab-btn-billing"', false);
        $reportsResponse->assertSee('id="tab-btn-financials"', false);
        $reportsResponse->assertSee('id="tab-btn-performance"', false);
        $reportsResponse->assertSee('Take Action');
        $reportsResponse->assertSee('Billing &amp; Invoices Ledger', false);

        // 3. Billing route delegates to reports and displays settled invoices ledger
        $billingResponse = $this->actingAs($admin)->get(route('billing'));
        $billingResponse->assertOk();
        $billingResponse->assertSee('TL-BILL-100');
        $billingResponse->assertSee('Commercial VIP');
        $billingResponse->assertSee('₱540.00');
        $billingResponse->assertSee('Total Collected');
        $billingResponse->assertSee('Settled Transactions');
        $billingResponse->assertSee('Average Ticket Size');
    }

    public function test_supply_inventory_has_interactive_tabs_and_take_action_modal_flow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = InventoryItem::create([
            'name' => 'Stain Remover Pro',
            'unit' => 'bottles',
            'quantity_on_hand' => 1,
            'low_stock_threshold' => 10,
        ]);

        $response = $this->actingAs($admin)->get(route('inventory.index'));
        $response->assertOk();
        $response->assertSee('id="inv-tab-btn-advisor"', false);
        $response->assertSee('id="inv-tab-btn-stock"', false);
        $response->assertSee('id="inv-tab-btn-recipes"', false);
        $response->assertSee('Take Action');
        $response->assertSee('Standard Operational Guidance');
        $response->assertSee('Record Immediate Stock-In Arrival');

        // Test replenishing via stock-in movement
        $postResponse = $this->actingAs($admin)->post(route('inventory.movement', $item), [
            'movement_type' => 'stock_in',
            'quantity' => 15,
            'notes' => 'Replenished via Take Action recommendation',
        ]);
        $postResponse->assertRedirect();

        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'quantity_on_hand' => '16.000',
        ]);
    }

    public function test_admin_dashboard_has_interactive_graphs_16_machine_fleet_and_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create(['name' => 'Rush Hour Customer']);

        Order::create([
            'created_by' => $admin->id,
            'order_number' => 'TL-GRAPH-01',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'weight_kg' => 8.0,
            'number_of_loads' => 1,
            'services' => 'Wash & Fold',
            'total_price' => 180.00,
            'amount_paid' => 180.00,
            'payment_status' => 'paid',
            'status' => 'washing',
            'created_at' => now()->setTime(14, 30), // Afternoon rush
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertOk();

        // 1. Multi-metric chart layer controls
        $response->assertSee('id="chart-mode-volume"', false);
        $response->assertSee('id="chart-mode-rev"', false);
        $response->assertSee('id="chart-mode-kg"', false);

        // 2. Peak Rush Hours chart
        $response->assertSee('Peak Rush Hours &amp; Drop-off Heatmap', false);
        $response->assertSee('Afternoon (2pm–5pm)');

        // 3. Services demand donut breakdown
        $response->assertSee('Services Volume &amp; Demand Breakdown', false);

        // 4. 16-Unit Machine Fleet (8 washers + 8 dryers)
        $response->assertSee('Washer #1');
        $response->assertSee('Washer #8');
        $response->assertSee('Dryer #1');
        $response->assertSee('Dryer #8');
        $response->assertSee('W-01');
        $response->assertSee('D-08');

        // 5. Quick Actions
        $response->assertSee('+ Log Expense');
        $response->assertSee('Promo Booster');
        $response->assertSee('Live Ping');

        // 6. Test quick expense submission directly from dashboard modal form
        $expenseResponse = $this->actingAs($admin)->post(route('expenses.store'), [
            'category' => 'supplies',
            'description' => 'Fast Detergent Gallon from Dashboard',
            'amount' => 450.00,
            'expense_date' => now()->toDateString(),
            'reference_number' => 'DASH-EXP-01',
        ]);
        $expenseResponse->assertRedirect();
        $this->assertDatabaseHas('expenses', [
            'description' => 'Fast Detergent Gallon from Dashboard',
            'amount' => '450.00',
        ]);
    }
}

