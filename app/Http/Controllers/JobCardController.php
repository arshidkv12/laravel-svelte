<?php

namespace App\Http\Controllers;

use App\Models\JobCard;
use Illuminate\Http\Request;
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
    public function create()
    {
        return Inertia::render('JobCards/Create');
    }

    /**
     * Store new job card
     */
    public function store(Request $request)
    {dd($request->all());

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'item'          => 'nullable|string|max:255',
            'problem'       => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'estimated_cost'=> 'nullable|numeric',
        ]);

        JobCard::create($validated);

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
        return Inertia::render('JobCards/Edit', [
            'jobCard' => $jobCard,
        ]);
    }

    /**
     * Update job card
     */
    public function update(Request $request, JobCard $jobCard)
    {
        $validated = $request->validate([
            'status'         => 'required|in:new,in_progress,waiting_for_parts,ready,delivered',
            'item'           => 'nullable|string|max:255',
            'problem'        => 'nullable|string',
            'delivery_date'  => 'nullable|date',
            'estimated_cost' => 'nullable|numeric',
            'notes'          => 'nullable|string',
        ]);

        $jobCard->update($validated);

        return redirect()
            ->route('job-cards.show', $jobCard)
            ->with('success', 'Job card updated');
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
}
