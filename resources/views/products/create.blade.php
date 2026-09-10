@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Inventory</p>
        <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">New Product</h1>
    </div>

    @unless(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
    <div class="px-4 py-3 rounded-card bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        This will be submitted to the owner for approval before it goes live.
    </div>
    @endunless

    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk p-6">
        <form action="{{ route('products.store') }}" method="POST" class="space-y-5">
            @csrf

            @if($salons->isNotEmpty())
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Salon *</label>
                <select name="salon_id" class="input-field" required>
                    <option value="">Select salon…</option>
                    @foreach($salons as $salon)
                        <option value="{{ $salon->id }}" {{ old('salon_id') == $salon->id ? 'selected' : '' }}>{{ $salon->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="input-field @error('name') ring-2 ring-red-400 @enderror"
                           placeholder="e.g. Olaplex No.3">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku') }}"
                           class="input-field" placeholder="e.g. OLP-003">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Category *</label>
                    <input type="text" name="category" value="{{ old('category') }}" required
                           class="input-field @error('category') ring-2 ring-red-400 @enderror" placeholder="e.g. Hair Care">
                    @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand') }}"
                           class="input-field" placeholder="e.g. Olaplex">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Cost Price (€)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ts-text-subtle text-sm">€</span>
                        <input type="number" name="cost_price" value="{{ old('cost_price') }}" min="0" step="0.01"
                               class="input-field pl-7" placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Sell Price (€)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ts-text-subtle text-sm">€</span>
                        <input type="number" name="sell_price" value="{{ old('sell_price') }}" min="0" step="0.01"
                               class="input-field pl-7" placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Stock Quantity</label>
                    <input type="number" name="stock_qty" value="{{ old('stock_qty', 0) }}" min="0" class="input-field">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Low Stock Alert At</label>
                    <input type="number" name="threshold_qty" value="{{ old('threshold_qty', 5) }}" min="0" class="input-field">
                    <p class="text-[10px] text-ts-text-subtle mt-1">Alert when stock falls to this level</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Status *</label>
                    <select name="status" required class="input-field @error('status') ring-2 ring-red-400 @enderror">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                    {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Save Product' : 'Submit for Approval' }}
                </button>
                <a href="{{ route('products.index') }}"
                   class="px-6 py-2.5 rounded-xl text-sm font-semibold text-ts-text-muted hover:bg-ts-surface-low transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
