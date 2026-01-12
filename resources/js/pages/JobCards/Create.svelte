<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type Flash, type BreadcrumbItem } from '@/types';
    import { onMount } from 'svelte';
    import { Form, Link, page } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';
    import CreateCustomerModal from '@/components/customer/CreateCustomerModal.svelte';

    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
    import { Separator } from '@/components/ui/separator';
    import { Input } from '@/components/ui/input';
    import { Textarea } from '@/components/ui/textarea';
    import { Label } from '@/components/ui/label';

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
    } from 'lucide-svelte';

    import CustomerSelect from '@/components/customer/CustomerSelect.svelte';
    import type { BaseFormSnippetProps } from '@/types/forms';
    import InputError from '@/components/InputError.svelte';
    import FilePondUpload from '@/components/job/FilePondUpload.svelte';

    let customer_id = $state<number | null>(null);
    let customerDialogOpen = $state(false);
    let disableFormSubmit = $state(false);
    let jobCardFiles = $state([]);
      
    let { customers, csrf_token } = $props<{
        customers:  Array<{
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
        const urlParams = new URLSearchParams(window.location.search);
        customer_id = Number(urlParams.get('customer_id'));
    });

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Job Cards', href: '/job-cards' },
        { title: 'Create Job Card', href: '/job-cards/create' },
    ];

    const statusOptions = [
        { value: 'pending', label: 'Pending', icon: Clock, color: 'text-yellow-600 bg-yellow-50' },
        { value: 'diagnosis', label: 'Diagnosis', icon: Search, color: 'text-blue-600 bg-blue-50' },
        { value: 'repairing', label: 'Repairing', icon: Wrench, color: 'text-orange-600 bg-orange-50' },
        { value: 'completed', label: 'Completed', icon: CircleCheckBig, color: 'text-green-600 bg-green-50' },
        { value: 'delivered', label: 'Delivered', icon: CircleCheckBig, color: 'text-emerald-600 bg-emerald-50' },
        { value: 'cancelled', label: 'Cancelled', icon: CircleX, color: 'text-red-600 bg-red-50' },
        { value: 'on_hold', label: 'On Hold', icon: CircleAlert, color: 'text-gray-600 bg-gray-50' }
    ];

    function formatCurrency(value: string): string {
        const num = parseFloat(value.replace(/[^\d.-]/g, ''));
        if (isNaN(num)) return '$0.00';
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(num);
    }

  async function handleSubmit() {

    
  }
</script>

<svelte:head>
    <title>Create New Job Card</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <CreateCustomerModal
      bind:open={customerDialogOpen}
    />
    <div class="min-h-screen bg-gray-50">
      <Form 
        method="post" 
        action={route('job-cards.store')} 
        class="space-y-6">
        {#snippet children({ errors, processing }: BaseFormSnippetProps)}
        
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
                        <h1 class="text-lg font-bold text-gray-900">
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
                                <Label class="text-sm font-medium">Select Customer *</Label>
                                <CustomerSelect initCustomers={customers}  bind:modelValue={customer_id} />
                                <InputError class="mt-1" message={errors.customer_id} />
                            </div>

                            <!-- Quick Add Customer Button -->
                            <div class="pt-2">
                              <Link on:click={(e)=> {e.preventDefault();customerDialogOpen=true}}>
                                <Button 
                                    variant="outline"
                                    class="w-full gap-2 cursor-pointer"
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
                                Enter the job information
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Item Name -->
                            <div class="space-y-2">
                                <Label for="item" class="text-sm font-medium">Item Name *</Label>
                                <Input
                                    id="item"
                                    name="item"
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
                                  placeholder="Describe the problem, issue, or service required..."
                                  class="min-h-[200px]"
                              />
                              <InputError class="mt-1" message={errors.problem} />
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
                                            name="estimated_cost"
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
                                            name="delivery_date"
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
                                    name="notes"
                                    placeholder="Any internal notes, special instructions, or observations..."
                                    rows={3}
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- file upload -->
                    <Card class="p-4">
                        <FilePondUpload bind:files={jobCardFiles} {csrf_token} bind:disableFormSubmit={disableFormSubmit}/>
                    </Card>

                    <!-- Bottom Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <Button 
                                onclick={()=>history.back()}
                                variant="outline" 
                                class="gap-2 cursor-pointer"
                            >
                                <ArrowLeft class="h-4 w-4" />
                                Back 
                            </Button>
                            
                            <div class="flex items-center gap-3">
                                <Button
                                    type="submit"
                                    disabled={processing}
                                    class="w-full gap-2 bg-blue-600 hover:bg-blue-700 text-white cursor-pointer"
                                >
                                    {#if processing}
                                        <div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                        Saving...
                                    {:else}
                                        <Save class="h-4 w-4" />
                                        Save Changes
                                    {/if}
                                </Button>
                            </div>
                        </div>
                    </div>

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
                                disabled={processing || disableFormSubmit}
                                class="w-full gap-2 bg-blue-600 hover:bg-blue-700 text-white cursor-pointer"
                            >
                                {#if processing}
                                    <div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                    Creating...
                                {:else if disableFormSubmit}
                                    <Plus class="h-4 w-4" />                                    
                                    Create Job Card
                                {:else}
                                    <Plus class="h-4 w-4" />
                                    Create Job Card
                                {/if}
                            </Button>
                            
                            <Link href="/job-cards">
                              <Button
                                  variant="outline"
                                  class="w-full gap-2 cursor-pointer"
                              >
                                  <ArrowLeft class="h-4 w-4" />
                                  Cancel
                              </Button>
                            </Link>
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
                                        <!-- {selectedCustomer ? selectedCustomer.name : 'Not selected'} -->
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Status:</span>
                                    <span class="font-medium text-gray-900">
                                        <!-- {statusOptions.find(s => s.value === form.status)?.label || 'Pending'} -->
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Estimated Cost:</span>
                                    <span class="font-medium text-gray-900">
                                        <!-- {form.estimated_cost ? formatCurrency(form.estimated_cost) : '$0.00'} -->
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Delivery Date:</span>
                                    <span class="font-medium text-gray-900">
                                        <!-- {form.delivery_date ? new Date(form.delivery_date).toLocaleDateString('en-US', { 
                                            month: 'short', 
                                            day: 'numeric', 
                                            year: 'numeric' 
                                        }) : 'Not set'} -->
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
                                <span>Be specific with item name and problem description</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <CircleCheckBig class="h-4 w-4 text-green-500 mt-0.5 shrink-0" />
                                <span>Set realistic delivery dates to manage customer expectations</span>
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