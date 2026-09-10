@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Finance</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Record Payment</h1>
    </div>

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6">
        <form action="{{ route('payments.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Link to appointment (optional) --}}
            <div x-data='appointmentPicker(@json($appointmentOptions), "{{ old('appointment_id', request('appointment_id')) }}")' class="relative">
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">
                    Appointment <span class="normal-case font-normal">(optional — search by client or service)</span>
                </label>
                <input type="hidden" name="appointment_id" :value="selectedId">
                <input type="text" x-model="query"
                       @focus="open = true"
                       @click.outside="open = false"
                       autocomplete="off"
                       placeholder="Search or leave blank for walk-in…"
                       class="input-field">

                <div x-show="open && filtered.length" x-cloak
                     class="absolute z-10 mt-1 w-full max-h-64 overflow-y-auto bg-ts-surface border border-ts-border-soft rounded-xl shadow-lg">
                    <template x-for="appt in filtered" :key="appt.id">
                        <button type="button" @click="select(appt)"
                                class="block w-full text-left px-3 py-2 text-sm hover:bg-ts-surface-low transition"
                                x-text="appt.label"></button>
                    </template>
                </div>

                <button type="button" x-show="selectedId" x-cloak @click="clear()"
                        class="text-xs text-ts-text-subtle hover:text-ts-error underline mt-1.5">
                    Clear — record as walk-in instead
                </button>
            </div>

            {{-- Customer (shown when no appointment selected) --}}
            <div id="customer-field">
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">
                    Client <span class="normal-case font-normal">(optional)</span>
                </label>
                <select name="customer_id" id="customer_id" class="input-field">
                    <option value="">— Select client —</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Guest name: for a customer who prefers not to be added to the client list --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">
                    Guest Name <span class="normal-case font-normal">(optional — for the receipt only, no client record created)</span>
                </label>
                <input type="text" name="guest_name" value="{{ old('guest_name') }}"
                       placeholder="e.g. first name only, if they'd rather not share contact details"
                       class="input-field">
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Amount (€) *</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                           value="{{ old('amount') }}" placeholder="0.00"
                           class="input-field @error('amount') ring-2 ring-red-400 @enderror">
                    @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Method *</label>
                    <select name="method" class="input-field @error('method') ring-2 ring-red-400 @enderror">
                        @foreach(['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank Transfer', 'other' => 'Other'] as $val => $label)
                        <option value="{{ $val }}" {{ old('method', 'cash') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Status</label>
                <select name="status" class="input-field">
                    <option value="paid" {{ old('status', 'paid') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            {{-- Products sold (optional): tracked for stock + the Z report's retail breakdown --}}
            <div x-data='productPicker(@json($products), "{{ old("products.0.product_id", request("product_id")) }}")' class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">
                        Products Sold <span class="normal-case font-normal">(optional)</span>
                    </label>
                    <button type="button" @click="addRow()" class="text-xs text-ts-primary hover:underline font-semibold">+ Add product</button>
                </div>

                <template x-for="(row, index) in rows" :key="row.key">
                    <div class="flex items-center gap-2">
                        <select :name="'products[' + index + '][product_id]'" x-model="row.productId" @change="syncAmount()" class="input-field flex-1">
                            <option value="">— Select product —</option>
                            <template x-for="p in products" :key="p.id">
                                <option :value="p.id" x-text="p.name + ' (€' + parseFloat(p.sell_price).toFixed(2) + ')'"></option>
                            </template>
                        </select>
                        <input type="number" :name="'products[' + index + '][quantity]'" x-model.number="row.quantity" @input="syncAmount()"
                               min="1" class="input-field w-20">
                        <span class="text-sm text-ts-text-muted w-16 text-right shrink-0" x-text="'€' + lineTotal(row).toFixed(2)"></span>
                        <button type="button" @click="removeRow(index)" class="text-ts-error text-lg leading-none px-1">&times;</button>
                    </div>
                </template>

                <p x-show="rows.length" x-cloak class="text-xs text-ts-text-subtle">
                    Products subtotal: €<span x-text="productsTotal().toFixed(2)"></span> — added to the Amount above automatically.
                </p>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Notes</label>
                <textarea name="notes" rows="2" placeholder="Optional notes…" class="input-field">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    Record Payment
                </button>
                <a href="{{ route('payments.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-medium text-ts-text-muted hover:bg-ts-surface-mid transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function appointmentPicker(options, initialId) {
    return {
        options,
        query: '',
        open: false,
        selectedId: initialId || '',

        get filtered() {
            const q = this.query.trim().toLowerCase();
            const list = q ? this.options.filter(o => o.label.toLowerCase().includes(q)) : this.options;
            return list.slice(0, 20);
        },

        select(appt) {
            this.selectedId = appt.id;
            this.query = appt.label;
            this.open = false;

            if (appt.customer_id) {
                document.getElementById('customer_id').value = appt.customer_id;
            }
            if (appt.amount) {
                document.getElementById('amount').value = parseFloat(appt.amount).toFixed(2);
            }
        },

        clear() {
            this.selectedId = '';
            this.query = '';
        },

        init() {
            if (this.selectedId) {
                const found = this.options.find(o => o.id == this.selectedId);
                if (found) this.select(found);
            }
        },
    };
}

function productPicker(products, initialProductId) {
    return {
        products,
        rows: [],
        nextKey: 0,
        lastProductsTotal: 0,

        init() {
            // Deep-link from Products → "Sell": jump straight to a pre-filled row
            // instead of making the user find and re-select the product themselves.
            if (initialProductId && this.products.some(p => p.id == initialProductId)) {
                this.rows.push({ key: this.nextKey++, productId: initialProductId, quantity: 1 });
                this.syncAmount();
            }
        },

        addRow() {
            this.rows.push({ key: this.nextKey++, productId: '', quantity: 1 });
        },

        removeRow(index) {
            this.rows.splice(index, 1);
            this.syncAmount();
        },

        lineTotal(row) {
            const product = this.products.find(p => p.id == row.productId);
            if (!product) return 0;
            return parseFloat(product.sell_price) * (parseInt(row.quantity) || 0);
        },

        productsTotal() {
            return this.rows.reduce((sum, row) => sum + this.lineTotal(row), 0);
        },

        // Keeps the Amount field in sync with the products subtotal, so a staff member
        // adding retail products can't forget to fold that total into the payment amount
        // (previously just a text warning — money that was silently missed at reconciliation).
        syncAmount() {
            const newTotal = this.productsTotal();
            const delta = newTotal - this.lastProductsTotal;
            this.lastProductsTotal = newTotal;

            if (delta === 0) return;

            const amountInput = document.getElementById('amount');
            const current = parseFloat(amountInput.value) || 0;
            amountInput.value = Math.max(0, current + delta).toFixed(2);
        },
    };
}
</script>
@endpush
