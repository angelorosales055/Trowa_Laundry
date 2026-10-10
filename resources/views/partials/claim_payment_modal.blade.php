<!-- Claim Release & Settlement Payment Window Modal -->
<div id="claim-payment-modal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-[#182830]/80 p-3 sm:p-4 backdrop-blur-xs overflow-y-auto">
    <div class="relative my-auto w-full max-w-lg rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] shadow-[8px_8px_0px_#182830] overflow-hidden">
        
        <!-- Top Title Bar -->
        <div class="flex items-center justify-between border-b-2 border-[#182830] bg-[#25799B] px-5 py-3.5 text-[#FFFDF8]">
            <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#CB1B03] shadow-[2px_2px_0px_#182830]">
                    <!-- Clean Handover Bag Vector SVG -->
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-recoleta text-xl font-black text-[#F7E6CB] leading-tight" id="claim-modal-title">Payment Settlement & Order Done</h3>
                    <p class="font-mono text-[10px] uppercase tracking-wider text-[#A2C5D8]" id="claim-modal-ticket-sub">Mark Order as Done</p>
                </div>
            </div>

            <button type="button" id="claim-modal-close-btn" class="flex h-8 w-8 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-lg font-black text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer" aria-label="Close">
                ×
            </button>
        </div>

        <!-- Form Body -->
        <form method="POST" id="claim-payment-form" class="p-5 space-y-4 bg-[#FFFDF8]">
            @csrf
            <input type="hidden" name="status" value="claimed">
            <input type="hidden" name="redirect_to" id="claim-redirect-to" value="orders.index">
            <input type="hidden" name="amount" id="claim-hidden-amount" value="0">

            <!-- Customer & Ledger Summary Card -->
            <div class="rounded-2xl border-2 border-[#182830] bg-[#F7E6CB]/50 p-4 shadow-[2px_2px_0px_#182830] space-y-2 font-mono text-xs">
                <div class="flex items-center justify-between border-b border-[#182830]/15 pb-1.5">
                    <span class="text-slate-600 font-bold uppercase text-[10px]">Client Account:</span>
                    <strong id="claim-customer-name" class="font-recoleta text-sm font-bold text-[#182830]">--</strong>
                </div>
                <div class="flex items-center justify-between py-0.5">
                    <span class="text-slate-600">Total Laundry Bill:</span>
                    <span id="claim-total-bill" class="font-bold text-[#182830]">₱0.00</span>
                </div>
                <div class="flex items-center justify-between py-0.5">
                    <span class="text-slate-600">Previously Paid:</span>
                    <span id="claim-amount-collected" class="font-bold text-emerald-700">₱0.00</span>
                </div>
                <div class="flex items-center justify-between border-t-2 border-dashed border-[#182830] pt-2">
                    <span class="font-bold text-[#182830] text-sm uppercase">Remaining Balance Due:</span>
                    <strong id="claim-balance-due-text" class="font-recoleta text-2xl font-black text-[#CB1B03]">₱0.00</strong>
                </div>
            </div>

            <!-- UNPAID BALANCE PAYMENT SECTION (Hidden if already paid) -->
            <div id="claim-payment-section" class="space-y-3">
                <div class="rounded-xl border border-[#25799B] bg-[#A2C5D8]/20 p-2.5 text-xs text-[#182830] font-medium flex items-center gap-2">
                    <svg class="h-4 w-4 text-[#25799B] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>Please collect payment now before releasing the clean laundry to the customer.</span>
                </div>

                <!-- Quick Tender Presets -->
                <div>
                    <span class="block font-mono text-[10px] font-bold uppercase text-slate-700 mb-1">Quick Cash Presets</span>
                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button" id="claim-btn-exact" class="rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer">
                            Exact Due
                        </button>
                        <button type="button" class="claim-preset-btn rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-amount="100">
                            ₱100
                        </button>
                        <button type="button" class="claim-preset-btn rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-amount="200">
                            ₱200
                        </button>
                        <button type="button" class="claim-preset-btn rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-amount="500">
                            ₱500
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-mono text-[11px] font-bold uppercase text-[#182830] mb-1">Cash Tendered (₱) <span class="text-[#CB1B03]">*</span></label>
                        <input type="number" step="0.01" min="0.01" id="claim-tender-input" placeholder="0.00" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2 font-mono text-lg font-black text-[#182830] shadow-[1px_1px_0px_#182830] focus:border-[#25799B] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-mono text-[11px] font-bold uppercase text-[#182830] mb-1">Payment Method</label>
                        <select name="payment_method" id="claim-method-select" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-2 py-2 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] focus:border-[#25799B] focus:outline-none">
                            <option value="cash">Cash Tender</option>
                            <option value="gcash">GCash E-Wallet</option>
                            <option value="other">Other / Card</option>
                        </select>
                    </div>
                </div>

                <!-- Reference Number (GCash) -->
                <div id="claim-ref-container" class="hidden">
                    <label class="block font-mono text-[10px] font-bold uppercase text-[#182830] mb-1">Transaction Ref #</label>
                    <input type="text" name="reference_number" id="claim-ref-input" placeholder="GCash Ref # or receipt #" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none">
                </div>

                <!-- Dynamic Change Due Card -->
                <div id="claim-change-card" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 text-center shadow-[1px_1px_0px_#182830]">
                    <span class="block font-mono text-[10px] font-bold uppercase text-slate-500">Change Due to Customer</span>
                    <strong id="claim-change-val" class="font-mono text-xl font-black text-emerald-700">₱0.00</strong>
                </div>
            </div>

            <!-- ALREADY PAID NOTIFICATION (Shown if balance == 0) -->
            <div id="claim-already-paid-section" class="hidden rounded-xl border-2 border-emerald-600 bg-emerald-50 p-4 text-center">
                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 mb-2">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h4 class="font-recoleta text-base font-bold text-emerald-950">Paid in Full</h4>
                <p class="font-mono text-xs text-emerald-800 mt-0.5">This laundry order has zero outstanding balance. You can immediately complete the order and release the clean garments.</p>
            </div>

            <!-- Optional Handover Note -->
            <div>
                <label class="block font-mono text-[10px] font-bold uppercase text-slate-600 mb-1">Handover Notes (Optional)</label>
                <input type="text" name="notes" placeholder="e.g. Picked up by customer in person, clean & complete" class="w-full rounded-xl border border-[#182830]/40 bg-[#FFFDF8] px-3 py-1.5 text-xs text-[#182830] focus:outline-none focus:border-[#25799B]">
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" id="claim-modal-cancel-btn" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2.5 font-recoleta text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-100 transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" id="claim-confirm-submit-btn" class="rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-5 py-2.5 font-recoleta text-sm font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer flex items-center gap-2">
                    <span id="claim-btn-label">Done ➔</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
(function() {
    const claimModal = document.getElementById('claim-payment-modal');
    const claimForm = document.getElementById('claim-payment-form');
    const ticketSub = document.getElementById('claim-modal-ticket-sub');
    const customerEl = document.getElementById('claim-customer-name');
    const totalBillEl = document.getElementById('claim-total-bill');
    const collectedEl = document.getElementById('claim-amount-collected');
    const balanceDueText = document.getElementById('claim-balance-due-text');
    const tenderInput = document.getElementById('claim-tender-input');
    const hiddenAmount = document.getElementById('claim-hidden-amount');
    const changeVal = document.getElementById('claim-change-val');
    const changeCard = document.getElementById('claim-change-card');
    const methodSelect = document.getElementById('claim-method-select');
    const refContainer = document.getElementById('claim-ref-container');
    const btnExact = document.getElementById('claim-btn-exact');
    const paymentSection = document.getElementById('claim-payment-section');
    const alreadyPaidSection = document.getElementById('claim-already-paid-section');
    const submitBtnLabel = document.getElementById('claim-btn-label');
    const submitBtn = document.getElementById('claim-confirm-submit-btn');
    const redirectInput = document.getElementById('claim-redirect-to');

    let currentBalanceDue = 0;

    function openClaimModal(orderData) {
        const orderId = orderData.orderId;
        const orderNumber = orderData.orderNumber;
        const customer = orderData.customer;
        const total = parseFloat(orderData.total) || 0;
        const paid = parseFloat(orderData.paid) || 0;
        const balance = Math.max(0, parseFloat(orderData.balance) || (total - paid));
        const redirectPage = orderData.redirectTo || 'orders.index';

        currentBalanceDue = balance;

        if (claimForm) claimForm.action = `/orders/${orderId}/status`;
        if (redirectInput) redirectInput.value = redirectPage;
        if (ticketSub) ticketSub.textContent = `Order ${orderNumber}`;
        if (customerEl) customerEl.textContent = customer;
        if (totalBillEl) totalBillEl.textContent = `₱${total.toFixed(2)}`;
        if (collectedEl) collectedEl.textContent = `₱${paid.toFixed(2)}`;
        if (balanceDueText) balanceDueText.textContent = `₱${balance.toFixed(2)}`;

        if (balance > 0) {
            // Unpaid balance: require tender payment
            paymentSection?.classList.remove('hidden');
            alreadyPaidSection?.classList.add('hidden');
            if (tenderInput) {
                tenderInput.value = balance.toFixed(2);
                tenderInput.required = true;
            }
            if (hiddenAmount) hiddenAmount.value = balance.toFixed(2);
            if (submitBtnLabel) submitBtnLabel.textContent = `Pay ₱${balance.toFixed(2)} & Done ➔`;
            if (submitBtn) {
                submitBtn.classList.remove('bg-emerald-700', 'hover:bg-emerald-800');
                submitBtn.classList.add('bg-[#CB1B03]', 'hover:bg-[#B51702]');
            }
            recalcClaimChange();
        } else {
            // Already paid: confirm claim release
            paymentSection?.classList.add('hidden');
            alreadyPaidSection?.classList.remove('hidden');
            if (tenderInput) {
                tenderInput.value = "0";
                tenderInput.required = false;
            }
            if (hiddenAmount) hiddenAmount.value = "0";
            if (submitBtnLabel) submitBtnLabel.textContent = "Done ➔";
            if (submitBtn) {
                submitBtn.classList.remove('bg-[#CB1B03]', 'hover:bg-[#B51702]');
                submitBtn.classList.add('bg-emerald-700', 'hover:bg-emerald-800');
            }
        }

        claimModal?.classList.replace('hidden', 'flex');
        if (balance > 0 && tenderInput) {
            tenderInput.focus();
            tenderInput.select();
        }
    }

    function recalcClaimChange() {
        const tender = parseFloat(tenderInput?.value) || 0;
        const amountToPay = Math.min(tender, currentBalanceDue);
        if (hiddenAmount) hiddenAmount.value = amountToPay.toFixed(2);

        const change = tender - currentBalanceDue;
        if (change >= 0) {
            if (changeVal) {
                changeVal.textContent = `₱${change.toFixed(2)}`;
                changeVal.className = 'font-mono text-xl font-black text-emerald-700';
            }
            if (changeCard) {
                changeCard.className = 'rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 text-center shadow-[1px_1px_0px_#182830]';
            }
        } else {
            const short = Math.abs(change);
            if (changeVal) {
                changeVal.textContent = `Short ₱${short.toFixed(2)} (Full payment required)`;
                changeVal.className = 'font-mono text-xs font-bold text-red-600';
            }
            if (changeCard) {
                changeCard.className = 'rounded-xl border-2 border-red-500 bg-red-50 p-3 text-center shadow-[1px_1px_0px_#182830]';
            }
        }
    }

    tenderInput?.addEventListener('input', recalcClaimChange);

    btnExact?.addEventListener('click', () => {
        if (tenderInput) {
            tenderInput.value = currentBalanceDue.toFixed(2);
            recalcClaimChange();
        }
    });

    document.querySelectorAll('.claim-preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (tenderInput) {
                tenderInput.value = parseFloat(this.dataset.amount).toFixed(2);
                recalcClaimChange();
            }
        });
    });

    methodSelect?.addEventListener('change', function() {
        if (this.value === 'gcash' || this.value === 'other') {
            refContainer?.classList.remove('hidden');
        } else {
            refContainer?.classList.add('hidden');
        }
    });

    const closeClaimModal = () => claimModal?.classList.replace('flex', 'hidden');
    document.getElementById('claim-modal-close-btn')?.addEventListener('click', closeClaimModal);
    document.getElementById('claim-modal-cancel-btn')?.addEventListener('click', closeClaimModal);

    // Global listener for buttons that trigger claim
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-claim-modal-btn');
        if (btn) {
            e.preventDefault();
            openClaimModal({
                orderId: btn.dataset.orderId,
                orderNumber: btn.dataset.orderNumber,
                customer: btn.dataset.customer,
                total: btn.dataset.total,
                paid: btn.dataset.paid,
                balance: btn.dataset.balance,
                redirectTo: btn.dataset.redirectTo || 'orders.index'
            });
        }
    });

    // Form submission validation: Ensure payment is complete before claiming
    claimForm?.addEventListener('submit', function(e) {
        if (currentBalanceDue > 0) {
            const tender = parseFloat(tenderInput?.value) || 0;
            if (tender < currentBalanceDue) {
                e.preventDefault();
                alert(`Order cannot be marked as claimed until the balance of ₱${currentBalanceDue.toFixed(2)} is fully paid.`);
                return false;
            }
        }

        if (!claimForm.hasAttribute('data-claim-confirmed')) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const custName = document.getElementById('claim-customer-name')?.textContent || 'Customer';
            const balText = document.getElementById('claim-balance-due-text')?.textContent || '₱0.00';
            const tenderVal = parseFloat(tenderInput?.value) || 0;

            const detailsHtml = `
                <div class="flex justify-between py-0.5 border-b border-[#182830]/10">
                    <span class="text-slate-600 font-bold">Customer:</span>
                    <strong>${custName}</strong>
                </div>
                <div class="flex justify-between py-0.5 border-b border-[#182830]/10">
                    <span class="text-slate-600 font-bold">Balance Settled:</span>
                    <strong>${balText}</strong>
                </div>
                <div class="flex justify-between pt-1">
                    <span class="text-[#182830] font-black uppercase">Cash Tendered:</span>
                    <strong class="font-recoleta text-base text-emerald-700">₱${tenderVal.toFixed(2)}</strong>
                </div>
            `;

            if (window.TrowaConfirm) {
                window.TrowaConfirm({
                    title: 'Confirm Payment & Order Release',
                    message: 'Confirm collecting payment and releasing clean laundry ticket to customer?',
                    badge: 'Counter Settlement',
                    type: 'check',
                    confirmText: 'Yes, Settle & Release ➔',
                    cancelText: 'Back to Edit',
                    details: detailsHtml
                }, function() {
                    claimForm.setAttribute('data-claim-confirmed', 'true');
                    const submitBtn = claimForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.click();
                    } else {
                        claimForm.submit();
                    }
                });
                return false;
            }
        }

        claimForm.removeAttribute('data-claim-confirmed');
    });
})();
</script>
