<div class="bg-white rounded-lg shadow-md p-4 transition-transform transform hover:scale-105">
    @if($product->image_url)
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-lg mb-2 w-full h-32 object-cover">
    @else
        <div class="rounded-lg mb-2 w-full h-32 bg-cream-soft dark:bg-cream-dark-soft flex items-center justify-center text-espresso-muted text-xs">No image</div>
    @endif
    <h3 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h3>
    <p class="text-gray-600">{{ $product->category }}</p>
    <p class="text-gray-800 font-bold mt-1">${{ number_format($product->sell_price, 2) }}</p>
    @if($product->stock_qty <= $product->threshold_qty)
        <span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full absolute top-2 right-2">Low Stock</span>
    @endif
    <div class="mt-4">
        <a href="{{ route('products.show', $product) }}" class="text-blue-500 hover:underline">View Details</a>
    </div>
</div>