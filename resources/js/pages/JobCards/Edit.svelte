<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type Flash, type BreadcrumbItem } from '@/types';
    import { onMount } from 'svelte';
    import { Form, Link, page } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';

    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
    import { Separator } from '@/components/ui/separator';
    import { Input } from '@/components/ui/input';
    import { Textarea } from '@/components/ui/textarea';
    import { Label } from '@/components/ui/label';
    // import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

    import { 
        ArrowLeft,
        Plus,
        User,
        Search,
        Calendar,
        Briefcase,
        DollarSign,
        Wrench,
        CircleAlert,
        CircleCheckBig,
        CircleX,
        Clock,
        Save,
        Trash2,
    } from 'lucide-svelte';

    import CustomerSelect from '@/components/customer/CustomerSelect.svelte';
    import type { BaseFormSnippetProps } from '@/types/forms';
    import InputError from '@/components/InputError.svelte';

    let customer_id = $state<number | null>(null);
    let status = $state<string>('pending');

    let { jobCard, customers } = $props<{
        jobCard: {
            id: number;
            customer_id: number;
            item: string;
            problem: string;
            estimated_cost: string | null;
            delivery_date: string | null;
            notes: string | null;
            status: string;
            created_at: string;
            updated_at: string;
        };
        customers: Array<{
            value: string;
            label: string;
        }>;
    }>();

    $effect(() => {  
        const flash = $page.flash as Flash;
        if (flash?.message) {
            if (flash.type === 'success') {
                toast.success(flash.message);
            } else if (flash.type === 'error') {
                toast.error(flash.message);
            }
        }
    });

    onMount(() => { 
        customer_id = jobCard.customer_id;
        status = jobCard.status;
    });

    const breadcrumbs: BreadcrumbItem[] = $derived([
        { title: 'Job Cards', href: '/job-cards' },
        { title: jobCard.item || 'Job Card', href: `/job-cards/${jobCard.id}` },
        { title: 'Edit Job Card', href: `/job-cards/${jobCard.id}/edit` },
    ]);

    const statusOptions = [
        { value: 'pending', label: 'Pending', icon: Clock, color: 'text-yellow-600 bg-yellow-50' },
        { value: 'diagnosis', label: 'Diagnosis', icon: Search, color: 'text-blue-600 bg-blue-50' },
        { value: 'repairing', label: 'Repairing', icon: Wrench, color: 'text-orange-600 bg-orange-50' },
        { value: 'completed', label: 'Completed', icon: CircleCheckBig, color: 'text-green-600 bg-green-50' },
        { value: 'delivered', label: 'Delivered', icon: CircleCheckBig, color: 'text-emerald-600 bg-emerald-50' },
        { value: 'cancelled', label: 'Cancelled', icon: CircleX, color: 'text-red-600 bg-red-50' },
        { value: 'on_hold', label: 'On Hold', icon: CircleAlert, color: 'text-gray-600 bg-gray-50' }
    ];

    function formatCurrency(value: string | null): string {
        if (!value) return '$0.00';
        const num = parseFloat(value.replace(/[^\d.-]/g, ''));
        if (isNaN(num)) return '$0.00';
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(num);
    }

    function formatDate(dateString: string | null): string {
        if (!dateString) return 'Not set';
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    function getStatusInfo() {
        return statusOptions.find(s => s.value === status) || statusOptions[0];
    }
</script>

<svelte:head>
    <title>Edit Job Card: {jobCard.item}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="min-h-screen bg-gray-50">
        <Form 
            method="put" 
            action={route('job-cards.update', jobCard.id)} 
            class="space-y-6">
            {#snippet children({ errors, processing }: BaseFormSnippetProps)}
            
            <div class="p-4 md:p-6 mx-auto">
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
                            <h1 class="text-lg font-bold text-gray-900">
                                Edit Job Card: {jobCard.item}
                            </h1>
                            <p class="text-sm text-gray-500">
                                Update the details of this job card
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
                                    Update customer for this job card
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- Customer Search/Select -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium">Select Customer *</Label>
                                    <CustomerSelect 
                                        initCustomers={customers} 
                                        bind:modelValue={customer_id} 
                                    />
                                    <InputError class="mt-1" message={errors.customer_id} />
                                </div>

                                <!-- Quick Add Customer Button -->
                                <div class="pt-2">
                                    <Link href="/customers/create">
                                        <Button 
                                            variant="outline"
                                            class="w-full gap-2"
                                        >
                                            <Plus class="h-4 w-4" />
                                            Add New Customer
                                        </Button>
                                    </Link>
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
                                    Update the job information
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- Item Name -->
                                <div class="space-y-2">
                                    <Label for="item" class="text-sm font-medium">Item Name *</Label>
                                    <Input
                                        id="item"
                                        name="item"
                                        value={jobCard.item}
                                        placeholder="e.g., iPhone 13, Laptop Dell XPS, Toyota Camry"
                                    />
                                    <InputError class="mt-1" message={errors.item} />
                                </div>

                                <!-- Problem Description -->
                                <div class="space-y-2">
                                    <Label for="problem" class="text-sm font-medium">Problem Description *</Label>
                                    <Textarea
                                        id="problem"
                                        name="problem"
                                        value={jobCard.problem}
                                        placeholder="Describe the problem, issue, or service required..."
                                        class="min-h-[200px]"
                                    />
                                    <InputError class="mt-1" message={errors.problem} />
                                </div>

                                <!-- Status -->
                                <div class="space-y-2">
                                    <Label for="status" class="text-sm font-medium">Status</Label>
                                    <!-- <Select bind:value={status} name="status">
                                        <SelectTrigger>
                                            <SelectValue>
                                                <div class="flex items-center gap-2">
                                                    {#if getStatusInfo().icon}
                                                        <svelte:component this={getStatusInfo().icon} class="h-4 w-4" />
                                                    {/if}
                                                    <span>{getStatusInfo().label}</span>
                                                </div>
                                            </SelectValue>
                                        </SelectTrigger>
                                        <SelectContent>
                                            {#each statusOptions as option}
                                                <SelectItem value={option.value}>
                                                    <div class="flex items-center gap-2">
                                                        {#if option.icon}
                                                            <svelte:component this={option.icon} class="h-4 w-4" />
                                                        {/if}
                                                        <span>{option.label}</span>
                                                    </div>
                                                </SelectItem>
                                            {/each}
                                        </SelectContent>
                                    </Select> 
                                    <InputError class="mt-1" message={errors.status} /> -->
                                </div>

                                <!-- Estimated Cost & Delivery Date -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Estimated Cost -->
                                    <div class="space-y-2">
                                        <Label for="estimated_cost" class="text-sm font-medium">Estimated Cost</Label>
                                        <div class="relative">
                                            <DollarSign class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                            <Input
                                                id="estimated_cost"
                                                name="estimated_cost"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                value={jobCard.estimated_cost || ''}
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
                                                name="delivery_date"
                                                type="date"
                                                value={jobCard.delivery_date ? jobCard.delivery_date.split('T')[0] : ''}
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
                                        name="notes"
                                        value={jobCard.notes || ''}
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
                                    type="submit"
                                    disabled={processing}
                                    class="w-full gap-2 bg-blue-600 hover:bg-blue-700 text-white"
                                >
                                    {#if processing}
                                        <div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                        Saving...
                                    {:else}
                                        <Save class="h-4 w-4" />
                                        Save Changes
                                    {/if}
                                </Button>
                                
                                <Button
                                    onclick={(e) => {e.preventDefault(); history.back()}}
                                    variant="outline"
                                    class="w-full gap-2 mb-4"
                                >
                                    <ArrowLeft class="h-4 w-4" />
                                    Cancel
                                </Button>

                                <!-- Delete Button -->
                                <Form 
                                    method="delete" 
                                    action={route('job-cards.destroy', jobCard.id)}
                                    on:submit={(e) => {
                                        if (!confirm('Are you sure you want to delete this job card? This action cannot be undone.')) {
                                            e.preventDefault();
                                        }
                                    }}
                                >
                                    <Button
                                        type="submit"
                                        variant="destructive"
                                        class="w-full gap-2"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                        Delete Job Card
                                    </Button>
                                </Form>
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
                                            <!-- {#if customer_id}
                                                {customers.find(c => c.value === customer_id.toString())?.label || 'Loading...'}
                                            {:else}
                                                Not selected
                                            {/if} -->
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Status:</span>
                                        <span class="font-medium text-gray-900">
                                            {getStatusInfo().label}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Estimated Cost:</span>
                                        <span class="font-medium text-gray-900">
                                            {formatCurrency(jobCard.estimated_cost)}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Delivery Date:</span>
                                        <span class="font-medium text-gray-900">
                                            {formatDate(jobCard.delivery_date)}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Created:</span>
                                        <span class="font-medium text-gray-900">
                                            {formatDate(jobCard.created_at)}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Last Updated:</span>
                                        <span class="font-medium text-gray-900">
                                            {formatDate(jobCard.updated_at)}
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
                                    <CircleCheckBig class="h-4 w-4 text-green-500 mt-0.5 shrink-0" />
                                    <span>Update the status to reflect current progress</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <CircleCheckBig class="h-4 w-4 text-green-500 mt-0.5 shrink-0" />
                                    <span>Adjust delivery dates if needed</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <CircleCheckBig class="h-4 w-4 text-green-500 mt-0.5 shrink-0" />
                                    <span>Add internal notes for technicians and follow-up actions</span>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
            {/snippet}
        </Form>
    </div>
</AppLayout>