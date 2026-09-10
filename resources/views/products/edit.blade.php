@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Inventory</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Edit — {{ $product->name }}</h1>
    </div>

    @unless(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
    <div class="px-4 py-3 rounded-card bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        Changes will be submitted to the owner for approval before they go live.
    </div>
    @endunless

    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk p-6">
        <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')

            @if($salons->isNotEmpty())
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Salon</label>
                <select name="salon_id" class="input-field">
                    <option value="">— Unassigned —</option>
                    @foreach($salons as $salon)
                        <option value="{{ $salon->id }}" {{ old('salon_id', $product->salon_id) == $salon->id ? 'selected' : '' }}>{{ $salon->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Category *</label>
                    <input type="text" name="category" value="{{ old('category', $product->category) }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Cost Price (€)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ts-text-subtle text-sm">€</span>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" step="0.01"
                               class="input-field pl-7">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Sell Price (€)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ts-text-subtle text-sm">€</span>
                        <input type="number" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" min="0" step="0.01"
                               class="input-field pl-7">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Stock Quantity</label>
                    <input type="number" name="stock_qty" value="{{ old('stock_qty', $product->stock_qty) }}" min="0" class="input-field">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Low Stock Alert At</label>
                    <input type="number" name="threshold_qty" value="{{ old('threshold_qty', $product->threshold_qty) }}" min="0" class="input-field">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Status *</label>
                    <select name="status" required class="input-field">
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                    {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Save Changes' : 'Submit for Approval' }}
                </button>
                <a href="{{ route('products.index') }}"
                   class="px-6 py-2.5 rounded-xl text-sm font-semibold text-ts-text-muted hover:bg-ts-surface-low transition">
                    Cancel
                </a>
                <form method="POST" action="{{ route('products.destroy', $product) }}" class="ml-auto"
                      onsubmit="return confirm('{{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request deletion of' }} {{ addslashes($product->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 border border-red-200 transition">
                        {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request Deletion' }}
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
