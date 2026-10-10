<?php

namespace App\Http\Controllers;

use App\Actions\ApplyInventoryConsumptionAction;
use App\Actions\CreateLaundryOrderAction;
use App\Mail\CustomerVerificationCodeMail;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    public function showRegister(): View
    {
        if (Auth::check() && Auth::user()->role === 'customer') {
            return redirect()->route('customer.portal');
        }

        return view('customer.register');
    }

    public function processRegister(Request $request): RedirectResponse
    {
        if ($request->filled('phone') && ! $request->filled('contact_number')) {
            $request->merge(['contact_number' => $request->input('phone')]);
        }
        if ($request->filled('contact_number') && ! $request->filled('phone')) {
            $request->merge(['phone' => $request->input('contact_number')]);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'contact_number' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

        // Find or create customer dossier
        $customer = Customer::query()->where('email', $validated['email'])->first();
        if ($customer) {
            $customer->update([
                'name' => $fullName,
                'contact_number' => $validated['contact_number'],
                'phone' => $validated['contact_number'],
                'address' => $validated['address'],
                'is_active' => true,
            ]);
        } else {
            $customer = Customer::query()->create([
                'name' => $fullName,
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'],
                'phone' => $validated['contact_number'],
                'address' => $validated['address'],
                'is_active' => true,
            ]);
        }

        // Generate 6-digit verification code
        $verificationCode = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Generate a friendly username based on email
        $baseUsername = Str::slug(explode('@', $validated['email'])[0]);
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = User::query()->create([
            'name' => $fullName,
            'username' => $username,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'customer_id' => $customer->id,
            'phone' => $validated['contact_number'],
            'address' => $validated['address'],
            'verification_code' => $verificationCode,
            'email_verified_at' => null,
        ]);

        // Send 6-digit code via email
        try {
            Mail::to($user->email)->send(new CustomerVerificationCodeMail($user, $verificationCode));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Verification email failed for {$user->email}: " . $e->getMessage());
        }

        session(['pending_verification_user_id' => $user->id]);

        return redirect()->route('customer.verify')->with([
            'status' => "Account registered! A 6-digit verification code was sent to {$user->email}.",
            'dev_code' => $verificationCode, // Convenient display for dev environment
        ]);
    }

    public function showVerify(): View|RedirectResponse
    {
        $userId = session('pending_verification_user_id') ?? Auth::id();
        $user = $userId ? User::find($userId) : null;

        if ($user && $user->email_verified_at) {
            return redirect()->route('customer.portal');
        }

        return view('customer.verify', [
            'user' => $user,
        ]);
    }

    public function processVerify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $userId = session('pending_verification_user_id') ?? Auth::id();
        $user = $userId ? User::find($userId) : null;

        if (! $user && $request->filled('email')) {
            $user = User::where('email', $request->input('email'))->first();
        }

        if (! $user) {
            return redirect()->route('customer.login')->withErrors([
                'code' => 'Session expired. Please sign in to verify your account.',
            ]);
        }

        $inputCode = trim($request->input('code'));
        if ($user->verification_code !== $inputCode) {
            return back()->withErrors([
                'code' => 'The verification code you entered is invalid. Please double-check the 6-digit code.',
            ]);
        }

        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->save();

        session()->forget('pending_verification_user_id');
        Auth::login($user);

        return redirect()->route('customer.portal')->with(
            'status',
            "Account verified successfully! Welcome to your Trowa Laundry Portal, {$user->name}."
        );
    }

    public function resendCode(Request $request): RedirectResponse
    {
        $userId = session('pending_verification_user_id') ?? Auth::id();
        $user = $userId ? User::find($userId) : null;

        if (! $user && $request->filled('email')) {
            $user = User::where('email', $request->input('email'))->first();
        }

        if (! $user) {
            return redirect()->route('customer.login')->withErrors(['email' => 'Session expired.']);
        }

        $newCode = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $user->verification_code = $newCode;
        $user->save();

        try {
            Mail::to($user->email)->send(new CustomerVerificationCodeMail($user, $newCode));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Resend verification email failed for {$user->email}: " . $e->getMessage());
        }

        return back()->with([
            'status' => "A new verification code has been dispatched to {$user->email}.",
            'dev_code' => $newCode,
        ]);
    }

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->role === 'customer') {
            return redirect()->route('customer.portal');
        }

        return view('customer.login');
    }

    public function processLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = $credentials['login'];
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::query()->where($fieldType, $loginInput)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'login' => 'Invalid email/username or password.',
            ])->withInput($request->only('login'));
        }

        if ($user->role !== 'customer') {
            return back()->withErrors([
                'login' => 'This account is a Staff/Admin account. Please use the Staff Counter Sign-in.',
            ])->withInput($request->only('login'));
        }

        if (! $user->email_verified_at) {
            session(['pending_verification_user_id' => $user->id]);

            return redirect()->route('customer.verify')->with([
                'status' => 'Please enter the 6-digit verification code to complete your login.',
                'dev_code' => $user->verification_code,
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('customer.portal')->with(
            'status',
            "Welcome back, {$user->name}!"
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login')->with('status', 'You have been signed out.');
    }

    public function portal(Request $request): View
    {
        $user = $request->user();
        $customer = $user->customer ?? Customer::where('user_id', $user->id)->first() ?? Customer::where('email', $user->email)->first();

        // Active orders: pending_confirmation or actively processing
        $activeOrders = Order::query()
            ->where(function ($q) use ($customer, $user): void {
                if ($customer) {
                    $q->where('customer_id', $customer->id);
                }
                $q->orWhere('created_by', $user->id);
            })
            ->whereNotIn('status', ['claimed', 'cancelled'])
            ->with(['orderServices.service', 'itemDetails', 'statusHistories'])
            ->latest()
            ->get();

        // Past history
        $pastOrders = Order::query()
            ->where(function ($q) use ($customer, $user): void {
                if ($customer) {
                    $q->where('customer_id', $customer->id);
                }
                $q->orWhere('created_by', $user->id);
            })
            ->whereIn('status', ['claimed', 'cancelled'])
            ->with(['orderServices.service', 'itemDetails', 'statusHistories'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Check for unrated completed orders to trigger the notification modal popup
        $unratedOrder = Order::query()
            ->where(function ($q) use ($customer, $user): void {
                if ($customer) {
                    $q->where('customer_id', $customer->id);
                }
                $q->orWhere('created_by', $user->id);
            })
            ->whereIn('status', ['ready_for_pickup', 'claimed'])
            ->whereNull('rating')
            ->latest()
            ->first();

        $services = Service::query()->where('is_active', true)->orderBy('name')->get();
        $inventoryItems = InventoryItem::query()
            ->where('is_active', true)
            ->where('quantity_on_hand', '>', 0)
            ->orderBy('name')
            ->get();

        // Customer Expense Analytics (Filterable by timeframe: all, month, week, year)
        $expenseFilter = $request->query('expense_filter', 'month');
        $allCustomerOrders = Order::query()
            ->where(function ($q) use ($customer, $user): void {
                if ($customer) {
                    $q->where('customer_id', $customer->id);
                }
                $q->orWhere('created_by', $user->id);
            })
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $filteredCustomerOrders = Order::query()
            ->where(function ($q) use ($customer, $user): void {
                if ($customer) {
                    $q->where('customer_id', $customer->id);
                }
                $q->orWhere('created_by', $user->id);
            })
            ->whereNotIn('status', ['cancelled'])
            ->when($expenseFilter === 'week', fn ($q) => $q->where('created_at', '>=', now()->startOfWeek()))
            ->when($expenseFilter === 'month', fn ($q) => $q->where('created_at', '>=', now()->startOfMonth()))
            ->when($expenseFilter === 'year', fn ($q) => $q->where('created_at', '>=', now()->startOfYear()))
            ->get();

        $customerAnalytics = [
            'filter' => $expenseFilter,
            'total_spent_filtered' => (float) $filteredCustomerOrders->sum('total_price'),
            'total_kg_filtered' => (float) $filteredCustomerOrders->sum('weight_kg'),
            'total_orders_filtered' => $filteredCustomerOrders->count(),
            'all_time_spent' => (float) $allCustomerOrders->sum('total_price'),
            'all_time_kg' => (float) $allCustomerOrders->sum('weight_kg'),
            'all_time_orders' => $allCustomerOrders->count(),
            'avg_order_value' => $allCustomerOrders->count() > 0 ? round($allCustomerOrders->sum('total_price') / $allCustomerOrders->count(), 2) : 0,
        ];

        // Mascot Jokes and Friendly Messages pool
        $mascotJokes = [
            [
                'mood' => 'cheerful',
                'speech' => "Hey there, {$user->name}! Trowa-Bot here! Did you know: Clean clothes make you 99.4% more charming? Science hasn't proven it, but we believe it!",
                'badge' => 'Laundry Tip',
            ],
            [
                'mood' => 'joke',
                'speech' => "Why did the sock cross the road? To escape the dryer vortex of mystery! Don't worry, here at Trowa we count and track every single pair!",
                'badge' => 'Daily Chuckle',
            ],
            [
                'mood' => 'wash_wisdom',
                'speech' => "Fun fact! Our 8 commercial washing machines run at high-efficiency RPMs to gently preserve fabric fibers while blasting away tropical humidity!",
                'badge' => 'Did You Know?',
            ],
            [
                'mood' => 'clean_vibes',
                'speech' => "Ready for a fresh batch? Drop your garments at counter, pick your preferred Ariel or Surf sachets, and let us handle the rest!",
                'badge' => 'Fresh Vibes',
            ],
            [
                'mood' => 'vip',
                'speech' => "You have processed {$customerAnalytics['all_time_kg']} kg of laundry with us so far! You're an official clean-freak VIP in our books!",
                'badge' => 'Patron Milestone',
            ],
        ];

        $currentTab = $request->query('tab', 'dashboard');

        return view('customer.portal', [
            'user' => $user,
            'customer' => $customer,
            'activeOrders' => $activeOrders,
            'pastOrders' => $pastOrders,
            'unratedOrder' => $unratedOrder,
            'services' => $services,
            'inventoryItems' => $inventoryItems,
            'customerAnalytics' => $customerAnalytics,
            'mascotJokes' => $mascotJokes,
            'currentTab' => $currentTab,
        ]);
    }

    public function submitIntake(
        Request $request,
        CreateLaundryOrderAction $createLaundryOrder,
        ApplyInventoryConsumptionAction $inventoryConsumption
    ): RedirectResponse {
        $user = $request->user();
        $customer = $user->customer ?? Customer::where('user_id', $user->id)->first() ?? Customer::where('email', $user->email)->first();

        // 1. Sanitize item_details before validation: drop zero-quantity or blank items
        if ($request->has('item_details') && is_array($request->input('item_details'))) {
            $cleanedItems = [];
            foreach ($request->input('item_details') as $item) {
                $qty = (int) ($item['quantity'] ?? 0);
                $name = trim((string) ($item['item_name'] ?? ''));
                if ($name !== '' && $qty > 0) {
                    $cleanedItems[] = [
                        'item_name' => $name,
                        'quantity' => $qty,
                    ];
                }
            }
            $request->merge(['item_details' => $cleanedItems]);
        }

        // 2. Resolve soap preference: Shop stock vs customer personal supplies
        $soapPreference = $request->input('soap_preference');
        $bringOwnSoap = $request->boolean('bring_own_soap') || $soapPreference === 'own';
        if ($bringOwnSoap) {
            $customNote = trim((string) $request->input('own_soap_custom', ''));
            $soapPreference = $customNote !== ''
                ? "Customer Personal Supplies: {$customNote}"
                : "Customer Brought Own Soap & Fabric Softener";
        } elseif (is_numeric($soapPreference)) {
            $invItem = InventoryItem::find((int) $soapPreference);
            $soapPreference = $invItem ? "Shop Stock: {$invItem->name}" : "Shop Standard Detergent Formulation";
        } elseif (empty($soapPreference)) {
            $soapPreference = "Standard Laundromat Wash Formulation";
        }

        $request->merge(['soap_preference' => $soapPreference]);

        $validated = $request->validate([
            'weight_kg' => ['required', 'numeric', 'gt:0', 'max:64'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'exists:services,id'],
            'soap_preference' => ['required', 'string', 'max:255'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            'item_details' => ['nullable', 'array'],
            'item_details.*.item_name' => ['required', 'string', 'max:100'],
            'item_details.*.quantity' => ['required', 'integer', 'min:1'],
            'inventory_items' => ['nullable', 'array'],
        ], [
            'weight_kg.max' => 'Fleet capacity exceeded: Our laundromat operates 8 washing machines (maximum 64.0 kg). Please reduce the weight.',
        ]);

        $orderData = [
            'customer_id' => $customer?->id,
            'customer_name' => $customer?->name ?? $user->name,
            'address' => $customer?->address ?? $user->address,
            'contact_number' => $customer?->contact_number ?? $user->phone,
            'weight_kg' => $validated['weight_kg'],
            'service_ids' => $validated['service_ids'],
            'status' => 'pending_confirmation',
            'soap_preference' => $validated['soap_preference'],
            'customer_notes' => $validated['customer_notes'] ?? null,
            'item_details' => $validated['item_details'] ?? [],
            'inventory_items' => $request->input('inventory_items', []),
            'amount_paid' => 0,
            'tendered_amount' => 0,
        ];

        $order = $createLaundryOrder->handle($orderData, (int) $user->id, $inventoryConsumption);

        return redirect()->route('customer.portal')->with(
            'status',
            "Laundry Request #{$order->order_number} submitted! Staff will verify washing machine availability and confirm your order shortly."
        );
    }

    public function submitRating(Order $order, Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless(
            $order->customer_id === $user->customer_id || $order->created_by === $user->id,
            403
        );

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update([
            'rating' => $validated['rating'],
            'rating_comment' => $validated['rating_comment'] ?? null,
            'rated_at' => now(),
        ]);

        return redirect()->route('customer.portal')->with('status', '⭐ Thank you for rating our service! Your feedback helps us keep Trowa Laundry spotless.');
    }
}
