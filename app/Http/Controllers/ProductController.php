<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {

        $products = Product::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'filters' => $request->only(['search', 'status']),
            'products' => $products,
        ]);
    }
    

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'barcode'     => 'nullable|string|unique:products,barcode',
            'price'       => 'required|numeric|min:0',
            'tax'         => 'nullable|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'status'      => 'boolean',
        ]);

        return Product::create($data);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'barcode'     => 'nullable|string|unique:products,barcode,' . $product->id,
            'price'       => 'sometimes|numeric|min:0',
            'tax'         => 'nullable|numeric|min:0',
            'quantity'    => 'sometimes|integer|min:0',
            'status'      => 'boolean',
        ]);

        $product->update($data);

        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}
