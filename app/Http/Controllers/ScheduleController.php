<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = $request->string('search')->trim()->toString();
        $orders = Order::query()
            ->with(['customer', 'creator'])
            ->when($status !== '', function ($query) use ($status): void {
                $legacyStatusMap = [
                    'received' => ['received', 'pending'],
                    'washing' => ['washing', 'in-progress'],
                    'ready_for_pickup' => ['ready_for_pickup', 'ready'],
                    'claimed' => ['claimed', 'delivered'],
                    'cancelled' => ['cancelled'],
                ];
                $query->whereIn('status', $legacyStatusMap[$status] ?? [$status]);
            })
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('schedule.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'customers' => Customer::query()->orderBy('name')->get(),
            'services' => Service::query()->where('is_active', true)->orderBy('id')->get(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load([
            'customer',
            'creator',
            'orderServices.service',
            'itemDetails',
            'statusHistories.changedBy',
            'payments.receivedBy',
        ]);

        return view('schedule.show', compact('order'));
    }
}
