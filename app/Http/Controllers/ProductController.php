<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesApprovals;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Salon;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use HandlesApprovals;

    public function index()
    {
        $user = Auth::user();

        $products = Product::when(!$user->isSuperAdmin(), fn($q) => $q->where('salon_id', $user->salon_id))
            ->orderBy('name')
            ->paginate(24);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $user = Auth::user();
        $salons = $user->isSuperAdmin() ? Salon::orderBy('name')->get() : collect();
        return view('products.create', compact('salons'));
    }

    public function store(ProductRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $data['salon_id'] = $user->isSuperAdmin()
            ? $request->input('salon_id')
            : $user->salon_id;

        $result = $this->applyOrQueue(Product::class, $data, null, $user);

        return redirect()->route('products.index')->with('success', $result === 'queued'
            ? 'Product submitted for owner approval.'
            : 'Product created.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $user = Auth::user();
        $salons = $user->isSuperAdmin() ? Salon::orderBy('name')->get() : collect();
        return view('products.edit', compact('product', 'salons'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($user->isSuperAdmin() && $request->has('salon_id')) {
            $data['salon_id'] = $request->input('salon_id');
        }

        $result = $this->applyOrQueue(Product::class, $data, $product, $user);

        return redirect()->route('products.index')->with('success', $result === 'queued'
            ? 'Change submitted for owner approval.'
            : 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $result = $this->deleteOrQueue($product, Auth::user());

        return redirect()->route('products.index')->with('success', $result === 'queued'
            ? 'Deletion submitted for owner approval.'
            : 'Product deleted.');
    }
}
