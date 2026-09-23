<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
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
            ->when(in_array($status, ['pending', 'in-progress', 'ready', 'delivered', 'cancelled'], true), fn ($query) => $query->where('status', $status))
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
            'services' => Service::query()->orderBy('id')->get(),
        ]);
    }
}
