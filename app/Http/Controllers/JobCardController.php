<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\JobCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class JobCardController extends Controller
{
    /**
     * List job cards
     */
    public function index(Request $request)
    { 
        $jobCards = JobCard::with('customer')
            ->latest()
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('job_no', 'like', '%' . $request->search . '%')
                    ->orWhereHas('customer', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%');
                    });
                });
            })
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('JobCards/Index', [
            'filters' => $request->only(['search', 'status']),
            'jobCards' => $jobCards,
        ]);
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
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

        return Inertia::render('JobCards/Create', [
            'customers' => $customers,
        ]);
    }

    /**
     * Store new job card
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'item'          => 'required|string|max:255',
            'problem'       => 'required|string',
            'delivery_date' => 'nullable|date',
            'estimated_cost'=> 'nullable|numeric',
        ], [
            'customer_id.required'   => 'Please select a customer before submitting.',
            'customer_id.exists'   => 'Please select a customer before submitting.',
            'item.required'          => 'Please provide the name of the item.',
            'item.max'               => 'The item name may not exceed 255 characters.',
            'problem.required'       => 'Please describe the problem clearly.',
            'delivery_date.date'     => 'Please enter a valid delivery date.',
            'estimated_cost.numeric' => 'Estimated cost must be a valid number.',
        ]);

        
        if ($validator->fails()) {
            Inertia::flash([
                'message' => 'Please fix the errors.',
                'type' => 'error'
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        JobCard::create($validator->validated());
        
        return redirect()
            ->route('job-cards.index')
            ->with('success', 'Job card created successfully');
    }

    /**
     * View job card
     */
    public function show(JobCard $jobCard)
    {
        return Inertia::render('JobCards/Show', [
            'jobCard' => $jobCard,
        ]);
    }

    /**
     * Edit job card
     */
    public function edit(JobCard $jobCard)
    {
        $customers = Customer::where('id', $jobCard->customer_id)
                        ->get()
                        ->map(fn ($c) => [
                            'value' => $c->id,
                            'label' => "{$c->name} - {$c->phone}",
                        ]);
        return Inertia::render('JobCards/Edit', [
            'jobCard' => $jobCard,
            'customers' => $customers
        ]);
    }

    /**
     * Update job card
     */
    public function update(Request $request, JobCard $jobCard)
    {
        // $validated = $request->validate([
        //     'status'         => 'required|in:new,in_progress,waiting_for_parts,ready,delivered',
        //     'item'           => 'nullable|string|max:255',
        //     'problem'        => 'nullable|string',
        //     'delivery_date'  => 'nullable|date',
        //     'estimated_cost' => 'nullable|numeric',
        //     'notes'          => 'nullable|string',
        // ]);

         $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'item'          => 'required|string|max:255',
            'problem'       => 'required|string',
            'delivery_date' => 'nullable|date',
            'estimated_cost'=> 'nullable|numeric',
        ], [
            'customer_id.required'   => 'Please select a customer before submitting.',
            'customer_id.exists'   => 'Please select a customer before submitting.',
            'item.required'          => 'Please provide the name of the item.',
            'item.max'               => 'The item name may not exceed 255 characters.',
            'problem.required'       => 'Please describe the problem clearly.',
            'delivery_date.date'     => 'Please enter a valid delivery date.',
            'estimated_cost.numeric' => 'Estimated cost must be a valid number.',
        ]);
          
        if ($validator->fails()) {
            Inertia::flash([
                'message' => 'Please fix the errors.',
                'type' => 'error'
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $jobCard->update($validator->validated());

        return redirect()
            ->route('job-cards.index')
            ->with('success', 'Job card updated successfully');

    }

    /**
     * Printable view
     */
    public function print(JobCard $jobCard)
    {
        return Inertia::render('JobCards/Print', [
            'jobCard' => $jobCard,
        ]);
    }




    /**
     * Display job cards for a specific customer.
     * Route: GET /customer-job-cards/{customer}?status=all&search=...
     */
    public function jobCardsByCustomer(Request $request, Customer $customer)
    {
        // Build query for job cards belonging to this customer
        $query = JobCard::with('customer')
            ->where('customer_id', $customer->id)
            ->latest();
        
        // Apply status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('job_number', 'like', "%{$search}%");
            });
        }
        
        // Apply priority filter
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }
        
        // Apply date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Paginate results (15 per page)
        $jobCards = $query->paginate(15)->withQueryString();
        
        // Transform data for Inertia/Svelte
        $transformedJobCards = $jobCards->map(function ($jobCard) {
            return [
                'id' => $jobCard->id,
                'job_number' => $jobCard->job_number,
                'title' => $jobCard->title,
                'description' => $jobCard->description,
                'status' => $jobCard->status,
                'priority' => $jobCard->priority,
                'customer' => [
                    'id' => $jobCard->customer->id,
                    'name' => $jobCard->customer->name,
                ],
                'created_at' => $jobCard->created_at->toISOString(),
                'updated_at' => $jobCard->updated_at->toISOString(),
                'due_date' => $jobCard->due_date?->toISOString(),
                'formatted_created_at' => $jobCard->created_at->format('M d, Y'),
                'formatted_due_date' => $jobCard->due_date?->format('M d, Y'),
            ];
        });
        
        // Get customer stats
        $customerStats = [
            'total_job_cards' => $customer->jobCards()->count(),
            'pending_jobs' => $customer->jobCards()->where('status', 'pending')->count(),
            'in_progress_jobs' => $customer->jobCards()->where('status', 'in-progress')->count(),
            'completed_jobs' => $customer->jobCards()->where('status', 'completed')->count(),
            'cancelled_jobs' => $customer->jobCards()->where('status', 'cancelled')->count(),
        ];
        
        // Get status counts for filter
        $statusCounts = [
            'all' => $customer->jobCards()->count(),
            'pending' => $customer->jobCards()->where('status', 'pending')->count(),
            'in-progress' => $customer->jobCards()->where('status', 'in-progress')->count(),
            'completed' => $customer->jobCards()->where('status', 'completed')->count(),
            'cancelled' => $customer->jobCards()->where('status', 'cancelled')->count(),
            'on_hold' => $customer->jobCards()->where('status', 'on_hold')->count(),
        ];
        
        // Return Inertia response
        return Inertia::render('JobCards/CustomerIndex', [
            'jobCards' => $transformedJobCards,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'created_at' => $customer->created_at->toISOString(),
                'formatted_created_at' => $customer->created_at->format('M d, Y'),
            ],
            'customerStats' => $customerStats,
            'statusCounts' => $statusCounts,
            'filters' => $request->only(['search', 'status', 'priority', 'date_from', 'date_to']),
            'pagination' => [
                'current_page' => $jobCards->currentPage(),
                'from' => $jobCards->firstItem(),
                'to' => $jobCards->lastItem(),
                'total' => $jobCards->total(),
                'links' => $jobCards->linkCollection()->toArray(),
            ],
        ]);
    }

}
