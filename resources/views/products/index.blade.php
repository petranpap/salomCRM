@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Inventory</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Products</h1>
        </div>
        <div class="flex items-center gap-3">
            @if(Route::has('payments.create'))
            <a href="{{ route('payments.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border border-ts-border-soft text-ts-text hover:bg-ts-primary/5 transition">
                Sell Products
            </a>
            @endif
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                New Product
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-ts-border-soft bg-ts-surface-low">
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Product</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Category</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Stock</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden md:table-cell">Cost</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden md:table-cell">Price</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts-border-soft">
                @forelse($products as $product)
                <tr class="hover:bg-ts-surface-low transition-colors {{ $product->is_low_stock ? 'bg-amber-50/40' : '' }}">
                    <td class="px-5 py-4">
                        <div>
                            <p class="font-semibold text-ts-text">{{ $product->name }}</p>
                            @if($product->sku)
                            <p class="text-[10px] text-ts-text-subtle font-mono mt-0.5">{{ $product->sku }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 text-ts-text-muted hidden sm:table-cell">{{ $product->category ?? '—' }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold {{ $product->is_low_stock ? 'text-amber-600' : 'text-ts-text' }}">
                                {{ $product->stock_qty }}
                            </span>
                            @if($product->is_low_stock)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                Low
                            </span>
                            @endif
                        </div>
                        <p class="text-[10px] text-ts-text-subtle mt-0.5">min {{ $product->threshold_qty }}</p>
                    </td>
                    <td class="px-5 py-4 text-ts-text-muted font-mono text-xs hidden md:table-cell">€{{ number_format($product->cost_price, 2) }}</td>
                    <td class="px-5 py-4 font-semibold text-ts-text font-mono text-xs hidden md:table-cell">€{{ number_format($product->sell_price, 2) }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-3">
                            @if(Route::has('payments.create') && $product->status === 'active')
                            <a href="{{ route('payments.create', ['product_id' => $product->id]) }}"
                               class="text-xs font-semibold text-emerald-600 hover:underline">Sell</a>
                            @endif
                            <a href="{{ route('products.edit', $product) }}"
                               class="text-xs font-semibold text-ts-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}"
                                  onsubmit="return confirm('{{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request deletion of' }} {{ addslashes($product->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">
                                    {{ auth()->user()->isOwner() || auth()->user()->isSuperAdmin() ? 'Delete' : 'Request Deletion' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-14 text-center text-ts-text-subtle text-sm">
                        No products yet.
                        <a href="{{ route('products.create') }}" class="text-ts-primary font-semibold hover:underline">Add the first one.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($products->hasPages())
        <div class="px-5 py-4 border-t border-ts-border-soft">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection
