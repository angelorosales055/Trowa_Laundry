<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffDashboardUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_dashboard_renders_with_lively_machine_bay_and_retro_elements(): void
    {
        $staff = User::factory()->create([
            'name' => 'Carla Mendoza',
            'username' => 'carla_staff',
            'role' => 'staff',
        ]);

        $customer = Customer::create([
            'name' => 'Roberto Cruz',
            'phone' => '09170001122',
            'address' => '123 Sampaguita St',
            'is_active' => true,
        ]);

        $washService = Service::create([
            'name' => 'Power Wash',
            'icon' => '🧺',
            'pricing_type' => 'per_load',
            'price' => 60,
            'price_per_load' => 60,
            'is_active' => true,
        ]);

        $order = Order::create([
            'created_by' => $staff->id,
            'order_number' => 'TL-TEST-LIVE-1',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'weight_kg' => 6.5,
            'number_of_loads' => 1,
            'services' => 'Power Wash',
            'total_price' => 60,
            'amount_paid' => 60,
            'payment_status' => 'paid',
            'status' => 'washing',
        ]);

        $response = $this->actingAs($staff)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Carla Mendoza');
        $response->assertSeeText('Live Machine Bay & Washer Rack');
        $response->assertSee('Fast Laundry Intake');
        $response->assertSee('Washer #01');
        $response->assertSee('Dryer #01');
        $response->assertSee('TL-TEST-LIVE-1');
        $response->assertSee('Roberto Cruz');
        $response->assertSee('6.5 kg');
        $response->assertSee('PAID');
    }

    public function test_login_page_renders_with_retro_brand_portal(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Trowa Laundry');
        $response->assertSeeText('Staff & Counter Portal');
        $response->assertSee('Cleaner clothes');
        $response->assertSee('Sign In to Station');
        $response->assertSee('Register Now / Sign Up');
    }

    public function test_login_page_renders_with_interactive_mini_robot_and_eyes(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('trowa-robot-svg');
        $response->assertSee('robot-left-pupil');
        $response->assertSee('robot-right-pupil');
        $response->assertSee('robot-eyes-open');
        $response->assertSee('robot-eyes-closed');
        $response->assertSee('robot-paws');
        $response->assertSee('robot-speech');
        $response->assertSee('toggle-password');
        $response->assertSee('setRobotEyesCovered');
        $response->assertSee('robot-spinning-face');
        $response->assertSee('robot-shaking');
        $response->assertSee('playSpinCycleAudio');
        $response->assertSee('robot-bubbles-layer');
        $response->assertSee('spawnSoapBubbles');
        $response->assertSee('playBubblePopAudio');
        $response->assertSee('robot-right-paw');
        $response->assertSee('triggerSneakPeek');
        $response->assertSee('playSneakPeekAudio');
    }

    public function test_login_page_renders_with_scroll_to_reveal_retro_washing_machine_drum(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('scroll-prompt-btn');
        $response->assertSee('trowa-drum-portal');
        $response->assertSee('portal-drum-door');
        $response->assertSee('portal-door-latch');
        $response->assertSee('portal-drum-interior');
        $response->assertSee('portal-chamber-1');
        $response->assertSee('portal-chamber-2');
        $response->assertSee('portal-chamber-3');
        $response->assertSee('return-to-station-btn');
        $response->assertSee('updateDrumPortal');
    }
}
