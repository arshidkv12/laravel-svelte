<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        Inertia::flash([
            'message' => 'Invoice successfully',
            'type' => 'success'
        ]);
        
        return redirect()
            ->route('invoices.index');
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
