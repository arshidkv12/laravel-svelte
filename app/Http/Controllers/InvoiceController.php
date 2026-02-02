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
    public function index()
    {
        //
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
            'invoice_date'      => 'required|date',
            'due_date'          => 'nullable|date:invoice_date',
            'customer_id'       => 'required|exists:customers,id',
            'subtotal'          => 'required|numeric|min:0',
            'tax_amount'        => 'nullable|numeric|min:0',
            'discount_amount'   => 'nullable|numeric|min:0',
            'total_amount'      => 'required|numeric|min:0',
            'amount_paid'       => 'nullable|numeric|min:0',
            'status'            => 'required|string',
            'job_card_id'       => 'nullable|exists:job_cards,id',
            'notes'             => 'nullable|string',
        ]);
        
        Invoice::create($validated);


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
    public function show(string $id)
    {
        //
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
