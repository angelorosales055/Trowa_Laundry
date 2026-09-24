<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Demo accounts for the prototype (passwords are hashed automatically by the User model cast)
        $staff = User::query()->updateOrCreate(['email' => 'staff123@example.com'], [
            'name' => 'Staff User',
            'username' => 'staff123',
            'email' => 'staff123@example.com',
            'password' => 'pass123',
            'role' => 'staff',
        ]);

        User::query()->updateOrCreate(['email' => 'manager123@example.com'], [
            'name' => 'Manager User',
            'username' => 'manager123',
            'email' => 'manager123@example.com',
            'password' => 'pass123',
            'role' => 'admin',
        ]);

        User::query()->updateOrCreate(['email' => 'admin123@example.com'], [
            'name' => 'Admin User',
            'username' => 'admin123',
            'email' => 'admin123@example.com',
            'password' => 'pass123',
            'role' => 'admin',
        ]);

        $services = [
            ['name' => 'Wash', 'icon' => '🧺', 'pricing_type' => 'per_load', 'price' => 60, 'price_per_load' => 60, 'is_active' => true],
            ['name' => 'Dry (40 minutes)', 'icon' => '♨️', 'pricing_type' => 'per_load', 'price' => 70, 'price_per_load' => 70, 'is_active' => true],
            ['name' => 'Detergent', 'icon' => '🧴', 'pricing_type' => 'per_load', 'price' => 15, 'price_per_load' => 15, 'is_active' => true],
            ['name' => 'Fabcon', 'icon' => '🌸', 'pricing_type' => 'per_load', 'price' => 8, 'price_per_load' => 8, 'is_active' => true],
            ['name' => 'Cellophane', 'icon' => '📦', 'pricing_type' => 'per_load', 'price' => 2, 'price_per_load' => 2, 'is_active' => true],
            ['name' => 'Bleach', 'icon' => '🧪', 'pricing_type' => 'per_load', 'price' => 7, 'price_per_load' => 7, 'is_active' => true],
            ['name' => 'Extra Dry (6 minutes)', 'icon' => '⚡', 'pricing_type' => 'per_load', 'price' => 15, 'price_per_load' => 15, 'is_active' => true],
            ['name' => 'Power Wash', 'icon' => '💦', 'pricing_type' => 'per_load', 'price' => 80, 'price_per_load' => 80, 'is_active' => true],
            ['name' => 'Spin Dry', 'icon' => '🌀', 'pricing_type' => 'per_load', 'price' => 15, 'price_per_load' => 15, 'is_active' => true],
            ['name' => 'Fold/Load', 'icon' => '👕', 'pricing_type' => 'per_load', 'price' => 20, 'price_per_load' => 20, 'is_active' => true],
        ];

        foreach ($services as $service) {
            Service::query()->updateOrCreate(['name' => $service['name']], $service);
        }

        $customers = collect([
            ['name' => 'Juan Dela Cruz', 'phone' => '09171234567', 'email' => 'juan@example.com'],
            ['name' => 'Maria Santos', 'phone' => '09181234567', 'email' => 'maria@example.com'],
            ['name' => 'Pedro Reyes', 'phone' => '09191234567', 'email' => 'pedro@example.com'],
        ])->mapWithKeys(fn (array $customer): array => [
            $customer['name'] => Customer::query()->updateOrCreate(['name' => $customer['name']], $customer),
        ]);

        foreach ($customers as $customer) {
            Order::query()
                ->where('customer_name', $customer->name)
                ->whereNull('customer_id')
                ->update(['customer_id' => $customer->id]);
        }

        if (Order::query()->doesntExist()) {
            $orders = [
                [
                    'created_by' => $staff->id,
                    'customer_id' => $customers['Juan Dela Cruz']->id,
                    'customer_name' => 'Juan Dela Cruz',
                    'weight_kg' => 5.2,
                    'services' => 'Wash & Fold',
                    'status' => 'pending',
                ],
                [
                    'created_by' => $staff->id,
                    'customer_id' => $customers['Maria Santos']->id,
                    'customer_name' => 'Maria Santos',
                    'weight_kg' => 3.5,
                    'services' => 'Wash & Iron, Fabric Softener',
                    'status' => 'in-progress',
                ],
                [
                    'created_by' => $staff->id,
                    'customer_id' => $customers['Pedro Reyes']->id,
                    'customer_name' => 'Pedro Reyes',
                    'weight_kg' => 8.0,
                    'services' => 'Dry Clean',
                    'status' => 'ready',
                ],
            ];

            foreach ($orders as $order) {
                Order::create($order);
            }
        }
    }
}
