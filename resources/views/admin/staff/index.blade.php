@extends('layouts_app')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Top Management Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>Store Team Administration</span>
            </div>
            <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
                Staff &amp; Operator Accounts
            </h1>
            <p class="text-xs text-[#25799B] font-medium mt-0.5">
                Manage counter staff, assign shift privileges, and control system roles.
            </p>
        </div>

        <div>
            <button type="button" onclick="openStaffModal('create')" class="retro-btn-primary text-xs flex items-center gap-2 cursor-pointer">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Add New Staff Member</span>
            </button>
        </div>
    </div>

    <!-- Quick Status Pills -->
    <div class="flex flex-wrap items-center gap-3">
        <span class="badge border-[#182830] bg-[#FFFDF8] px-3.5 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830]">
            {{ $staffUsers->total() }} Total Registered Accounts
        </span>
        <span class="badge border-[#182830] bg-[#A2C5D8] px-3.5 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830]">
            {{ $totalStaff }} Counter Staff
        </span>
        <span class="badge border-[#182830] bg-[#CB1B03] px-3.5 py-1.5 font-mono text-xs font-bold text-white shadow-[1px_1px_0px_#182830]">
            {{ $totalAdmin }} Administrators
        </span>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('admin.staff.index') }}" class="retro-panel p-3.5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-1 max-w-md">
            <label for="staff-search-input" class="sr-only">Search staff</label>
            <input type="text" 
                   id="staff-search-input" 
                   name="search" 
                   value="{{ $search }}" 
                   placeholder="Search by name, email, username or contact number..." 
                   class="field text-xs flex-1">
            <button type="submit" class="retro-btn-primary text-xs px-3 py-2">Search</button>
            @if($search)
                <a href="{{ route('admin.staff.index') }}" class="retro-btn-secondary text-xs px-2.5 py-2">Clear</a>
            @endif
        </div>
        <span class="font-mono text-xs text-slate-500">Live Staff Directory</span>
    </form>

    <!-- Staff Directory Table -->
    <div class="retro-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-mono text-xs">
                <thead class="border-b-2 border-[#182830] bg-[#A2C5D8]/40 uppercase text-slate-800">
                    <tr>
                        <th class="p-3.5">Name &amp; Username</th>
                        <th class="p-3.5">Role</th>
                        <th class="p-3.5">Email &amp; Phone</th>
                        <th class="p-3.5">Orders Processed</th>
                        <th class="p-3.5">Payments Handled</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#182830]/15 bg-[#FFFDF8]">
                    @forelse($staffUsers as $member)
                        <tr class="hover:bg-amber-50/50 transition">
                            <td class="p-3.5">
                                <strong class="font-sans text-sm font-bold text-[#182830] block">{{ $member->name }}</strong>
                                <span class="text-[11px] text-slate-500 font-mono">@<span>{{ $member->username ?? 'none' }}</span></span>
                            </td>
                            <td class="p-3.5">
                                @if($member->role === 'admin')
                                    <span class="badge border-[#182830] bg-[#CB1B03] text-white text-[10px] font-bold uppercase shadow-[1px_1px_0px_#182830]">
                                        Administrator
                                    </span>
                                @else
                                    <span class="badge border-[#182830] bg-[#A2C5D8] text-[#182830] text-[10px] font-bold uppercase shadow-[1px_1px_0px_#182830]">
                                        Counter Staff
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="block text-slate-800">{{ $member->email }}</span>
                                <span class="block text-[11px] text-slate-500">{{ $member->phone ?? 'No phone added' }}</span>
                            </td>
                            <td class="p-3.5 font-bold text-[#182830]">
                                {{ $member->orders_count }} orders
                            </td>
                            <td class="p-3.5 font-bold text-[#25799B]">
                                {{ $member->received_payments_count }} transactions
                            </td>
                            <td class="p-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            onclick="openStaffModal('edit', {{ json_encode([
                                                'id' => $member->id,
                                                'name' => $member->name,
                                                'username' => $member->username,
                                                'email' => $member->email,
                                                'phone' => $member->phone,
                                                'role' => $member->role,
                                            ]) }})"
                                            class="rounded-lg border border-[#182830] bg-[#FFFDF8] px-2.5 py-1 text-[11px] font-bold text-[#25799B] shadow-[1px_1px_0px_#182830] hover:bg-[#F7E6CB] transition cursor-pointer">
                                        Edit
                                    </button>

                                    @if($member->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.staff.destroy', $member) }}" data-confirm="Are you sure you want to permanently delete staff account '{{ $member->name }}'? They will immediately lose access to the system." data-confirm-title="Delete Staff Member" data-confirm-type="danger" data-confirm-btn="Yes, Delete Staff" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-[#CB1B03] bg-red-50 px-2 py-1 text-[11px] font-bold text-[#CB1B03] shadow-[1px_1px_0px_#182830] hover:bg-red-100 transition cursor-pointer">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 font-mono text-xs">
                                No staff members found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffUsers->hasPages())
            <div class="border-t-2 border-[#182830] bg-[#F7E6CB]/40 p-3.5">
                {{ $staffUsers->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Staff Create / Edit Modal -->
<div id="staff-modal" class="fixed inset-0 z-50 hidden bg-[#182830]/75 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-md rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[6px_6px_0px_#182830]">
        <button type="button" onclick="closeStaffModal()" class="absolute top-4 right-4 text-2xl font-black text-[#182830] hover:text-[#CB1B03] cursor-pointer">×</button>

        <h3 id="staff-modal-title" class="font-recoleta text-2xl font-black text-[#182830]">Add New Staff Member</h3>
        <p class="font-mono text-xs text-[#25799B] mt-0.5">Configure authentication credentials &amp; permissions.</p>

        <form method="POST" action="{{ route('admin.staff.store') }}" id="staff-form" class="mt-5 space-y-4 font-mono text-xs">
            @csrf
            <div id="staff-method-slot"></div>

            <div>
                <label for="staff-name-input" class="block font-bold text-[#182830] uppercase mb-1">Full Name *</label>
                <input type="text" name="name" id="staff-name-input" required class="field text-xs font-sans" placeholder="e.g. Maria Santos">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="staff-username-input" class="block font-bold text-[#182830] uppercase mb-1">Username *</label>
                    <input type="text" name="username" id="staff-username-input" required class="field text-xs" placeholder="e.g. msantos">
                </div>
                <div>
                    <label for="staff-role-input" class="block font-bold text-[#182830] uppercase mb-1">Role *</label>
                    <select name="role" id="staff-role-input" required class="field text-xs">
                        <option value="staff">Counter Staff</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="staff-email-input" class="block font-bold text-[#182830] uppercase mb-1">Email Address *</label>
                <input type="email" name="email" id="staff-email-input" required class="field text-xs" placeholder="e.g. maria@trowalaundry.com">
            </div>

            <div>
                <label for="staff-phone-input" class="block font-bold text-[#182830] uppercase mb-1">Phone Number (Optional)</label>
                <input type="text" name="phone" id="staff-phone-input" class="field text-xs" placeholder="0917-000-0000">
            </div>

            <div>
                <label for="staff-password-input" class="block font-bold text-[#182830] uppercase mb-1" id="staff-password-label">Password *</label>
                <input type="password" name="password" id="staff-password-input" class="field text-xs" placeholder="Min. 8 characters">
                <span id="staff-password-hint" class="hidden text-[10px] text-slate-500 mt-1 block">Leave blank to keep existing password unchanged.</span>
            </div>

            <div class="pt-3 border-t-2 border-[#182830]/15 flex items-center justify-end gap-2">
                <button type="button" onclick="closeStaffModal()" class="retro-btn-secondary text-xs px-3 py-2 cursor-pointer">
                    Cancel
                </button>
                <button type="submit" id="staff-submit-btn" class="retro-btn-primary text-xs px-4 py-2 cursor-pointer">
                    Save Staff Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openStaffModal(mode, data = null) {
        const modal = document.getElementById('staff-modal');
        const form = document.getElementById('staff-form');
        const title = document.getElementById('staff-modal-title');
        const methodSlot = document.getElementById('staff-method-slot');
        const pwdInput = document.getElementById('staff-password-input');
        const pwdLabel = document.getElementById('staff-password-label');
        const pwdHint = document.getElementById('staff-password-hint');

        if (mode === 'create') {
            title.textContent = 'Add New Staff Member';
            form.action = "{{ route('admin.staff.store') }}";
            methodSlot.innerHTML = '';
            form.reset();
            pwdInput.required = true;
            pwdLabel.textContent = 'Password *';
            pwdHint.classList.add('hidden');
        } else if (mode === 'edit' && data) {
            title.textContent = 'Edit Staff Member';
            form.action = `/admin/staff/${data.id}`;
            methodSlot.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('staff-name-input').value = data.name || '';
            document.getElementById('staff-username-input').value = data.username || '';
            document.getElementById('staff-email-input').value = data.email || '';
            document.getElementById('staff-phone-input').value = data.phone || '';
            document.getElementById('staff-role-input').value = data.role || 'staff';
            pwdInput.required = false;
            pwdInput.value = '';
            pwdLabel.textContent = 'Change Password (Optional)';
            pwdHint.classList.remove('hidden');
        }

        modal.classList.remove('hidden');
    }

    function closeStaffModal() {
        document.getElementById('staff-modal').classList.add('hidden');
    }
</script>
@endsection
