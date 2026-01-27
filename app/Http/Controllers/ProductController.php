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


    public function create(Request $request){
        return Inertia::render('Products/Create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'barcode'     => 'nullable|string|unique:products,barcode',
            'price'       => 'required|numeric|min:0',
            'tax'         => 'nullable|numeric|min:0',
            'quantity'    => 'nullable|required|integer|min:0',
            'status'      => 'boolean',
        ],
        [
            'status.boolean' => 'Please select status',
        ]);

        return Product::create($data);
    }


    public function show(Product $product)
    {
        return Inertia::render('Products/Show', 
            ['product' => $product]
        );
    }

    /**
     * Edit job card
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'sku'         => 'nullable|string',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'barcode'     => 'nullable|string|unique:products,barcode,' . $product->id,
            'price'       => 'sometimes|numeric|min:0',
            'tax'         => 'nullable|numeric|min:0',
            'quantity'    => 'sometimes|integer|min:0',
            'status'      => 'boolean',
        ],[
            'status.boolean' => 'Please select status',
        ]);

        $product->update($validated);

        Inertia::flash([
            'message' => 'Customer created successfully',
            'type' => 'success'
        ]);

        return redirect()
            ->route('products.show', $product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back();
    }
}
