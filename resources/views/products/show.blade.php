@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-espresso-subtle dark:text-espresso-muted mb-1">Products</p>
            <h1 class="font-display text-3xl font-semibold text-espresso dark:text-cream">{{ $product->name }}</h1>
        </div>
        <a href="{{ route('products.index') }}" class="btn-outline text-sm">← Back</a>
    </div>

    <div class="card flex flex-col md:flex-row gap-6">
        <div class="md:w-1/3">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-card w-full object-cover">
            @else
                <div class="rounded-card w-full h-48 bg-cream-soft dark:bg-cream-dark-soft flex items-center justify-center text-espresso-muted text-sm">No image</div>
            @endif
        </div>
        <div class="flex-1 space-y-3">
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-espresso-muted">SKU</span><p class="font-mono font-medium">{{ $product->sku }}</p></div>
                <div><span class="text-espresso-muted">Category</span><p>{{ $product->category }}</p></div>
                <div><span class="text-espresso-muted">Cost Price</span><p class="font-medium">€{{ number_format($product->cost_price, 2) }}</p></div>
                <div><span class="text-espresso-muted">Sell Price</span><p class="font-medium text-rose-sand">€{{ number_format($product->sell_price, 2) }}</p></div>
                <div><span class="text-espresso-muted">Stock</span>
                    <p class="font-medium {{ $product->is_low_stock ? 'text-rose-sand' : '' }}">
                        {{ $product->stock_qty }} units{{ $product->is_low_stock ? ' · Low Stock' : '' }}
                    </p>
                </div>
                <div><span class="text-espresso-muted">Status</span>
                    <span class="{{ $product->status === 'active' ? 'status-confirmed' : 'badge bg-gray-100 text-gray-500' }}">{{ $product->status }}</span>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <a href="{{ route('products.edit', $product) }}" class="btn-primary">Edit Product</a>
                <form action="{{ route('products.destroy', $product) }}" method="POST"
                      x-data
                      @submit.prevent="if(confirm('Delete this product?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-outline border-red-300 text-red-500 hover:bg-red-500 hover:text-white">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
