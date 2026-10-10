<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $staffUsers = User::query()
            ->whereIn('role', ['staff', 'admin'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount(['orders', 'receivedPayments', 'statusChanges'])
            ->latest()
            ->paginate(15);

        return view('admin.staff.index', [
            'staffUsers' => $staffUsers,
            'search' => $search,
            'totalStaff' => User::where('role', 'staff')->count(),
            'totalAdmin' => User::where('role', 'admin')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['staff', 'admin'])],
            'password' => ['required', 'string', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.staff.index')
            ->with('status', "Staff member {$user->name} successfully registered.");
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        // Prevent editing customer accounts from this controller
        if (! in_array($staff->role, ['staff', 'admin'], true)) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($staff->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['staff', 'admin'])],
            'password' => ['nullable', 'string', Password::min(8)],
        ]);

        // Prevent admin from removing their own admin role
        if ($staff->id === $request->user()->id && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot revoke your own administrator privileges.']);
        }

        $data = [
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')
            ->with('status', "Staff profile for {$staff->name} updated successfully.");
    }

    public function destroy(Request $request, User $staff): RedirectResponse
    {
        if (! in_array($staff->role, ['staff', 'admin'], true)) {
            abort(404);
        }

        if ($staff->id === $request->user()->id) {
            return back()->withErrors(['error' => 'You cannot delete your own account while logged in.']);
        }

        $name = $staff->name;
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('status', "Staff account for {$name} was removed.");
    }
}
