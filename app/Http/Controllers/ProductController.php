<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('sku', 'like', '%' . $request->search . '%')
                ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
    
    
        $statusOptions = [
            ['value' => '1', 'label' => 'Active'],
            ['value' => '0', 'label' => 'Inactive'],
        ];

        $products = $query
            ->orderBy('id', 'desc')
            ->paginate(25);

        return Inertia::render('Products/Index', [
            'filters' => $request->only(['search', 'status']),
            'products' => $products,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'status', 'category_id']),
            'statusOptions' => $statusOptions
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
