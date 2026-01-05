<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * List all customers with optional search
     */
    public function index(Request $request)
    {  
        $customers = Customer::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Customers/Index', [
            'filters' => $request->only(['search']),
            'customers' => $customers,
        ]);
    }

    /**
     * Show form to create a new customer
     */
    public function create()
    {
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a new customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:10|unique:customers,phone',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'address' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully');
    }

    /**
     * Show a single customer
     */
    public function show(Customer $customer)
    {
        return Inertia::render('Customers/Show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Show form to edit a customer
     */
    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update a customer
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully');
    }

    /**
     * Delete a customer
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully');
    }
}
