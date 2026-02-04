<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::query();

        if ($request->has('search') && $request->search) {
            $query->where(function ($query) use ($request) {
                $query->orWhereHas('customer', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%');
                });
            });
        }

        $allowedSorts = [
            'id',
            'created_at',
            'updated_at',
        ];

        $sortBy  = request('sort_by', 'id');
        $sortDir = request('sort_dir', 'desc');

        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'id';
        }

        $invoices = $query
            ->with('customer')
            ->orderBy($sortBy, $sortDir)
            ->paginate(25);


        return Inertia::render('Invoice/Index', [
            'invoices' => $invoices,
            'csrf_token' => csrf_token(),
            // 'filters' => $request->only(['search', 'status']),
            // 'statusOptions' => $statusOptions,
            // 'sort_by' => $sortBy, 
            // 'sort_dir' => $sortDir
        ]);
        
    }

    public function create(Request $request){
        
        $q = $request->get('q');
        $customer_id = $request->get('customer_id');
        $customers = Customer::latest()
            ->when($q, fn ($query) =>
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
            )
            ->when($customer_id, fn ($query) =>
                $query->where('id', $customer_id)
            )
            ->limit(5)          
            ->get()             
            ->map(fn ($c) => [
                'value' => $c->id,
                'label' => "{$c->name} - {$c->phone}",
            ]);
            
        return Inertia::render('Invoice/Create', [
            'initCustomerId' => $customer_id,
            'customers' => $customers,
            'csrf_token' => csrf_token()
        ]);
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'discount_amount'   => 'nullable|numeric|min:0',
            'amount_paid'       => 'nullable|numeric|min:0',
            'status'            => 'required|string',
            'job_card_id'       => 'nullable|exists:job_cards,id',
            'notes'             => 'nullable|string',
        ]);

        $validatedItems = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_type'           => 'required|string',
            'items.*.product_id'          => 'nullable|exists:products,id',
            'items.*.service_id'          => 'nullable|exists:services,id',
            'items.*.name'                => 'required|string',
            'items.*.quantity'            => 'required|numeric|min:0',
            'items.*.unit_price'          => 'required|numeric|min:0',
            'items.*.unit'                => 'nullable|string',
            'items.*.tax_rate'            => 'nullable|numeric|min:0',
        ],[
            'items.required'              => 'Please add items'
        ]);
        
        $invoice = Invoice::create($validated);

        foreach ($validatedItems['items'] as $item) {
            $invoice->items()->create($item);
        }

        Inertia::flash([
            'message' => 'Invoice successfully',
            'type' => 'success'
        ]);
        
        return redirect()
            ->route('invoices.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('customer');

        return Inertia::render('JobCards/Show', [
            'jobCard' => $invoice,
            'customer' => $invoice->customer,  
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
