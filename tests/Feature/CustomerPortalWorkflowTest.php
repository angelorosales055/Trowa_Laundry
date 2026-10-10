<?php

namespace Tests\Feature;

use App\Mail\CustomerDirectMessageMail;
use App\Mail\CustomerVerificationCodeMail;
use App\Mail\OrderReadyForClaimMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomerPortalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_receives_6_digit_verification_code(): void
    {
        $response = $this->post(route('customer.register.post'), [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria@example.com',
            'phone' => '09171234567',
            'address' => 'Unit 402 Sunshine Residences, Katipunan Ave, QC',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('customer.verify'));

        $user = User::where('email', 'maria@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertEquals('09171234567', $user->phone);
        $this->assertEquals('Unit 402 Sunshine Residences, Katipunan Ave, QC', $user->address);
        $this->assertNotNull($user->verification_code);
        $this->assertEquals(6, strlen($user->verification_code));

        $customer = Customer::find($user->customer_id);
        $this->assertNotNull($customer);
        $this->assertEquals('Maria Santos', $customer->name);
        $this->assertEquals('maria@example.com', $customer->email);
        $this->assertEquals('Unit 402 Sunshine Residences, Katipunan Ave, QC', $customer->address);
    }

    public function test_customer_can_verify_code_and_access_portal(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'verification_code' => '654321',
            'email_verified_at' => null,
        ]);
        $customer = Customer::create([
            'name' => $user->name,
            'email' => $user->email,
            'contact_number' => '09171234567',
            'address' => '77 Trowa Street',
        ]);
        $user->customer_id = $customer->id;
        $user->save();

        $response = $this->post(route('customer.verify.post'), [
            'email' => $user->email,
            'code' => '654321',
        ]);

        $response->assertRedirect(route('customer.portal'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->verification_code);

        // Access portal
        $this->actingAs($user);
        $portalResponse = $this->get(route('customer.portal'));
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('Verified Customer Member');
        $portalResponse->assertSee('Washing Machine Fleet Limit');
    }

    public function test_8_washing_machine_fleet_limit_rejects_greater_than_64_kg(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $customer = Customer::create([
            'name' => $user->name,
            'email' => $user->email,
            'address' => '123 Laundry Blvd',
        ]);
        $user->customer_id = $customer->id;
        $user->save();

        $service = Service::create([
            'name' => 'Wash, Dry & Fold',
            'pricing_type' => 'per_kg',
            'price' => 35,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Try submitting 100 kg (requires ~13 machines, exceeding our 8 machines)
        $response = $this->post(route('customer.orders.submit'), [
            'service_ids' => [$service->id],
            'weight_kg' => 100, // 100 kg > 64 kg maximum fleet capacity
            'garment_count' => 50,
            'soap_preference' => 'Ariel Powder + Downy Passion',
            'customer_notes' => 'Bulk laundry',
        ]);

        $response->assertSessionHasErrors('weight_kg');

        $this->assertDatabaseMissing('orders', [
            'customer_id' => $customer->id,
            'weight_kg' => 100,
        ]);
    }

    public function test_customer_submits_intake_within_fleet_limit_and_staff_confirms(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now(), 'address' => 'Katipunan Dorm 2B']);
        $customer = Customer::create([
            'name' => $user->name,
            'email' => $user->email,
            'address' => 'Katipunan Dorm 2B',
        ]);
        $user->customer_id = $customer->id;
        $user->save();

        $service = Service::create([
            'name' => 'Wash, Dry & Fold',
            'pricing_type' => 'per_kg',
            'price' => 35,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Submit self-service intake of 16 kg (2 loads, well within 8 machines)
        $response = $this->post(route('customer.orders.submit'), [
            'service_ids' => [$service->id],
            'weight_kg' => 16.0,
            'garment_count' => 12,
            'soap_preference' => 'Tide Liquid + Downy Sunrise Fresh',
            'customer_notes' => 'Please fold neatly with hangers',
        ]);

        $response->assertRedirect(route('customer.portal'));

        $order = Order::where('customer_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending_confirmation', $order->status);
        $this->assertEquals(16.0, (float) $order->weight_kg);
        $this->assertEquals('Tide Liquid + Downy Sunrise Fresh', $order->soap_preference);
        $this->assertEquals('Please fold neatly with hangers', $order->customer_notes);

        // Staff sees pending confirmation order in queue and confirms it
        $this->actingAs($staff);
        $confirmResponse = $this->post(route('orders.confirm-online', $order));
        $confirmResponse->assertRedirect();

        $order->refresh();
        $this->assertEquals('received', $order->status);
    }

    public function test_staff_can_reject_online_order_with_reason(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();

        $order = Order::create([
            'order_number' => 'ORD-TEST-REJECT',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'created_by' => $staff->id,
            'services' => 'Wash, Dry & Fold',
            'weight_kg' => 8.0,
            'total_price' => 600,
            'status' => 'pending_confirmation',
            'payment_status' => 'unpaid',
            'payment_method' => 'cash',
        ]);

        $this->actingAs($staff);
        $rejectResponse = $this->post(route('orders.reject-online', $order), [
            'rejection_reason' => 'Delicates machine is currently under scheduled maintenance until 4 PM.',
        ]);

        $rejectResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals('Delicates machine is currently under scheduled maintenance until 4 PM.', $order->rejection_reason);
    }

    public function test_customer_can_submit_star_rating_and_comment(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $customer = Customer::create([
            'name' => $user->name,
            'email' => $user->email,
        ]);
        $user->customer_id = $customer->id;
        $user->save();

        $order = Order::create([
            'order_number' => 'ORD-RATE-101',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'created_by' => $user->id,
            'services' => 'Wash, Dry & Fold',
            'weight_kg' => 8.0,
            'total_price' => 300,
            'status' => 'claimed',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        $this->actingAs($user);
        $rateResponse = $this->post(route('customer.orders.rate', $order), [
            'rating' => 5,
            'rating_comment' => 'Fresh scent and prompt turnaround! Very happy with Trowa Laundry.',
        ]);

        $rateResponse->assertRedirect(route('customer.portal'));

        $order->refresh();
        $this->assertEquals(5, $order->rating);
        $this->assertEquals('Fresh scent and prompt turnaround! Very happy with Trowa Laundry.', $order->rating_comment);
        $this->assertNotNull($order->rated_at);
    }

    public function test_customer_receives_verification_email_at_inputted_email(): void
    {
        Mail::fake();

        $inputtedEmail = 'user1_custom_test@gmail.com';

        $response = $this->post(route('customer.register.post'), [
            'first_name' => 'User1',
            'last_name' => 'Customer',
            'email' => $inputtedEmail,
            'phone' => '09170001111',
            'address' => 'Block 5 Lot 2, Commonwealth, Quezon City',
            'password' => 'pass1234',
            'password_confirmation' => 'pass1234',
        ]);

        $response->assertRedirect(route('customer.verify'));

        Mail::assertSent(CustomerVerificationCodeMail::class, function ($mail) use ($inputtedEmail) {
            return $mail->hasTo($inputtedEmail);
        });
    }

    public function test_staff_can_send_direct_email_message_to_customer_email(): void
    {
        Mail::fake();

        $staff = User::factory()->create(['role' => 'staff']);
        $targetEmail = 'user1_message_test@example.com';
        $customer = Customer::create([
            'name' => 'User One',
            'email' => $targetEmail,
            'phone' => '09172223333',
            'address' => 'Quezon City',
        ]);

        $this->actingAs($staff);

        $response = $this->post(route('customers.send-email', $customer), [
            'subject' => 'Important update about your blanket',
            'message' => 'Please note that your wool blanket required gentle cycle air drying.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Mail::assertSent(CustomerDirectMessageMail::class, function ($mail) use ($targetEmail) {
            return $mail->hasTo($targetEmail) && $mail->customSubject === 'Important update about your blanket';
        });
    }

    public function test_ready_for_claim_email_is_sent_to_customer_email_when_order_is_ready(): void
    {
        Mail::fake();

        $staff = User::factory()->create(['role' => 'staff']);
        $targetEmail = 'user1_order_claim@example.com';
        $customer = Customer::create([
            'name' => 'User One',
            'email' => $targetEmail,
            'phone' => '09178889999',
            'address' => 'Diliman, QC',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-CLAIM-TEST',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'created_by' => $staff->id,
            'services' => 'Wash, Dry & Fold',
            'weight_kg' => 7.0,
            'total_price' => 245,
            'status' => 'folding',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        $this->actingAs($staff);

        $response = $this->post(route('orders.status.update', $order), [
            'status' => 'ready_for_pickup',
            'notes' => 'Folded neatly and wrapped in protective plastic.',
        ]);

        $response->assertRedirect();

        Mail::assertSent(OrderReadyForClaimMail::class, function ($mail) use ($targetEmail) {
            return $mail->hasTo($targetEmail);
        });
    }

    public function test_customer_intake_sanitizes_zero_quantity_items_and_supports_custom_and_stock_soap(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $customer = Customer::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '09171112222',
            'address' => 'Katipunan Ave, QC',
        ]);
        $user->customer_id = $customer->id;
        $user->save();

        $service = Service::create([
            'name' => 'Wash, Dry & Fold',
            'pricing_type' => 'per_kg',
            'price' => 35,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Submit form with some zero-quantity items (which previously threw validation error)
        // and bringing own soap custom formulation
        $response = $this->post(route('customer.orders.submit'), [
            'service_ids' => [$service->id],
            'weight_kg' => 12.0,
            'soap_preference' => 'own',
            'own_soap_custom' => 'Ariel Pods + Downy Floral Mist',
            'customer_notes' => 'Delicate wool tops inside mesh bag',
            'item_details' => [
                ['item_name' => 'Tops / Shirts', 'quantity' => 5],
                ['item_name' => 'Pants / Jeans', 'quantity' => 0], // Zero quantity should be sanitized without failing validation!
                ['item_name' => 'Towels', 'quantity' => 3],
            ],
        ]);

        $response->assertRedirect(route('customer.portal'));
        $response->assertSessionHasNoErrors();

        $order = Order::where('customer_id', $customer->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertStringContainsString('Ariel Pods + Downy Floral Mist', $order->soap_preference);
        $this->assertEquals('pending_confirmation', $order->status);

        // Verify that only the items with quantity >= 1 were saved to item_details
        $savedItems = $order->itemDetails;
        $this->assertCount(2, $savedItems);
        $this->assertEquals(['Tops / Shirts', 'Towels'], $savedItems->pluck('item_name')->all());
        $this->assertEquals([5, 3], $savedItems->pluck('quantity')->all());
    }
}
