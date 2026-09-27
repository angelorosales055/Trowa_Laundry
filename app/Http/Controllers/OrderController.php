<?php

namespace App\Http\Controllers;

use App\Actions\ApplyInventoryConsumptionAction;
use App\Actions\CreateLaundryOrderAction;
use App\Actions\UpdateLaundryOrderAction;
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
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $user = $request->user();
        $search = $request->string('search')->trim()->toString();
        $orders = Order::query()
            ->with(['customer', 'creator'])
            ->when($user->role === 'staff', fn ($query) => $query->where('created_by', $user->id))
            ->when(isset($filters['from']), fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when(isset($filters['to']), fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
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
            'customers' => Customer::query()->where('is_active', true)->orderBy('name')->get(),
            'services' => Service::query()->where('is_active', true)->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request, CreateLaundryOrderAction $createLaundryOrder, ApplyInventoryConsumptionAction $inventoryConsumption): RedirectResponse
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
            'item_details' => ['nullable', 'array'],
            'item_details.*.item_name' => ['nullable', 'string', 'max:100'],
            'item_details.*.quantity' => ['nullable', 'integer', 'min:1'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,gcash,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ]);
        $data['item_details'] = $this->validatedItemDetails($data['item_details'] ?? []);
        $data['weight_kg'] = $data['weight_kg'] ?? $data['weight'] ?? null;
        abort_if($data['weight_kg'] === null, 422, 'Weight is required.');
        $order = $createLaundryOrder->handle($data, (int) $request->user()->id, $inventoryConsumption);

        return back()->with('status', "Order {$order->order_number} was created.");
    }

    public function edit(Order $order): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $order->load(['customer', 'orderServices', 'itemDetails']);

        return view('schedule.edit', [
            'order' => $order,
            'customers' => Customer::query()->where('is_active', true)->orderBy('name')->get(),
            'services' => Service::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Order $order, UpdateLaundryOrderAction $updateOrder, ApplyInventoryConsumptionAction $inventoryConsumption): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'weight_kg' => ['required', 'numeric', 'gt:0'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'distinct', 'exists:services,id'],
            'item_details' => ['nullable', 'array'],
            'item_details.*.item_name' => ['nullable', 'string', 'max:100'],
            'item_details.*.quantity' => ['nullable', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);
        $data['item_details'] = $this->validatedItemDetails($data['item_details'] ?? []);
        $updateOrder->handle($order, $data, (int) $request->user()->id, $inventoryConsumption);

        return redirect()->route('orders.show', $order)->with('status', 'Order updated and the correction was recorded.');
    }

    /**
     * @param  array<int, array{item_name?: ?string, quantity?: ?int}>  $items
     * @return array<int, array{item_name: string, quantity: int}>
     */
    private function validatedItemDetails(array $items): array
    {
        $items = collect($items)
            ->filter(fn (array $item): bool => filled($item['item_name'] ?? null) || filled($item['quantity'] ?? null))
            ->values();

        foreach ($items as $item) {
            if (blank($item['item_name'] ?? null) || blank($item['quantity'] ?? null)) {
                abort(422, 'Each laundry item must have both a name and quantity.');
            }
        }

        return $items->map(fn (array $item): array => [
            'item_name' => $item['item_name'],
            'quantity' => (int) $item['quantity'],
        ])->all();
    }
}
