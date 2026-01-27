<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type Flash, type BreadcrumbItem } from '@/types';
    import { Button, buttonVariants } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
    import { Badge } from '@/components/ui/badge';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
    import { Select, SelectContent, SelectItem, SelectTrigger } from '@/components/ui/select';
    import { Search, Plus, SquarePen, Eye, EllipsisVertical, Funnel, Download } from 'lucide-svelte';
    import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
    import { type Pagination, type Product } from '@/types/products';
    import { Link, router } from '@inertiajs/svelte';
    import PaginationUi from '@/components/general/Pagination.svelte';
    import DeleteConfirmDialog from '@/components/confirm/DeleteConfirmDialog.svelte';
    import Filter from '@/components/general/Filter.svelte';

    let { products, filters, statusOptions } = $props();


    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
        {
            title: 'Products',
            href: '/products',
        },
    ];

    const getStatusVariant = (status: string) => {
        switch (status) {
            case 'active': return 'success';
            case 'inactive': return 'secondary';
            case 'draft': return 'outline';
            default: return 'secondary';
        }
    };


    const handleStatusChange = (value: string) => {
        filters.status = value === 'all' ? undefined : value;
        updateFilters();
    };

    const updateFilters = () => {
        const params = new URLSearchParams();
        if (filters.search) params.set('search', filters.search);
        if (filters.status) params.set('status', filters.status);
        
        window.location.href = `/products?${params.toString()}`;
        // Or using Inertia.get() if you prefer SPA navigation
        // Inertia.get(`/products?${params.toString()}`);
    };

    const resetFilters = () => {
        filters = {};
        window.location.href = '/products';
    };

    const exportProducts = () => {
        // Handle export logic
        console.log('Exporting products...');
    };
</script>

<AppLayout {breadcrumbs}>
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="p-4 md:p-6 md:pb-0 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Products</h1>
                <p class="text-muted-foreground mt-1">
                    Manage your products and inventory
                </p>
            </div>
            <Link href="/products/create">
                <Button class="gap-2 cursor-pointer">
                    <Plus class="h-4 w-4" />
                    Add Product
                </Button>
            </Link>
        </div>

        <!-- Filters Card -->
        <Card class="shadow-none border-none py-0">
            <!-- <CardHeader>
                <CardTitle>Filters</CardTitle>
                <CardDescription>
                    Filter and search through your products
                </CardDescription>
            </CardHeader> -->
            <CardContent>
                <!-- <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4" />
                        <Input
                            type="text"
                            placeholder="Search products by name..."
                            bind:value={searchValue}
                            class="pl-9"
                        />
                    </div>

                    <Button variant="outline" onclick={resetFilters} class="gap-2">
                        <Funnel class="h-4 w-4" />
                        Clear Filters
                    </Button>
                </div> -->
                <Filter {filters} {statusOptions} />
            </CardContent>
        </Card>

        <!-- Products Table -->
        <Card class="shadow-none border-none">
            <CardHeader class="flex flex-row items-center justify-between">
                <div>
                    <CardTitle>Product List</CardTitle>
                    <CardDescription>
                        Showing {products.from} to {products.to} of {products.total} products
                    </CardDescription>
                </div>
                <Button variant="outline" size="sm" onclick={exportProducts} class="gap-2">
                    <Download class="h-4 w-4" />
                    Export
                </Button>
            </CardHeader>
            <CardContent>
                <div class="rounded-md border overflow-hidden">
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow class="bg-gray-50">
                                    <TableHead class="pl-4 min-w-[120px]">Created</TableHead>
                                    <TableHead class="min-w-[180px]">Product Name</TableHead>
                                    <TableHead class="min-w-[100px]">Price</TableHead>
                                    <TableHead class="text-center min-w-[80px]">Stock</TableHead>
                                    <TableHead class="min-w-[100px]">Status</TableHead>
                                    <TableHead class="w-20 text-center">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {#each products.data as product (product.id)}
                                    <TableRow class="hover:bg-muted/50">
                                        <TableCell class="pl-4">
                                            <Link href={`/products/${product.id}`}>
                                                <div class="text-muted-foreground">
                                                    {product.created_at_formatted}
                                                </div>
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            <Link href={`/products/${product.id}`}>
                                                <div class="max-w-[250px] whitespace-normal">
                                                    <p class="font-medium line-clamp-2" title={product.name}>
                                                        {product.name}
                                                    </p>
                                                </div>
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            <Link href={`/products/${product.id}`}>
                                                <div class="font-medium">
                                                    <span class="md:hidden text-sm">Price: </span>
                                                    {product.price || '-'}
                                                </div>
                                            </Link>
                                        </TableCell>
                                        <TableCell class="text-center">
                                            <Link href={`/products/${product.id}`}>
                                                <div>
                                                    <span class="md:hidden text-sm">Stock: </span>
                                                    {product.quantity}
                                                </div>
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            <Link href={`/products/${product.id}`}>
                                                {product.status ? 'Active' : 'Inactive'}
                                            </Link>
                                        </TableCell>
                                        <TableCell class="text-center">
                                            <DropdownMenu>
                                                <DropdownMenuTrigger >
                                                    <Button variant="ghost" size="sm">
                                                        <EllipsisVertical class="h-4 w-4" />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <Link href={`/products/${product.id}`}>
                                                        <DropdownMenuItem>
                                                            <Eye class="mr-2 h-4 w-4" />
                                                            View
                                                        </DropdownMenuItem>
                                                    </Link>
                                                    <Link href={`/products/${product.id}/edit`}>
                                                        <DropdownMenuItem class="mb-1">
                                                            <SquarePen class="mr-2 h-4 w-4" />
                                                            Edit
                                                        </DropdownMenuItem>
                                                    </Link>
                                                    <DeleteConfirmDialog
                                                        onConfirm={async () => router.delete(
                                                            route('products.destroy', product.id), {
                                                                preserveScroll: true,
                                                                preserveState: true
                                                            })
                                                        }
                                                        itemName={product.name}
                                                        btnSize={'default'}
                                                        title="Delete Product"
                                                        description={`This will permanently delete <b>${product.name}</b>. This action cannot be undone.`}
                                                        buttonText="Delete"
                                                        buttonVariant='outline'
                                                    />
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </TableCell>
                                    </TableRow>
                                {:else}
                                    <TableRow>
                                        <TableCell colspan={6} class="text-center py-8 px-4">
                                            <div class="flex flex-col items-center gap-3 max-w-md mx-auto">
                                                <div class="w-12 h-12 rounded-full bg-muted flex items-center justify-center">
                                                    <Search class="h-6 w-6 text-muted-foreground" />
                                                </div>
                                                <div class="text-center">
                                                    <p class="font-medium">No products found</p>
                                                    <p class="text-muted-foreground text-sm mt-1">
                                                        {filters.search ? 'Try adjusting your search or filters' : 'Get started by adding your first product'}
                                                    </p>
                                                </div>
                                                {#if filters.search}
                                                    <Button variant="outline" size="sm" onclick={resetFilters}>
                                                        Clear search
                                                    </Button>
                                                {:else}
                                                    <Link href="/products/create">
                                                        <Button size="sm">
                                                            <Plus class="mr-2 h-4 w-4" />
                                                            Add Product
                                                        </Button>
                                                    </Link>
                                                {/if}
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                {/each}
                            </TableBody>
                        </Table>
                    </div>
                </div>

                <!-- Pagination -->
                <PaginationUi links={products.links}  
                    currentPage={products.current_page} 
                    lastPage={products.last_page}
                />

            </CardContent>
        </Card>
    </div>
</AppLayout>