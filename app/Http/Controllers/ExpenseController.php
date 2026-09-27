<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $expenses = Expense::query()
            ->with('recordedBy')
            ->when($from, fn ($query) => $query->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('expense_date', '<=', $to))
            ->latest('expense_date')
            ->paginate(20)
            ->withQueryString();

        return view('expenses.index', [
            'expenses' => $expenses,
            'total' => Expense::query()
                ->when($from, fn ($query) => $query->whereDate('expense_date', '>=', $from))
                ->when($to, fn ($query) => $query->whereDate('expense_date', '<=', $to))
                ->sum('amount'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'in:utilities,rent,supplies,maintenance,transportation,payroll,other'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'expense_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ]);
        $data['recorded_by'] = $request->user()->id;
        Expense::query()->create($data);

        return back()->with('status', 'Expense recorded.');
    }
}
