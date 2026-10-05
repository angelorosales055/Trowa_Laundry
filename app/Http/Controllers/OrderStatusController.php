<?php

namespace App\Http\Controllers;

use App\Actions\RecordPaymentAction;
use App\Actions\SyncOrderPaymentSummaryAction;
use App\Actions\UpdateOrderStatusAction;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderStatusController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'date' => ['nullable', 'date'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $user = $request->user();
        $status = strtolower($request->string('status')->toString());
        $statusAliases = [
            'pending' => 'received',
            'in-progress' => 'washing',
            'ready' => 'ready_for_pickup',
            'delivered' => 'claimed',
        ];
        $status = $statusAliases[$status] ?? $status;
        $search = $request->string('search')->trim()->toString();
        $orders = Order::query()
            ->with(['customer:id,name,phone,contact_number', 'orderServices.service'])
            ->when($user->role === 'staff', fn ($query) => $query->where('created_by', $user->id))
            ->when($status !== '', function ($query) use ($status): void {
                $legacyStatuses = match ($status) {
                    'received' => ['received', 'pending'],
                    'washing' => ['washing', 'in-progress'],
                    'ready_for_pickup' => ['ready_for_pickup', 'ready'],
                    'claimed' => ['claimed', 'delivered'],
                    default => [$status],
                };
                $query->whereIn(DB::raw('LOWER(status)'), $legacyStatuses);
            })
            ->when(isset($filters['date']), fn ($query) => $query->whereDate('created_at', $filters['date']))
            ->when(! isset($filters['date']) && isset($filters['from']), fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when(! isset($filters['date']) && isset($filters['to']), fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            }))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        $activeWashingOrders = Order::query()
            ->with(['customer', 'orderServices.service'])
            ->whereIn(DB::raw('LOWER(status)'), ['washing', 'in-progress'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        $activeDryingOrders = Order::query()
            ->with(['customer', 'orderServices.service'])
            ->where(DB::raw('LOWER(status)'), 'drying')
            ->latest('updated_at')
            ->take(8)
            ->get();

        return view('schedule.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'module' => 'status',
            'customers' => collect(),
            'services' => collect(),
            'activeWashingOrders' => $activeWashingOrders,
            'activeDryingOrders' => $activeDryingOrders,
        ]);
    }

    public function update(
        Request $request,
        Order $order,
        UpdateOrderStatusAction $updateOrderStatus,
        RecordPaymentAction $recordPayment,
        SyncOrderPaymentSummaryAction $syncSummary
    ): RedirectResponse {
        $data = $request->validate([
            'status' => ['required', 'in:received,washing,drying,ironing,folding,ready_for_pickup,claimed,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,gcash,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        abort_unless($user->role === 'admin' || $order->created_by === $user->id, 403);

        // Claimed status payment verification and processing
        if ($data['status'] === 'claimed') {
            $totalPrice = (float) $order->total_price;
            $amountPaid = (float) $order->amount_paid;
            $balanceDue = max(0, $totalPrice - $amountPaid);

            // If an unpaid balance exists and payment details were supplied in the claim window:
            if ($balanceDue > 0 && $request->filled('amount') && (float) $request->input('amount') > 0) {
                $payAmount = (float) $request->input('amount');
                $recordPayment->handle($order, [
                    'amount' => $payAmount,
                    'payment_method' => $request->input('payment_method', 'cash'),
                    'reference_number' => $request->input('reference_number'),
                    'notes' => $request->input('notes') ?? 'Settled in full upon customer claim release.',
                ], (int) $user->id, $syncSummary);

                $order->refresh();
            }

            // Once the payment is done that will be the only time that it will be able to be marked as claimed
            $remaining = max(0, (float) $order->total_price - (float) $order->amount_paid);
            if ($remaining > 0) {
                return back()->withErrors([
                    'status' => "Order {$order->order_number} cannot be marked as claimed until payment is fully settled. Remaining balance: ₱" . number_format($remaining, 2) . ".",
                    'payment' => "Order {$order->order_number} cannot be marked as claimed until payment is fully settled. Remaining balance: ₱" . number_format($remaining, 2) . ".",
                ]);
            }
        }

        $updateOrderStatus->handle($order, $data['status'], (int) $user->id, $data['notes'] ?? null);

        $flashMessage = $data['status'] === 'claimed'
            ? "Order {$order->order_number} marked as Done and released to customer."
            : "Order {$order->order_number} moved to " . ucfirst(str_replace('_', ' ', $data['status'])) . '.';

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo === 'schedule.index') {
            return redirect()
                ->route('schedule.index', ['status' => $data['status']])
                ->with('status', $flashMessage);
        }
        if ($redirectTo === 'dashboard') {
            return redirect()
                ->route('dashboard')
                ->with('status', $flashMessage);
        }
        if ($redirectTo === 'orders.show') {
            return redirect()
                ->route('orders.show', $order)
                ->with('status', $flashMessage);
        }

        return redirect()
            ->route('orders.index', ['status' => $data['status']])
            ->with('status', $flashMessage);
    }
}
