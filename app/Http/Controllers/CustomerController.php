<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $customers = Customer::query()
            ->withCount('orders')
            ->when($request->user()->role === 'staff', fn ($query) => $query->where('is_active', true))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'search' => $search,
            'mergeTargets' => $request->user()->role === 'admin'
                ? Customer::query()->where('is_active', true)->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        Customer::create($data);

        return back()->with('status', 'Customer added.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);
        $data['phone'] = $data['phone'] ?? $data['contact_number'];
        $data['contact_number'] = $data['contact_number'] ?? $data['phone'];
        $customer->update($data);

        return back()->with('status', 'Customer details updated.');
    }

    public function merge(Request $request, Customer $customer): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate([
            'target_customer_id' => ['required', 'integer', 'exists:customers,id'],
        ]);
        $target = Customer::query()->where('is_active', true)->findOrFail($data['target_customer_id']);
        abort_if($customer->id === $target->id || $customer->merged_into_id !== null, 422, 'Choose a different active customer record.');

        DB::transaction(function () use ($customer, $target): void {
            $customer->orders()->update(['customer_id' => $target->id]);
            $customer->update(['is_active' => false, 'merged_into_id' => $target->id]);
        });

        return back()->with('status', "Customer records merged into {$target->name}. Order snapshots were preserved.");
    }

    public function toggleActive(Request $request, Customer $customer): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        abort_if($customer->merged_into_id !== null, 422, 'Merged customer records cannot be reactivated.');
        $customer->update(['is_active' => ! $customer->is_active]);

        return back()->with('status', $customer->is_active ? 'Customer activated.' : 'Customer deactivated.');
    }
}
