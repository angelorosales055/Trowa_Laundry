<?php

namespace App\Http\Controllers;

use App\Actions\RecordPaymentAction;
use App\Actions\RefundPaymentAction;
use App\Actions\SyncOrderPaymentSummaryAction;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'method' => ['nullable', 'in:cash,gcash,other'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $user = $request->user();
        $orders = Order::query()
            ->with(['customer', 'payments.receivedBy'])
            ->when($user->role === 'staff', fn ($query) => $query->where('created_by', $user->id))
            ->when(isset($filters['method']), fn ($query) => $query->whereHas('payments', fn ($paymentQuery) => $paymentQuery->where('payment_method', $filters['method'])))
            ->when(isset($filters['from']), fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when(isset($filters['to']), fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('payments.index', [
            'orders' => $orders,
            'method' => $filters['method'] ?? null,
            'from' => $filters['from'] ?? null,
            'to' => $filters['to'] ?? null,
        ]);
    }

    public function store(Request $request, Order $order, RecordPaymentAction $recordPayment, SyncOrderPaymentSummaryAction $syncSummary): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin' || $order->created_by === $request->user()->id, 403);
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'in:cash,gcash,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $recordPayment->handle($order, $data, (int) $request->user()->id, $syncSummary);

        return back()->with('status', 'Payment recorded.');
    }

    public function refund(Request $request, Order $order, Payment $payment, RefundPaymentAction $refundPayment, SyncOrderPaymentSummaryAction $syncSummary): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        $refundPayment->handle($order, $payment, $data, (int) $request->user()->id, $syncSummary);

        return back()->with('status', 'Refund recorded.');
    }
}
