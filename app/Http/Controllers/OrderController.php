<?php

namespace App\Http\Controllers;

use App\Actions\CreateLaundryOrderAction;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = $request->string('search')->trim()->toString();
        $orders = Order::query()
            ->with(['customer', 'creator'])
            ->when($user->role === 'staff', fn ($query) => $query->where('created_by', $user->id))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('schedule.index', [
            'orders' => $orders,
            'status' => '',
            'search' => $search,
            'module' => 'management',
            'customers' => Customer::query()->orderBy('name')->get(),
            'services' => Service::query()->where('is_active', true)->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request, CreateLaundryOrderAction $createLaundryOrder): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id', 'required_without:customer_name'],
            'customer_name' => ['nullable', 'string', 'max:255', 'required_without:customer_id'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'weight_kg' => ['nullable', 'numeric', 'gt:0'],
            'weight' => ['nullable', 'numeric', 'gt:0'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'distinct', 'exists:services,id'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
        ]);

        $data['weight_kg'] = $data['weight_kg'] ?? $data['weight'] ?? null;
        abort_if($data['weight_kg'] === null, 422, 'Weight is required.');
        $order = $createLaundryOrder->handle($data, (int) $request->user()->id);

        return back()->with('status', "Order {$order->order_number} was created.");
    }
}
