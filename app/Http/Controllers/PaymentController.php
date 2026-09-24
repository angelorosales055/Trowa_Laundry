<?php

namespace App\Http\Controllers;

use App\Actions\RecordPaymentAction;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $orders = Order::query()
            ->with(['customer', 'payments.receivedBy'])
            ->when($user->role === 'staff', fn ($query) => $query->where('created_by', $user->id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('payments.index', compact('orders'));
    }

    public function store(Request $request, Order $order, RecordPaymentAction $recordPayment): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'in:cash,gcash,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['payment_method'] === 'gcash') {
            validator($data, ['reference_number' => ['required', 'string', 'max:255']])->validate();
        }

        $recordPayment->handle($order, $data, (int) $request->user()->id);

        return back()->with('status', 'Payment recorded.');
    }
}
