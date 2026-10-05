<?php

namespace App\Http\Controllers;

use App\Actions\ApplyInventoryConsumptionAction;
use App\Actions\CreateLaundryOrderAction;
use App\Actions\UpdateLaundryOrderAction;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'date' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
            'payment_status' => ['nullable', 'string'],
        ]);
        $user = $request->user();
        $search = $request->string('search')->trim()->toString();
        $status = strtolower($request->string('status')->trim()->toString());
        $paymentStatus = strtolower($request->string('payment_status')->trim()->toString());
        $date = $request->string('date')->trim()->toString();

        $statusAliases = [
            'pending' => 'received',
            'in-progress' => 'washing',
            'ready' => 'ready_for_pickup',
            'delivered' => 'claimed',
        ];
        $canonicalStatus = $statusAliases[$status] ?? $status;

        $baseQuery = Order::query()
            ->when($user->role === 'staff', fn ($query) => $query->where('created_by', $user->id));

        $stats = [
            'total_active' => (clone $baseQuery)->whereNotIn('status', ['claimed', 'delivered', 'cancelled'])->count(),
            'awaiting_wash' => (clone $baseQuery)->whereIn('status', ['received', 'pending'])->count(),
            'washing' => (clone $baseQuery)->whereIn('status', ['washing', 'in-progress'])->count(),
            'drying' => (clone $baseQuery)->where('status', 'drying')->count(),
            'ironing' => (clone $baseQuery)->where('status', 'ironing')->count(),
            'folding' => (clone $baseQuery)->where('status', 'folding')->count(),
            'ready' => (clone $baseQuery)->whereIn('status', ['ready_for_pickup', 'ready'])->count(),
            'claimed' => (clone $baseQuery)->whereIn('status', ['claimed', 'delivered'])->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            'unpaid_count' => (clone $baseQuery)->whereIn('payment_status', ['unpaid', 'partially_paid'])->count(),
            'unpaid_amount' => (clone $baseQuery)->whereIn('payment_status', ['unpaid', 'partially_paid'])
                ->selectRaw('COALESCE(SUM(total_price - amount_paid), 0) as balance')
                ->value('balance') ?? 0,
            'total_loads_active' => (int) ((clone $baseQuery)->whereNotIn('status', ['claimed', 'delivered', 'cancelled'])->sum('number_of_loads')),
            'total_all' => (clone $baseQuery)->count(),
        ];

        $perPage = (int) $request->input('per_page', 8);
        if (!in_array($perPage, [5, 8, 10, 15, 25], true)) {
            $perPage = 8;
        }

        $orders = (clone $baseQuery)
            ->with(['customer', 'creator', 'orderServices.service', 'itemDetails', 'payments'])
            ->when($canonicalStatus !== '' && $canonicalStatus !== 'all', function ($query) use ($canonicalStatus): void {
                $statusList = match ($canonicalStatus) {
                    'received' => ['received', 'pending'],
                    'washing' => ['washing', 'in-progress'],
                    'ready_for_pickup' => ['ready_for_pickup', 'ready'],
                    'claimed' => ['claimed', 'delivered'],
                    default => [$canonicalStatus],
                };
                $query->whereIn('status', $statusList);
            })
            ->when($paymentStatus !== '' && $paymentStatus !== 'all', fn ($query) => $query->where('payment_status', $paymentStatus))
            ->when($date !== '', fn ($query) => $query->whereDate('created_at', $date))
            ->when($date === '' && isset($filters['from']), fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when($date === '' && isset($filters['to']), fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search): void {
                        $cq->where('phone', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%");
                    });
            }))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        if ($orders->currentPage() > $orders->lastPage() && $orders->total() > 0) {
            return redirect()->to($request->fullUrlWithQuery(['page' => $orders->lastPage()]));
        }

        return view('orders.index', [
            'orders' => $orders,
            'status' => $status,
            'canonicalStatus' => $canonicalStatus,
            'paymentStatus' => $paymentStatus,
            'search' => $search,
            'date' => $date,
            'perPage' => $perPage,
            'from' => $filters['from'] ?? null,
            'to' => $filters['to'] ?? null,
            'stats' => $stats,
            'customers' => Customer::query()->where('is_active', true)->orderBy('name')->get(),
            'services' => Service::query()->where('is_active', true)->orderBy('id')->get(),
            'inventoryItems' => InventoryItem::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('orders.index', ['open_intake' => 1]);
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
            'inventory_items' => ['nullable', 'array'],
            'inventory_items.*' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'tendered_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,gcash,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ]);
        $data['item_details'] = $this->validatedItemDetails($data['item_details'] ?? []);
        $data['weight_kg'] = $data['weight_kg'] ?? $data['weight'] ?? null;
        abort_if($data['weight_kg'] === null, 422, 'Weight is required.');
        $order = $createLaundryOrder->handle($data, (int) $request->user()->id, $inventoryConsumption);

        if ($request->input('redirect_to') === 'orders.index') {
            return redirect()->route('orders.index')->with('status', "Order {$order->order_number} was created successfully.");
        }

        if ($request->input('redirect_to') === 'orders.show') {
            return redirect()->route('orders.show', $order)->with('status', "Order {$order->order_number} was created successfully.");
        }

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
