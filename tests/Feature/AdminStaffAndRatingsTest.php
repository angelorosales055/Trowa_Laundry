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
}
