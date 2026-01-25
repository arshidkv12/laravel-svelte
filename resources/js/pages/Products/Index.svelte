<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type Flash, type BreadcrumbItem } from '@/types';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
    import { Badge } from '@/components/ui/badge';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
    import { Select, SelectContent, SelectItem, SelectTrigger } from '@/components/ui/select';
    import { Search, Plus, Edit, Eye, MoreVertical, Filter, Download } from 'lucide-svelte';
    import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
    import { type Pagination, type Product } from '@/types/products';
    import { Link } from '@inertiajs/svelte';
    import PaginationUi from '@/components/general/Pagination.svelte';

    export let filters: { search?: string; status?: string };
    export let products: {
        data: Product[];
        links: Pagination['links'];
        current_page: number;
        last_page: number;
        total: number;
        from: number;
        to: number;
    };

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

    let searchValue = filters.search || '';
    let statusValue = filters.status || 'all';

    const statusOptions = [
        { value: 'all', label: 'All Status' },
        { value: 'active', label: 'Active', variant: 'success' },
        { value: 'inactive', label: 'Inactive', variant: 'secondary' },
        { value: 'draft', label: 'Draft', variant: 'outline' },
    ];

    const getStatusVariant = (status: string) => {
        switch (status) {
            case 'active': return 'success';
            case 'inactive': return 'secondary';
            case 'draft': return 'outline';
            default: return 'secondary';
        }
    };

    // const handleSearch = debounce((value: string) => {
    //     filters.search = value;
    //     updateFilters();
    // }, 300);

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
                <Button class="gap-2">
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
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4" />
                        <Input
                            type="text"
                            placeholder="Search products by name..."
                            bind:value={searchValue}
                            class="pl-9"
                        />
                    </div>
                    
                    <!-- <Select value={statusValue} on:change={(e) => {
                        statusValue = e.detail;
                        handleStatusChange(e.detail);
                    }}>
                        <SelectTrigger class="w-full sm:w-[180px]">
                            <SelectValue placeholder="Status" />
                        </SelectTrigger>
                        <SelectContent>
                            {#each statusOptions as option}
                                <SelectItem value={option.value}>
                                    {option.label}
                                </SelectItem>
                            {/each}
                        </SelectContent>
                    </Select> -->

                    <Button variant="outline" onclick={resetFilters} class="gap-2">
                        <Filter class="h-4 w-4" />
                        Clear Filters
                    </Button>
                </div>
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
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-12">ID</TableHead>
                                <TableHead>Product Name</TableHead>
                                <TableHead>SKU</TableHead>
                                <TableHead>Price</TableHead>
                                <TableHead>Stock</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Created</TableHead>
                                <TableHead class="w-20">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {#each products.data as product (product.id)}
                                <TableRow class="hover:bg-muted/50">
                                    <TableCell class="font-medium">{product.id}</TableCell>
                                    <TableCell>
                                        <div class="flex items-center gap-3">
                                            {#if product.image}
                                                <img 
                                                    src={product.image} 
                                                    alt={product.name}
                                                    class="w-10 h-10 rounded-md object-cover"
                                                />
                                            {:else}
                                                <div class="w-10 h-10 rounded-md bg-muted flex items-center justify-center">
                                                    <span class="text-xs text-muted-foreground">No image</span>
                                                </div>
                                            {/if}
                                            <div>
                                                <p class="font-medium">{product.name}</p>
                                                <p class="text-xs text-muted-foreground line-clamp-1">{product.description}</p>
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <code class="relative rounded bg-muted px-[0.3rem] py-[0.2rem] font-mono text-sm">
                                            {product.sku || 'N/A'}
                                        </code>
                                    </TableCell>
                                    <TableCell class="font-medium">
                                        ${product.price || '0.00'}
                                    </TableCell>
                                    <TableCell>
                                        <span class:low-stock={product.stock < 10} class:text-red-500={product.stock === 0}>
                                            {product.stock}
                                        </span>
                                        {#if product.stock < 10 && product.stock > 0}
                                            <span class="text-xs text-amber-600 ml-1">(Low)</span>
                                        {:else if product.stock === 0}
                                            <span class="text-xs text-red-600 ml-1">(Out)</span>
                                        {/if}
                                    </TableCell>
                                    <TableCell>
                                        <Badge >
                                            {product.status || 'draft'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-muted-foreground">
                                        {new Date(product.created_at).toLocaleDateString()}
                                    </TableCell>
                                    <TableCell>
                                        <DropdownMenu>
                                            <DropdownMenuTrigger >
                                                <Button variant="ghost" size="sm">
                                                    <MoreVertical class="h-4 w-4" />
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
                                                    <DropdownMenuItem>
                                                        <Edit class="mr-2 h-4 w-4" />
                                                        Edit
                                                    </DropdownMenuItem>
                                                </Link>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                                </TableRow>
                            {:else}
                                <TableRow>
                                    <TableCell   class="text-center py-8">
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="w-12 h-12 rounded-full bg-muted flex items-center justify-center">
                                                <Search class="h-6 w-6 text-muted-foreground" />
                                            </div>
                                            <div>
                                                <p class="font-medium">No products found</p>
                                                <p class="text-muted-foreground text-sm">
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

                <!-- Pagination -->
                <PaginationUi links={products.links}  
                    currentPage={products.current_page} 
                    lastPage={products.last_page}
                />

            </CardContent>
        </Card>
    </div>
</AppLayout>