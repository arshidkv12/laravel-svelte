<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Badge } from '@/components/ui/badge';
    import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
    import { Plus, Search, Eye, Edit, Trash2, FileText, Download, Send, Filter } from 'lucide-svelte';
    import { format } from 'date-fns';
    import {  Link } from '@inertiajs/svelte';
    import { onMount } from 'svelte';

   let { invoices, filters, csrf_token } = $props();
    
    let searchTerm = $state('');
    let statusFilter = $state('');
    let fromDate = $state('');
    let toDate = $state('');
    
    const breadcrumbs = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Invoices', href: '/invoices' },
    ];

    const statusColors = {
        draft: 'bg-gray-100 text-gray-800',
        sent: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };

    const statusLabels = {
        draft: 'Draft',
        sent: 'Sent',
        paid: 'Paid',
        cancelled: 'Cancelled'
    };

    function formatCurrency(amount: number): string {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    function formatDate(dateString: string): string {
        return format(new Date(dateString), 'MMM dd, yyyy');
    }

    function getStatusClass(status: string): string {
        return statusColors[status as keyof typeof statusColors] || 'bg-gray-100 text-gray-800';
    }

    function handleSearch() {
        const params = new URLSearchParams();
        if (searchTerm) params.set('search', searchTerm);
        if (statusFilter) params.set('status', statusFilter);
        if (fromDate) params.set('from_date', fromDate);
        if (toDate) params.set('to_date', toDate);
        
        window.location.href = `/invoices?${params.toString()}`;
    }

    function clearFilters() {
        searchTerm = '';
        statusFilter = '';
        fromDate = '';
        toDate = '';
        window.location.href = '/invoices';
    }

    onMount(()=>{
        console.log(invoices)
    })
</script>

<AppLayout {breadcrumbs}>
    <div class="container mx-auto p-4">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Invoices</h1>
                    <p class="text-gray-600">Manage your invoices and billing</p>
                </div>
                <Link href="/invoices/create">
                    <Button>
                        <Plus class="h-4 w-4 mr-2" />
                        Create Invoice
                    </Button>
                </Link>
            </div>
        </div>

        <!-- Filters Card -->
        <Card class="mb-6">
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <Filter class="h-5 w-5" />
                    Filters
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Search</label>
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                            <Input
                                type="text"
                                placeholder="Search invoices..."
                                bind:value={searchTerm}
                                class="pl-10"
                                onkeyup={(e) => e.key === 'Enter' && handleSearch()}
                            />
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Status</label>
                        <select
                            bind:value={statusFilter}
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-sm font-medium">From Date</label>
                        <Input
                            type="date"
                            bind:value={fromDate}
                        />
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-sm font-medium">To Date</label>
                        <Input
                            type="date"
                            bind:value={toDate}
                        />
                    </div>
                </div>
                
                <div class="flex justify-end gap-2 mt-4">
                    <Button variant="outline" onclick={clearFilters}>
                        Clear
                    </Button>
                    <Button onclick={handleSearch}>
                        <Search class="h-4 w-4 mr-2" />
                        Apply Filters
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Invoices Table -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle>Invoice List</CardTitle>
                <div class="text-sm text-gray-600">
                    {invoices.length} {invoices.length === 1 ? 'invoice' : 'invoices'} found
                </div>
            </CardHeader>
            <CardContent>
                {#if invoices.length === 0}
                    <div class="text-center py-12">
                        <FileText class="h-12 w-12 mx-auto text-gray-400" />
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No invoices found</h3>
                        <p class="mt-2 text-gray-600">Get started by creating a new invoice.</p>
                        <Link href="/invoices/create" class="mt-4 inline-block">
                            <Button>
                                <Plus class="h-4 w-4 mr-2" />
                                Create Invoice
                            </Button>
                        </Link>
                    </div>
                {:else}
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Invoice #</TableHead>
                                    <TableHead>Customer</TableHead>
                                    <TableHead>Due Date</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Total</TableHead>
                                    <TableHead>Created</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {#each invoices as invoice (invoice.id)}
                                    <TableRow class="hover:bg-gray-50">
                                        <TableCell class="font-medium">
                                            <Link href={`/invoices/${invoice.id}`} class="text-blue-600 hover:text-blue-800 hover:underline">
                                                {invoice.invoice_number}
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            <div class="font-medium">{invoice.customer.name}</div>
                                            <div class="text-sm text-gray-600">{invoice.customer.email}</div>
                                        </TableCell>
                                        <TableCell>
                                            {formatDate(invoice.due_date)}
                                        </TableCell>
                                        <TableCell>
                                            <Badge class={getStatusClass(invoice.status)}>
                                                {statusLabels[invoice.status as keyof typeof statusLabels]}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="font-semibold">
                                            {formatCurrency(invoice.total_amount)}
                                        </TableCell>
                                        <TableCell class="text-gray-600">
                                            {formatDate(invoice.created_at)}
                                        </TableCell>
                                        <TableCell>
                                            <div class="flex items-center justify-end gap-2">
                                                <Link href={`/invoices/${invoice.id}`}>
                                                    <Button variant="ghost" size="icon" title="View">
                                                        <Eye class="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                                <Link href={`/invoices/${invoice.id}/edit`}>
                                                    <Button variant="ghost" size="icon" title="Edit">
                                                        <Edit class="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    title="Download"
                                                    class="text-gray-600 hover:text-blue-600"
                                                >
                                                    <Download class="h-4 w-4" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    title="Send"
                                                    class="text-gray-600 hover:text-green-600"
                                                >
                                                    <Send class="h-4 w-4" />
                                                </Button>
                                                <form method="POST" action={`/invoices/${invoice.id}`} style="display: inline;">
                                                    <input type="hidden" name="_method" value="DELETE" />
                                                    <Button
                                                        type="submit"
                                                        variant="ghost"
                                                        size="icon"
                                                        title="Delete"
                                                        class="text-gray-600 hover:text-red-600"
                                                    >
                                                        <Trash2 class="h-4 w-4" />
                                                    </Button>
                                                </form>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                {/each}
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Summary -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                Showing {invoices.length} invoices
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-lg font-semibold">
                                    Total: 
                                </div>
                                <Badge class="bg-green-100 text-green-800">
                                    Paid:  
                                </Badge>
                                <Badge class="bg-blue-100 text-blue-800">
                                    Pending: 
                                </Badge>
                            </div>
                        </div>
                    </div>
                {/if}
            </CardContent>
        </Card>
    </div>
</AppLayout>