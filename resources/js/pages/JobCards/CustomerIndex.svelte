<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type Flash, type BreadcrumbItem } from '@/types';
    import { onMount } from 'svelte';
    import { page } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';

    import { Button } from '@/components/ui/button';
    import { Badge } from '@/components/ui/badge';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
    import { Separator } from '@/components/ui/separator';
    import { Input } from '@/components/ui/input';
    import { 
        Search,
        Filter,
        Plus,
        Calendar,
        User,
        ChevronRight,
        Clock,
        CheckCircle,
        XCircle,
        AlertCircle,
        MoreVertical,
        Eye,
        SquarePen,
        Trash2,
        Briefcase,
        ArrowLeft,

        EllipsisVertical

    } from 'lucide-svelte';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuLabel,
        DropdownMenuSeparator,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import DeleteConfirmDialog from '@/components/confirm/DeleteConfirmDialog.svelte';

    // Define types
    type JobCard = {
        id: number;
        job_number: string;
        title: string;
        description?: string;
        status: string;
        customer: {
            id: number;
            name: string;
        };
        created_at: string;
        updated_at: string;
        due_date?: string;
        priority?: string;
    };

    type Customer = {
        id: number;
        name: string;
    };

    type PaginationLink = {
        url: string | null;
        label: string;
        active: boolean;
    };

    type Pagination = {
        current_page: number;
        from: number;
        to: number;
        total: number;
        links: PaginationLink[];
    };

    // Use $props() in Svelte 5 runes mode
    const props = $props<{
        jobCards?: JobCard[];
        customer?: Customer;
        filters?: {
            status?: string;
            search?: string;
        };
        pagination?: Pagination;
    }>();

    // Destructure with defaults
    const { 
        jobCards = [], 
        customer = undefined, 
        filters = {},
        pagination = {
            current_page: 1,
            from: 0,
            to: 0,
            total: 0,
            links: []
        }
    } = props;

    // Reactive state with runes
    let searchQuery = $state(filters.search || '');
    let selectedStatus = $state(filters.status || 'all');
    let isDeletingId = $state<number | null>(null);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Job Cards', href: '/job-cards' },
        ...(customer ? [
            { title: 'Customers', href: '/customers' },
            { title: customer.name, href: `/customers/${customer.id}` },
            { title: 'Job Cards', href: `/job-cards?customer_id=${customer.id}` }
        ] : [])
    ];

    const statusColors: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'in-progress': 'bg-blue-100 text-blue-800 border-blue-200',
        completed: 'bg-green-100 text-green-800 border-green-200',
        cancelled: 'bg-red-100 text-red-800 border-red-200',
        on_hold: 'bg-gray-100 text-gray-800 border-gray-200'
    };

    const statusIcons: Record<string, any> = {
        pending: Clock,
        'in-progress': Clock,
        completed: CheckCircle,
        cancelled: XCircle,
        on_hold: AlertCircle
    };

    const priorityColors: Record<string, string> = {
        high: 'bg-red-100 text-red-800',
        medium: 'bg-yellow-100 text-yellow-800',
        low: 'bg-blue-100 text-blue-800'
    };

    onMount(() => {
        const flash = $page.flash as Flash;
        if (flash?.message) {
            if (flash.type === 'success') {
                toast.success(flash.message);
            } else if (flash.type === 'error') {
                toast.error(flash.message);
            }
        }
    });

    function applyFilters() {
        const params = new URLSearchParams();
        
        if (searchQuery.trim()) {
            params.set('search', searchQuery.trim());
        }
        
        if (selectedStatus !== 'all') {
            params.set('status', selectedStatus);
        }
        
        if (customer) {
            params.set('customer_id', customer.id.toString());
        }
        
        window.location.href = `/job-cards${params.toString() ? '?' + params.toString() : ''}`;
    }

    function resetFilters() {
        searchQuery = '';
        selectedStatus = 'all';
        
        const params = new URLSearchParams();
        if (customer) {
            params.set('customer_id', customer.id.toString());
        }
        
        window.location.href = `/job-cards${params.toString() ? '?' + params.toString() : ''}`;
    }

    function formatDate(dateString: string) {
        if (!dateString) return '—';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    async function deleteJobCard(id: number) {
        isDeletingId = id;
        try {
            const response = await fetch(`/job-cards/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to delete job card');
            }

            toast.success('Job card deleted successfully');
            
            // Reload the page to reflect changes
            window.location.reload();
        } catch (error: any) {
            toast.error(error.message || 'Failed to delete job card');
            console.error('Delete error:', error);
        } finally {
            isDeletingId = null;
        }
    }


</script>

<svelte:head>
    <title>{customer ? `${customer.name}'s Job Cards` : 'Job Cards'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="min-h-screen bg-white">
        <div class="p-4 md:p-6 max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            {#if customer}
                                <Button 
                                    href={`/customers/${customer.id}`}
                                    variant="ghost" 
                                    size="sm"
                                    class="p-0 h-auto"
                                >
                                    <ArrowLeft class="h-4 w-4 mr-2" />
                                    Back to Customer
                                </Button>
                            {/if}
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                            {customer ? `${customer.name}'s Job Cards` : 'All Job Cards'}
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            {customer 
                                ? `Manage job cards for ${customer.name}` 
                                : 'Manage all job cards'}
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <Button 
                            href={`/job-cards/new${customer ? `?customer_id=${customer.id}` : ''}`}
                            variant="default" 
                            class="gap-2 bg-blue-600 hover:bg-blue-700 text-white"
                        >
                            <Plus class="h-4 w-4" />
                            New Job Card
                        </Button>
                    </div>
                </div>
                <Separator />
            </div>

            <!-- Filters Card -->
            <Card class="border border-gray-200 shadow-none mb-6">
                <CardContent class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Search</label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <Input
                                    bind:value={searchQuery}
                                    type="text"
                                    placeholder="Search job cards..."
                                    class="pl-10"
                                    onkeypress={(e) => {
                                        if (e.key === 'Enter') applyFilters();
                                    }}
                                />
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Status</label>
                            <select
                                bind:value={selectedStatus}
                                class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                            >
                                <option value="all">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-end gap-2">
                            <Button 
                                onclick={applyFilters}
                                variant="default" 
                                class="w-full md:w-auto gap-2 bg-blue-600 hover:bg-blue-700"
                            >
                                <Filter class="h-4 w-4" />
                                Apply Filters
                            </Button>
                            <Button 
                                onclick={resetFilters}
                                variant="outline" 
                                class="w-full md:w-auto"
                            >
                                Reset
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Results Count -->
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">
                    Showing <span class="font-medium">{pagination.from || 0}</span> to <span class="font-medium">{pagination.to || 0}</span> of{' '}
                    <span class="font-medium">{pagination.total}</span> job cards
                </p>
            </div>

            <!-- Job Cards List -->
            {#if jobCards.length === 0}
                <Card class="border border-gray-200 shadow-none">
                    <CardContent class="p-8 text-center">
                        <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <Briefcase class="h-6 w-6 text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No job cards found</h3>
                        <p class="text-gray-500 mb-6">
                            {filters.search || filters.status !== 'all' 
                                ? 'Try adjusting your filters'
                                : customer
                                    ? 'No job cards have been created for this customer yet.'
                                    : 'No job cards have been created yet.'}
                        </p>
                        <Button 
                            href={`/job-cards/new${customer ? `?customer_id=${customer.id}` : ''}`}
                            variant="default" 
                            class="gap-2 bg-blue-600 hover:bg-blue-700"
                        >
                            <Plus class="h-4 w-4" />
                            Create Your First Job Card
                        </Button>
                    </CardContent>
                </Card>
            {:else}
                <div class="grid grid-cols-1 gap-4">
                    {#each jobCards as jobCard}
                        <Card class="border border-gray-200 shadow-none hover:shadow-sm transition-shadow group">
                            <CardContent class="p-0">
                                <a 
                                    href={`/job-cards/${jobCard.id}`}
                                    class="block p-6 hover:bg-gray-50 transition-colors"
                                >
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <!-- Left Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="text-lg font-semibold text-gray-900 truncate">
                                                    {jobCard.title}
                                                </h3>
                                                <Badge 
                                                    variant="outline" 
                                                    class={statusColors[jobCard.status] || 'bg-gray-100 text-gray-800 border-gray-200'}
                                                >
                                                    <!-- {#let Icon = statusIcons[jobCard.status] || Clock}
                                                        <Icon class="h-3 w-3 mr-1" />
                                                    {/let} -->
                                                    {jobCard.status.replace('-', ' ').replace('_', ' ').toUpperCase()}
                                                </Badge>
                                                {#if jobCard.priority}
                                                    <Badge 
                                                        class={priorityColors[jobCard.priority] || 'bg-gray-100 text-gray-800'}
                                                    >
                                                        {jobCard.priority.toUpperCase()}
                                                    </Badge>
                                                {/if}
                                            </div>
                                            
                                            <p class="text-gray-600 mb-4 line-clamp-2">
                                                {jobCard.description || 'No description provided'}
                                            </p>
                                            
                                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-gray-900">#{jobCard.job_number}</span>
                                                </div>
                                                <span class="text-gray-300 hidden sm:inline">•</span>
                                                <div class="flex items-center gap-1">
                                                    <User class="h-3.5 w-3.5" />
                                                    <span class="truncate">{jobCard.customer.name}</span>
                                                </div>
                                                <span class="text-gray-300 hidden sm:inline">•</span>
                                                <div class="flex items-center gap-1">
                                                    <Calendar class="h-3.5 w-3.5" />
                                                    <span>Created {formatDate(jobCard.created_at)}</span>
                                                </div>
                                                {#if jobCard.due_date}
                                                    <span class="text-gray-300 hidden sm:inline">•</span>
                                                    <div class="flex items-center gap-1">
                                                        <Calendar class="h-3.5 w-3.5" />
                                                        <span>Due {formatDate(jobCard.due_date)}</span>
                                                    </div>
                                                {/if}
                                            </div>
                                        </div>
                                        
                                        <!-- Right Actions -->
                                        <div class="flex items-center gap-2 sm:self-start">
                                            <Button 
                                                href={`/job-cards/${jobCard.id}`}
                                                variant="ghost" 
                                                size="sm"
                                                class="h-8 w-8 p-0 opacity-0 group-hover:opacity-100 sm:opacity-100 transition-opacity"
                                            >
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                            
                                            <DropdownMenu>
                                                <DropdownMenuTrigger>
                                                    <Button 
                                                        variant="ghost" 
                                                        size="sm"
                                                        class="h-8 w-8 p-0"
                                                    >
                                                        <EllipsisVertical class="h-4 w-4" />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end" class="w-48">
                                                    <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                                    <DropdownMenuItem onclick={() => window.location.href = `/job-cards/${jobCard.id}`}>
                                                        <Eye class="h-4 w-4 mr-2" />
                                                        View Details
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem onclick={() => window.location.href = `/job-cards/${jobCard.id}/edit`}>
                                                        <SquarePen class="h-4 w-4 mr-2" />
                                                        Edit
                                                    </DropdownMenuItem>
                                                    <DropdownMenuSeparator />
                                                    <DeleteConfirmDialog
                                                        onConfirm={async () => {
                                                            await deleteJobCard(jobCard.id);
                                                        }}
                                                        itemName={jobCard.title}
                                                        title="Delete Job Card"
                                                        description={`This will permanently delete job card #${jobCard.job_number}. This action cannot be undone.`}
                                                        buttonText="Delete"
                                                        triggerClass="w-full justify-start text-red-600 hover:text-red-700 hover:bg-red-50 px-2 py-1.5 text-sm"
                                                        disabled={isDeletingId === jobCard.id}
                                                    />
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </div>
                                    </div>
                                </a>
                            </CardContent>
                        </Card>
                    {/each}
                </div>

                <!-- Pagination -->
                {#if pagination.links && pagination.links.length > 3}
                    <div class="flex items-center justify-center mt-8">
                        <nav class="flex items-center gap-1">
                            {#each pagination.links as link, index}
                                {#if link.url}
                                    <a
                                        href={link.url}
                                        class={`px-3 py-2 text-sm font-medium rounded-md transition-colors ${
                                            link.active
                                                ? 'bg-blue-600 text-white'
                                                : 'text-gray-700 hover:bg-gray-100'
                                        }`}
                                    >
                                        {link.label.replace('&laquo; Previous', '←').replace('Next &raquo;', '→')}
                                    </a>
                                {:else}
                                    <span class="px-3 py-2 text-sm text-gray-400">
                                        {link.label}
                                    </span>
                                {/if}
                            {/each}
                        </nav>
                    </div>
                {/if}
            {/if}
        </div>
    </div>
</AppLayout>