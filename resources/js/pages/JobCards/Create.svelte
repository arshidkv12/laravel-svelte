<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type Flash, type BreadcrumbItem } from '@/types';
    import { onMount } from 'svelte';
    import { page } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';

    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
    import { Separator } from '@/components/ui/separator';
    import { Input } from '@/components/ui/input';
    import { Textarea } from '@/components/ui/textarea';
    import { Label } from '@/components/ui/label';
    import * as Select from '@/components/ui/select';
    import { 
        ArrowLeft,
        Plus,
        User,
        Search,
        Calendar,
        Briefcase,
        FileText,
        DollarSign,
        Wrench,
        AlertCircle,
        CheckCircle,
        XCircle,
        Clock,
        Settings,
        MessageSquare,
        Tag
    } from 'lucide-svelte';

    // Use $props() in Svelte 5
    const { customers = [] } = $props<{
        customers?: Array<{
            id: number;
            name: string;
            phone?: string;
            email?: string;
        }>;
    }>();

    // Form state
    let form = $state({
        customer_id: '',
        item: '',
        problem: '',
        status: 'pending',
        estimated_cost: '',
        delivery_date: '',
        notes: ''
    });

    // UI state
    let isSubmitting = $state(false);
    let customerSearch = $state('');
    let filteredCustomers = $state<typeof customers>(customers);
    let selectedCustomer = $state<typeof customers[0] | null>(null);
    let showCustomerList = $state(false);

    // Filter customers based on search
    $effect(() => {
        // if (customerSearch.trim() === '') {
        //     filteredCustomers = customers;
        // } else {
        //     filteredCustomers = customers.filter(customer =>
        //         customer.name.toLowerCase().includes(customerSearch.toLowerCase()) ||
        //         customer.phone?.includes(customerSearch) ||
        //         customer.email?.toLowerCase().includes(customerSearch.toLowerCase())
        //     );
        // }
    });

    // Pre-select customer from query params
    onMount(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const customerId = urlParams.get('customer_id');
        
        if (customerId) {
            // const customer = customers.find(c => c.id.toString() === customerId);
            // if (customer) {
            //     selectCustomer(customer);
            // }
        }

        const flash = $page.flash as Flash;
        if (flash?.message) {
            if (flash.type === 'success') {
                toast.success(flash.message);
            } else if (flash.type === 'error') {
                toast.error(flash.message);
            }
        }
    });

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Job Cards', href: '/job-cards' },
        { title: 'Create Job Card', href: '/job-cards/create' },
    ];

    const statusOptions = [
        { value: 'pending', label: 'Pending', icon: Clock, color: 'text-yellow-600 bg-yellow-50' },
        { value: 'diagnosis', label: 'Diagnosis', icon: Search, color: 'text-blue-600 bg-blue-50' },
        { value: 'repairing', label: 'Repairing', icon: Wrench, color: 'text-orange-600 bg-orange-50' },
        { value: 'completed', label: 'Completed', icon: CheckCircle, color: 'text-green-600 bg-green-50' },
        { value: 'delivered', label: 'Delivered', icon: CheckCircle, color: 'text-emerald-600 bg-emerald-50' },
        { value: 'cancelled', label: 'Cancelled', icon: XCircle, color: 'text-red-600 bg-red-50' },
        { value: 'on_hold', label: 'On Hold', icon: AlertCircle, color: 'text-gray-600 bg-gray-50' }
    ];

    function selectCustomer(customer: typeof customers[0]) {
        selectedCustomer = customer;
        form.customer_id = customer.id.toString();
        showCustomerList = false;
        customerSearch = customer.name;
    }

    function clearCustomer() {
        selectedCustomer = null;
        form.customer_id = '';
        customerSearch = '';
    }

    function formatCurrency(value: string): string {
        const num = parseFloat(value.replace(/[^\d.-]/g, ''));
        if (isNaN(num)) return '$0.00';
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(num);
    }

    async function handleSubmit() {
        // Validation
        if (!form.customer_id) {
            toast.error('Please select a customer');
            return;
        }

        if (!form.item.trim()) {
            toast.error('Item name is required');
            return;
        }

        if (!form.problem.trim()) {
            toast.error('Problem description is required');
            return;
        }

        isSubmitting = true;

        try {
            const response = await fetch('/job-cards', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(form)
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to create job card');
            }

            const result = await response.json();
            
            toast.success('Job card created successfully');
            
            // Redirect to the new job card
            window.location.href = `/job-cards/${result.id}`;
        } catch (error: any) {
            toast.error(error.message || 'Failed to create job card');
            console.error('Submit error:', error);
        } finally {
            isSubmitting = false;
        }
    }
