<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Separator } from '@/components/ui/separator';
    import {
        ArrowLeft,
        Edit,
        Printer,
        Copy,
        Package,
        DollarSign,
        Percent,
        Hash,
        Barcode,
        Calendar,
        CheckCircle,
        XCircle,
        AlertCircle,
        ExternalLink,
        ChevronRight,

        Coins

    } from 'lucide-svelte';
    import { type BreadcrumbItem } from '@/types';
    import { Link, router } from '@inertiajs/svelte';
    import DeleteConfirmDialog from '@/components/confirm/DeleteConfirmDialog.svelte';

    interface Product {
        id: number;
        name: string;
        sku?: string;
        description?: string;
        price: string | number;
        tax: string | number;
        quantity: string | number;
        barcode?: string;
        status: string | boolean;
        image?: string;
        created_at: string;
        updated_at: string;
    }

    let { product }: { product: Product } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
        {
            title: 'Products',
            href: '/products',
        },
        {
            title: product.name,
            href: `/products/${product.id}`,
        },
    ];

    // Formatting functions
    function formatCurrency(amount: number | string): string {
        const num = typeof amount === 'string' ? parseFloat(amount) : Number(amount);
        if (isNaN(num)) return '$0.00';
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(num);
    }

    function formatDate(dateString: string): string {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        } catch {
            return 'Invalid date';
        }
    }

    // Calculate price with tax
    const priceWithTax = $derived.by(() => {
        const price = Number(product.price) || 0;
        const tax = Number(product.tax) || 0;
        return price + (price * tax) / 100;
    });

    // Stock status
    const stockStatus = $derived.by(() => {
        const quantity = Number(product.quantity) || 0;
        if (quantity <= 0) return { text: 'Out of Stock', variant: 'destructive' as const, icon: XCircle };
        if (quantity <= 10) return { text: 'Low Stock', variant: 'outline' as const, icon: AlertCircle };
        return { text: 'In Stock', variant: 'secondary' as const, icon: CheckCircle };
    });

    // Product status
   type Status = {
        text: string;
        variant: 'secondary' | 'destructive';
        icon: typeof CheckCircle | typeof XCircle;
    };

    const productStatus = $derived.by(() => {
        const isActive = product.status === '1' || product.status === true;

        return isActive
            ? { text: 'Active', variant: 'secondary', icon: CheckCircle }
            : { text: 'Inactive', variant: 'destructive', icon: XCircle };
    });

    const StatusIcon = $derived( productStatus.icon);

    // Copy to clipboard
    async function copyToClipboard(text: string) {
        try {
            await navigator.clipboard.writeText(text);
            // You can add toast notification here if needed
            console.log('Copied to clipboard:', text);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    }

    // Print function
    function printProduct() {
        window.print();
    }
</script>

<AppLayout {breadcrumbs}>
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
        <!-- Header -->
        <div class="bg-white border-b">
            <div class="p-6 max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <Button
                                onclick={() => history.back()}
                                variant="ghost"
                                size="sm"
                                class="gap-2"
                            >
                                <ArrowLeft class="h-4 w-4" />
                                Back
                            </Button>
                            
                        </div>
                        
                        <div class="space-y-2">
                            {#if product.sku}
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-gray-500 px-3 py-1 bg-gray-100 rounded-full">
                                    SKU: {product.sku}
                                </span>
                            </div>
                            {/if}
                            <p class="text-md text-gray-600 flex items-center gap-2">
                                <Package class="h-5 w-5 text-gray-400" />
                                Product ID: #{product.id.toString().padStart(5, '0')}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Button
                            href={`/products/${product.id}/edit`}
                            class="gap-2 bg-blue-600 hover:bg-blue-700"
                        >
                            <Edit class="h-4 w-4" />
                            Edit Product
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Product Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Product Information Card -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader class="bg-gradient-to-r from-blue-50 to-blue-100/50">
                            <CardTitle class="flex items-center gap-3 pt-2">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <Package class="h-5 w-5 text-blue-600" />
                                </div>
                                <span>Product Information</span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="p-6 space-y-6">
                            <!-- Basic Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">Product Name</p>
                                    <p class="text-xl font-semibold text-gray-900">{product.name}</p>
                                </div>
                                {#if product.sku}
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 mb-1">SKU</p>
                                        <p class="text-xl font-semibold text-gray-900">{product.sku}</p>
                                    </div>
                                {/if}
                            </div>

                            <!-- Description -->
                            {#if product.description && product.description.trim() !== ''}
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-2">Description</p>
                                    <div class="bg-gray-50 rounded-lg p-4 border">
                                        <p class="text-gray-800 whitespace-pre-line">{product.description}</p>
                                    </div>
                                </div>
                            {/if}

                            <!-- Timeline -->
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-4">Timeline</p>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-medium text-gray-900">Created</span>
                                                <span class="text-sm text-gray-500">{formatDate(product.created_at)}</span>
                                            </div>
                                            <div class="h-0.25 bg-gray-200 rounded-full"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-medium text-gray-900">Last Updated</span>
                                                <span class="text-sm text-gray-500">{formatDate(product.updated_at)}</span>
                                            </div>
                                            <div class="h-0.25 bg-gray-200 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Pricing & Inventory Details -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader class="bg-gradient-to-r from-green-50 to-green-100/50">
                            <CardTitle class="pt-2 flex items-center gap-3">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <Coins class="h-5 w-5 text-green-600" />
                                </div>
                                <span>Pricing & Inventory</span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Pricing Section -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                        <DollarSign class="h-5 w-5 text-green-600" />
                                        Pricing Details
                                    </h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Base Price</span>
                                            <span class="font-medium text-gray-900">{formatCurrency(product.price)}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 flex items-center gap-2">
                                                <Percent class="h-4 w-4" />
                                                Tax Rate
                                            </span>
                                            <span class="font-medium text-gray-900">{product.tax}%</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Tax Amount</span>
                                            <span class="font-medium text-gray-900">
                                                {formatCurrency((Number(product.price) * Number(product.tax)) / 100)}
                                            </span>
                                        </div>
                                        <Separator />
                                        <div class="flex justify-between items-center pt-2">
                                            <span class="text-lg font-semibold text-gray-900">Final Price</span>
                                            <span class="text-2xl font-bold text-blue-600">
                                                {formatCurrency(priceWithTax)}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventory Section -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                        <Package class="h-5 w-5 text-blue-600" />
                                        Inventory Details
                                    </h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 flex items-center gap-2">
                                                <Hash class="h-4 w-4" />
                                                Current Stock
                                            </span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-2xl font-bold text-gray-900">{product.quantity}</span>
                                                <Badge variant={stockStatus.variant} class="gap-2">
                                                    {stockStatus.text}
                                                </Badge>
                                            </div>
                                        </div>
                                        
                                        {#if product.barcode && product.barcode.toString().trim() !== ''}
                                            <div>
                                                <p class="text-sm text-gray-600 mb-2 flex items-center gap-2">
                                                    <Barcode class="h-4 w-4" />
                                                    Barcode
                                                </p>
                                                <div class="bg-gray-100 p-3 rounded-lg">
                                                    <div class="flex items-center justify-between">
                                                        <code class="font-mono text-lg font-semibold">{product.barcode}</code>
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            onclick={() => copyToClipboard(product.barcode!.toString())}
                                                            class="h-8 w-8 p-0"
                                                        >
                                                            <Copy class="h-4 w-4" />
                                                        </Button>
                                                    </div>
                                                </div>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Product Image Section -->
                    {#if product.image && product.image.trim() !== ''}
                        <Card class="border border-gray-200 shadow-sm">
                            <CardHeader class="bg-gradient-to-r from-purple-50 to-purple-100/50">
                                <CardTitle class="flex items-center gap-3">
                                    <div class="p-2 bg-purple-100 rounded-lg">
                                        <Package class="h-5 w-5 text-purple-600" />
                                    </div>
                                    <span>Product Image</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="p-6">
                                <div class="flex justify-center">
                                    <div class="relative max-w-md w-full">
                                        <img 
                                            src={product.image} 
                                            alt={product.name}
                                            class="w-full h-auto max-h-96 object-contain rounded-lg border shadow-sm"
                                            loading="lazy"
                                        />
                                        <div class="absolute top-4 right-4 flex gap-2">
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                class="bg-white/90 backdrop-blur-sm"
                                                onclick={() => window.open(product.image!, '_blank')}
                                            >
                                                <ExternalLink class="h-4 w-4 mr-2" />
                                                Open Full Size
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    {/if}
                </div>

                <!-- Right Column - Summary & Actions -->
                <div class="space-y-8">
                    <!-- Quick Stats Card -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader>
                            <CardTitle>Product Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <!-- <Badge variant={productStatus.variant} class="gap-2">
                                        <StatusIcon class="h-3 w-3" />
                                        {productStatus.text}
                                    </Badge> -->
                                </div>
                                <Separator />
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Stock Status</span>
                                    <!-- <Badge variant={stockStatus.variant} class="gap-2">
                                        <StockIcon class="h-3 w-3" />
                                        {stockStatus.text}
                                    </Badge> -->
                                </div>
                                <Separator />
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Current Price</span>
                                    <span class="font-medium text-gray-900">
                                        {formatCurrency(priceWithTax)}
                                    </span>
                                </div>
                                <Separator />
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Available Stock</span>
                                    <span class="font-medium text-gray-900">
                                        {product.quantity} units
                                    </span>
                                </div>
                                <Separator />
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Created</span>
                                    <span class="font-medium text-gray-900">
                                        {formatDate(product.created_at)}
                                    </span>
                                </div>
                                <Separator />
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Last Updated</span>
                                    <span class="font-medium text-gray-900">
                                        {formatDate(product.updated_at)}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Actions Card -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader>
                            <CardTitle>Actions</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <Button
                                href={`/products/${product.id}/edit`}
                                class="w-full gap-3 justify-start bg-blue-600 hover:bg-blue-700"
                            >
                                <Edit class="h-4 w-4" />
                                Edit Product
                            </Button>
                            
                            <DeleteConfirmDialog
                                onConfirm={async () => router.delete(
                                    route('products.destroy', product.id), {
                                        preserveScroll: true,
                                        preserveState: true
                                    })
                                }
                                itemName={product.name}
                                title="Delete Product"
                                description={`This will permanently delete <b>${product.name}</b>. This action cannot be undone.`}
                                buttonText="Delete"
                                buttonVariant='outline'
                            />
                        </CardContent>
                    </Card>

                    <!-- Quick Links -->
                    <Card class="border border-gray-200 shadow-sm">
                        <CardHeader>
                            <CardTitle>Quick Links</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2">
                            <Button
                                variant="ghost"
                                class="w-full justify-between"
                                href="/products"
                            >
                                <div class="flex items-center gap-3">
                                    <Package class="h-4 w-4" />
                                    <span>All Products</span>
                                </div>
                                <ChevronRight class="h-4 w-4" />
                            </Button>
                            <Button
                                variant="ghost"
                                class="w-full justify-between"
                                href="/products/create"
                            >
                                <div class="flex items-center gap-3">
                                    <Package class="h-4 w-4" />
                                    <span>Create New Product</span>
                                </div>
                                <ChevronRight class="h-4 w-4" />
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</AppLayout>

 