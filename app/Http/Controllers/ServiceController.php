<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('services.index', ['services' => Service::query()->orderBy('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:services,name'],
            'icon' => ['required', 'string', 'max:10'],
            'pricing_type' => ['required', 'in:per_kg,per_load,flat_rate'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'price_per_load' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['price_per_load'] = $data['price_per_load'] ?? $data['price'] ?? 0;
        $data['price'] = $data['price'] ?? $data['price_per_load'];
        $data['is_active'] = $data['is_active'] ?? true;
        Service::create($data);

        return back()->with('status', 'Service added.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:services,name,'.$service->id],
            'icon' => ['required', 'string', 'max:10'],
            'pricing_type' => ['required', 'in:per_kg,per_load,flat_rate'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'price_per_load' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['price_per_load'] = $data['price_per_load'] ?? $data['price'] ?? 0;
        $data['price'] = $data['price'] ?? $data['price_per_load'];
        $service->update($data);

        return back()->with('status', 'Prices updated and applied to new orders.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return back()->with('status', 'Service removed.');
    }

    public function toggleActive(Service $service): RedirectResponse
    {
        $service->update(['is_active' => ! $service->is_active]);

        return back()->with('status', $service->is_active ? 'Service activated.' : 'Service deactivated.');
    }
}
