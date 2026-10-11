<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffAndRatingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_staff_directory_and_perform_crud(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Master Manager',
            'email' => 'admin@trowalaundry.com',
        ]);

        $this->actingAs($admin);

        // 1. List staff
        $listResponse = $this->get(route('admin.staff.index'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('Staff &amp; Operator Accounts', false);

        // 2. Create staff
        $createResponse = $this->post(route('admin.staff.store'), [
            'name' => 'John Operator',
            'username' => 'john_operator',
            'email' => 'john.staff@trowalaundry.com',
            'phone' => '09181112233',
            'role' => 'staff',
            'password' => 'StaffPass123!',
            'password_confirmation' => 'StaffPass123!',
        ]);

        $createResponse->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'John Operator',
            'email' => 'john.staff@trowalaundry.com',
            'role' => 'staff',
        ]);

        $staffUser = User::where('email', 'john.staff@trowalaundry.com')->first();
        $this->assertNotNull($staffUser);

        // 3. Update staff
        $updateResponse = $this->put(route('admin.staff.update', $staffUser), [
            'name' => 'John Senior Operator',
            'username' => 'john_senior',
            'email' => 'john.senior@trowalaundry.com',
            'phone' => '09189998877',
            'role' => 'staff',
        ]);

        $updateResponse->assertRedirect(route('admin.staff.index'));
        $staffUser->refresh();
        $this->assertEquals('John Senior Operator', $staffUser->name);
        $this->assertEquals('john.senior@trowalaundry.com', $staffUser->email);

        // 4. Delete staff
        $deleteResponse = $this->delete(route('admin.staff.destroy', $staffUser));
        $deleteResponse->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseMissing('users', ['id' => $staffUser->id]);
    }

    public function test_admin_cannot_delete_own_account_or_revoke_own_role(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin.root@trowalaundry.com',
        ]);

        $this->actingAs($admin);

        // Try deleting own account
        $deleteResponse = $this->delete(route('admin.staff.destroy', $admin));
        $deleteResponse->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        // Try changing own role to staff
        $updateResponse = $this->put(route('admin.staff.update', $admin), [
            'name' => $admin->name,
            'username' => $admin->username ?? 'admin_root',
            'email' => $admin->email,
            'role' => 'staff',
        ]);
        $updateResponse->assertSessionHasErrors('role');
        $admin->refresh();
        $this->assertEquals('admin', $admin->role);
    }

    public function test_non_admin_cannot_access_staff_management(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);

        $response = $this->get(route('admin.staff.index'));
        $response->assertStatus(403);
    }

    public function test_customer_portal_dashboard_mascot_and_expense_analytics(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $customer = Customer::create([
            'name' => $user->name,
            'email' => $user->email,
        ]);
        $user->customer_id = $customer->id;
        $user->save();

        Order::create([
            'order_number' => 'ORD-EXP-1',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'created_by' => $user->id,
            'services' => 'Wash, Dry & Fold',
            'weight_kg' => 8.0,
            'total_price' => 280,
            'status' => 'completed',
            'payment_status' => 'paid',
            'created_at' => now()->subDays(2),
        ]);

        $this->actingAs($user);

        // Test Dashboard tab with week expense filter
        $dashResponse = $this->get(route('customer.portal', ['tab' => 'dashboard', 'expense_filter' => 'week']));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Trowa-Bot');
        $dashResponse->assertSee('My Laundry Spending &amp; Expenses', false);
        $dashResponse->assertSee('280.00');

        // Test History tab
        $historyResponse = $this->get(route('customer.portal', ['tab' => 'history']));
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee('Past Transaction Records &amp; Order History', false);
        $historyResponse->assertSee('ORD-EXP-1');
    }

    public function test_customer_rating_submission_and_admin_reports_with_pdf_print(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $customer = Customer::create([
            'name' => 'Happy Patron',
            'email' => $user->email,
        ]);
        $user->customer_id = $customer->id;
        $user->save();

        $order = Order::create([
            'order_number' => 'ORD-RATE-1',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'created_by' => $user->id,
            'services' => 'Wash, Dry & Fold',
            'weight_kg' => 6.0,
            'total_price' => 210,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($user);

        // Submit 5-star rating with review
        $rateResponse = $this->post(route('customer.orders.rate', $order), [
            'rating' => 5,
            'rating_comment' => 'Smells fresh and warm! Clothes were folded crisply.',
        ]);

        $rateResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals(5, $order->rating);
        $this->assertEquals('Smells fresh and warm! Clothes were folded crisply.', $order->rating_comment);
        $this->assertNotNull($order->rated_at);

        // Now login as Admin and check reports ratings tab and print export
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $reportsResponse = $this->get(route('reports'));
        $reportsResponse->assertStatus(200);
        $reportsResponse->assertSee('Ratings, Feedback &amp; Recs', false);
        $reportsResponse->assertSee('Smells fresh and warm! Clothes were folded crisply.');
        $reportsResponse->assertSee('5.0 / 5.0');
        $reportsResponse->assertSee('Export Report as PDF');

        // Check print report endpoint
        $printResponse = $this->get(route('reports.print', ['auto' => 1]));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Trowa Laundry House');
        $printResponse->assertSee('Smells fresh and warm! Clothes were folded crisply.');
        $printResponse->assertSee('window.print()');
    }

    public function test_staff_cannot_access_feedbacks_only_admin_can(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Staff is forbidden from accessing the feedback module
        $this->actingAs($staff);
        $staffResponse = $this->get(route('customers.feedbacks'));
        $staffResponse->assertStatus(403);

        // Admin has full access to feedback review
        $this->actingAs($admin);
        $adminResponse = $this->get(route('customers.feedbacks'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Customer Feedbacks &amp; Ratings', false);
    }

    public function test_customer_can_see_their_own_feedback_in_portal_history(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $customer = Customer::create([
            'name' => 'Feedback Reviewer',
            'email' => $user->email,
        ]);
        $user->customer_id = $customer->id;
        $user->save();

        $order = Order::create([
            'order_number' => 'ORD-MY-FEEDBACK-99',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'created_by' => $user->id,
            'services' => 'Wash, Dry & Fold',
            'weight_kg' => 8.0,
            'total_price' => 280,
            'status' => 'claimed',
            'payment_status' => 'paid',
            'rating' => 5,
            'rating_comment' => 'Exceptional service! Fabric softener smells great.',
            'rated_at' => now(),
        ]);

        $this->actingAs($user);
        $response = $this->get(route('customer.portal', ['tab' => 'history']));
        $response->assertStatus(200);

        // Customer sees their review in the table row
        $response->assertSee('ORD-MY-FEEDBACK-99');
        $response->assertSee('Exceptional service! Fabric softener smells great.');

        // Customer sees their feedback log card
        $response->assertSee('My Service Reviews &amp; Feedback History', false);
        $response->assertSee('1 Review Submitted');
    }

    public function test_inventory_shows_stock_in_and_stock_out_dates_supplier_details_and_contact(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $item = \App\Models\InventoryItem::create([
            'name' => 'Surf Detergent Sachet',
            'unit' => 'sachet',
            'quantity_on_hand' => 100,
            'low_stock_threshold' => 20,
            'is_active' => true,
        ]);

        $supplier = \App\Models\Supplier::create([
            'name' => 'CleanPro Chemicals Supply',
            'contact_person' => 'Juan Dela Cruz',
            'phone' => '0917-888-9999',
            'email' => 'juan@cleanprosupply.ph',
            'address' => 'Warehouse 12, Metro Depot',
            'payment_terms' => 'COD',
            'is_active' => true,
        ]);

        $supplierOrder = \App\Models\SupplierOrder::create([
            'order_number' => 'PO-20261011-TEST',
            'supplier_id' => $supplier->id,
            'status' => 'received',
            'total_cost' => 1500,
            'recorded_by' => $admin->id,
            'received_at' => now(),
            'items_payload' => [
                [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'unit' => $item->unit,
                    'quantity_added' => 50,
                    'new_balance' => 150,
                ]
            ],
            'notes' => 'Commercial restock order',
        ]);

        $item->movements()->create([
            'recorded_by' => $admin->id,
            'movement_type' => 'stock_in',
            'quantity_change' => 50,
            'notes' => 'Supplier Delivery PO-20261011-TEST',
        ]);

        $response = $this->get(route('inventory.index', ['tab' => 'stock']));
        $response->assertStatus(200);

        // Date of Stock In / Out is visible
        $response->assertSee('Date &amp; Time of Stock In / Out', false);
        $response->assertSee('Stock In (Arrival)');

        // Switch to suppliers tab
        $suppResponse = $this->get(route('inventory.index', ['tab' => 'suppliers']));
        $suppResponse->assertStatus(200);

        // Supplier details and contact links
        $suppResponse->assertSee('CleanPro Chemicals Supply');
        $suppResponse->assertSee('Juan Dela Cruz');
        $suppResponse->assertSee('mailto:juan@cleanprosupply.ph');
        $suppResponse->assertSee('PO-20261011-TEST');
        $suppResponse->assertSee('View Invoice');
    }
}