</script>

<svelte:head>
    <title>Create New Job Card</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="min-h-screen bg-gray-50">
        <div class="p-4 md:p-6  mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3">
                            <Button 
                                href="/job-cards"
                                variant="ghost" 
                                size="sm"
                                class="p-0 h-auto"
                            >
                                <ArrowLeft class="h-4 w-4 mr-2" />
                                Back to Jobs
                            </Button>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                            Create New Job Card
                        </h1>
                        <p class="text-sm text-gray-500">
                            Fill in the details to create a new job card
                        </p>
                    </div>
                </div>
                <Separator />
            </div>

            <!-- Form -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer Selection -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <User class="h-5 w-5 text-blue-600" />
                                Customer Information
                            </CardTitle>
                            <CardDescription>
                                Select a customer for this job card
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Customer Search/Select -->
                            <div class="space-y-2">
                                <Label for="customer-search" class="text-sm font-medium">Select Customer *</Label>
                                <div class="relative">
                                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                    <Input
                                        id="customer-search"
                                        bind:value={customerSearch}
                                        placeholder="Search customers by name, phone, or email..."
                                        class="pl-10"
                                        onfocus={() => showCustomerList = true}
                                    />
                                </div>

                                <!-- Selected Customer Preview -->
                                {#if selectedCustomer}
                                    <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                                        <div class="flex items-center justify-between">
                                            <div class="space-y-1">
                                                <div class="font-medium text-green-900">{selectedCustomer.name}</div>
                                                <div class="text-sm text-green-700">
                                                    {#if selectedCustomer.phone}
                                                        <div class="flex items-center gap-1">
                                                            {selectedCustomer.phone}
                                                        </div>
                                                    {/if}
                                                    {#if selectedCustomer.email}
                                                        <div class="flex items-center gap-1">
                                                            {selectedCustomer.email}
                                                        </div>
                                                    {/if}
                                                </div>
                                            </div>
                                            <Button 
                                                type="button" 
                                                variant="ghost" 
                                                size="sm"
                                                onclick={clearCustomer}
                                                class="text-red-600 hover:text-red-700 hover:bg-red-50"
                                            >
                                                Change
                                            </Button>
                                        </div>
                                    </div>
                                {:else}
                                    <!-- Customer List Dropdown -->
                                    {#if showCustomerList}
                                        <div class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                                            {#if filteredCustomers.length === 0}
                                                <div class="p-4 text-center text-gray-500">
                                                    No customers found
                                                </div>
                                            {:else}
                                                {#each filteredCustomers as customer}
                                                    <button
                                                        type="button"
                                                        onclick={() => selectCustomer(customer)}
                                                        class="w-full p-3 text-left hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors"
                                                    >
                                                        <div class="font-medium text-gray-900">{customer.name}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {#if customer.phone}
                                                                <span>{customer.phone}</span>
                                                            {/if}
                                                            {#if customer.email}
                                                                <span class="ml-2">{customer.email}</span>
                                                            {/if}
                                                        </div>
                                                    </button>
                                                {/each}
                                            {/if}
                                        </div>
                                    {/if}
                                {/if}
                            </div>

                            <!-- Quick Add Customer Button -->
                            <div class="pt-2">
                                <Button 
                                    href="/customers/create"
                                    variant="outline"
                                    class="w-full gap-2"
                                >
                                    <Plus class="h-4 w-4" />
                                    Add New Customer
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Job Details -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Briefcase class="h-5 w-5 text-blue-600" />
                                Job Details
                            </CardTitle>
                            <CardDescription>
                                Enter the job information
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Item Name -->
                            <div class="space-y-2">
                                <Label for="item" class="text-sm font-medium">Item Name *</Label>
                                <Input
                                    id="item"
                                    bind:value={form.item}
                                    placeholder="e.g., iPhone 13, Laptop Dell XPS, Toyota Camry"
                                    required
                                />
                            </div>

                            <!-- Problem Description -->
                            <div class="space-y-2">
                                <Label for="problem" class="text-sm font-medium">Problem Description *</Label>
                                <Textarea
                                    id="problem"
                                    bind:value={form.problem}
                                    placeholder="Describe the problem, issue, or service required..."
                                    rows={4}
                                    required
                                />
                            </div>

                            <!-- Status -->

                            <!-- Estimated Cost & Delivery Date -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Estimated Cost -->
                                <div class="space-y-2">
                                    <Label for="estimated_cost" class="text-sm font-medium">Estimated Cost</Label>
                                    <div class="relative">
                                        <DollarSign class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                        <Input
                                            id="estimated_cost"
                                            bind:value={form.estimated_cost}
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            class="pl-10"
                                        />
                                    </div>
                                </div>

                                <!-- Delivery Date -->
                                <div class="space-y-2">
                                    <Label for="delivery_date" class="text-sm font-medium">Delivery Date</Label>
                                    <div class="relative">
                                        <Calendar class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                        <Input
                                            id="delivery_date"
                                            bind:value={form.delivery_date}
                                            type="date"
                                            class="pl-10"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="space-y-2">
                                <Label for="notes" class="text-sm font-medium">Internal Notes</Label>
                                <Textarea
                                    id="notes"
                                    bind:value={form.notes}
                                    placeholder="Any internal notes, special instructions, or observations..."
                                    rows={3}
                                />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column - Actions & Summary -->
                <div class="space-y-6">
                    <!-- Actions Card -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader>
                            <CardTitle>Actions</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <Button
                                type="button"
                                onclick={handleSubmit}
                                disabled={isSubmitting || !form.customer_id || !form.item.trim() || !form.problem.trim()}
                                class="w-full gap-2 bg-blue-600 hover:bg-blue-700 text-white"
                            >
                                {#if isSubmitting}
                                    <div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                    Creating...
                                {:else}
                                    <Plus class="h-4 w-4" />
                                    Create Job Card
                                {/if}
                            </Button>

                            <Button
                                href="/job-cards"
                                variant="outline"
                                class="w-full gap-2"
                            >
                                <ArrowLeft class="h-4 w-4" />
                                Cancel
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Summary Card -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader>
                            <CardTitle>Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Customer:</span>
                                    <span class="font-medium text-gray-900">
                                        {selectedCustomer ? selectedCustomer.name : 'Not selected'}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Status:</span>
                                    <span class="font-medium text-gray-900">
                                        {statusOptions.find(s => s.value === form.status)?.label || 'Pending'}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Estimated Cost:</span>
                                    <span class="font-medium text-gray-900">
                                        {form.estimated_cost ? formatCurrency(form.estimated_cost) : '$0.00'}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Delivery Date:</span>
                                    <span class="font-medium text-gray-900">
                                        {form.delivery_date ? new Date(form.delivery_date).toLocaleDateString('en-US', { 
                                            month: 'short', 
                                            day: 'numeric', 
                                            year: 'numeric' 
                                        }) : 'Not set'}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Quick Tips -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader>
                            <CardTitle>Quick Tips</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-start gap-2">
                                <CheckCircle class="h-4 w-4 text-green-500 mt-0.5 shrink-0" />
                                <span>Be specific with item name and problem description</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <CheckCircle class="h-4 w-4 text-green-500 mt-0.5 shrink-0" />
                                <span>Set realistic delivery dates to manage customer expectations</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <CheckCircle class="h-4 w-4 text-green-500 mt-0.5 shrink-0" />
                                <span>Add internal notes for technicians and follow-up actions</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</AppLayout>