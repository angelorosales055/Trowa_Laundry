<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class InventoryDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recordedBy = User::query()->where('role', 'admin')->orderBy('id')->first()
            ?? User::query()->where('role', 'staff')->orderBy('id')->first();

        if (! $recordedBy) {
            throw new RuntimeException('Create an admin or staff account before seeding sample inventory.');
        }

        $items = [
            ['name' => 'Detergent', 'unit' => 'L', 'quantity_on_hand' => 20, 'low_stock_threshold' => 5],
            ['name' => 'Fabric Conditioner', 'unit' => 'L', 'quantity_on_hand' => 10, 'low_stock_threshold' => 3],
            ['name' => 'Bleach', 'unit' => 'L', 'quantity_on_hand' => 5, 'low_stock_threshold' => 1],
        ];

        foreach ($items as $attributes) {
            $item = InventoryItem::query()->firstOrCreate(
                ['name' => $attributes['name']],
                [
                    ...$attributes,
                    'is_active' => true,
                ],
            );

            if ($item->wasRecentlyCreated && (float) $item->quantity_on_hand > 0) {
                $item->movements()->create([
                    'recorded_by' => $recordedBy->id,
                    'movement_type' => 'opening_stock',
                    'quantity_change' => $item->quantity_on_hand,
                    'notes' => 'Sample opening stock; verify and adjust to the actual physical count.',
                ]);
            }
        }
    }
}
