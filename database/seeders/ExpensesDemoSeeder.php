<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class ExpensesDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recordedBy = User::query()->where('role', 'admin')->orderBy('id')->first();

        if (! $recordedBy) {
            throw new RuntimeException('Create an admin account before seeding sample expenses.');
        }

        $expenseDate = now()->startOfMonth();
        $expenses = [
            ['category' => 'rent', 'description' => 'Sample expense - shop rent', 'amount' => '12000.00', 'reference_number' => 'DEMO-EXP-RENT'],
            ['category' => 'utilities', 'description' => 'Sample expense - electricity and water', 'amount' => '3850.00', 'reference_number' => 'DEMO-EXP-UTILITIES'],
            ['category' => 'supplies', 'description' => 'Sample expense - detergent and laundry supplies', 'amount' => '2450.00', 'reference_number' => 'DEMO-EXP-SUPPLIES'],
            ['category' => 'maintenance', 'description' => 'Sample expense - washing machine maintenance', 'amount' => '1800.00', 'reference_number' => 'DEMO-EXP-MAINTENANCE'],
            ['category' => 'transportation', 'description' => 'Sample expense - supply pickup and delivery', 'amount' => '650.00', 'reference_number' => 'DEMO-EXP-TRANSPORT'],
        ];

        foreach ($expenses as $index => $expense) {
            Expense::query()->firstOrCreate(
                ['reference_number' => $expense['reference_number']],
                [
                    ...$expense,
                    'expense_date' => $expenseDate->copy()->addDays($index)->toDateString(),
                    'recorded_by' => $recordedBy->id,
                ],
            );
        }
    }
}
