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
            ['name' => 'Surf Powder Detergent', 'unit' => 'sachet', 'quantity_on_hand' => 45, 'low_stock_threshold' => 15],
            ['name' => 'Surf Fabcon Blossom Fresh', 'unit' => 'sachet', 'quantity_on_hand' => 30, 'low_stock_threshold' => 10],
            ['name' => 'Downy Sunrise Fresh', 'unit' => 'sachet', 'quantity_on_hand' => 4, 'low_stock_threshold' => 10],
            ['name' => 'Ariel Sunrise Power', 'unit' => 'sachet', 'quantity_on_hand' => 0, 'low_stock_threshold' => 8],
            ['name' => 'Commercial Liquid Detergent', 'unit' => 'L', 'quantity_on_hand' => 20, 'low_stock_threshold' => 5],
            ['name' => 'Fabric Softener Drum', 'unit' => 'L', 'quantity_on_hand' => 10, 'low_stock_threshold' => 3],
            ['name' => 'Color-Safe Bleach', 'unit' => 'L', 'quantity_on_hand' => 5, 'low_stock_threshold' => 1],
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
