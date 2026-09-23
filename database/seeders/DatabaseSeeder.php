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
            'role' => 'manager',
        ]);

        User::query()->updateOrCreate(['email' => 'admin123@example.com'], [
            'name' => 'Admin User',
            'username' => 'admin123',
            'email' => 'admin123@example.com',
            'password' => 'pass123',
            'role' => 'admin',
        ]);

        $services = [
            ['name' => 'Wash & Fold', 'icon' => '🧺', 'pricing_type' => 'per_kg', 'price' => 55],
            ['name' => 'Wash & Iron', 'icon' => '👔', 'pricing_type' => 'per_kg', 'price' => 75],
            ['name' => 'Dry Clean', 'icon' => '✨', 'pricing_type' => 'per_kg', 'price' => 120],
            ['name' => 'Express Wash', 'icon' => '⚡', 'pricing_type' => 'per_kg', 'price' => 95],
            ['name' => 'Bedding & Linen', 'icon' => '🛏️', 'pricing_type' => 'per_kg', 'price' => 85],
            ['name' => 'Stain Removal', 'icon' => '🧴', 'pricing_type' => 'flat_rate', 'price' => 50],
            ['name' => 'Shoe Cleaning', 'icon' => '👟', 'pricing_type' => 'flat_rate', 'price' => 150],
            ['name' => 'Fabric Softener', 'icon' => '🌸', 'pricing_type' => 'flat_rate', 'price' => 25],
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
