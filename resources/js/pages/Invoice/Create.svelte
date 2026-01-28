<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Textarea } from '@/components/ui/textarea';
    import * as  Select from '@/components/ui/select';
    import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
    import { Plus, Trash2, Calendar, Save, User, FileText } from 'lucide-svelte';
    import { format } from 'date-fns';
    import CustomerSelect from '@/components/customer/CustomerSelect.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Form } from '@inertiajs/svelte';
    import { type BaseFormSnippetProps } from '@/types/forms';
    import ProductSelect from '@/components/general/ProductSelect.svelte';
    import { type Product } from '@/types/products';
    import _, { uniqueId } from 'lodash';
    
    let { customers, csrf_token, initCustomerId } = $props();
    let customer_id = $derived(initCustomerId);

    const breadcrumbs = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Invoices', href: '/invoices' },
        { title: 'Create Invoice', href: '/invoices/create' },
    ];

    let invoice = $state({
        client_id: '',
        invoice_number: '',
        issue_date: format(new Date(), 'yyyy-MM-dd'),
        due_date: format(new Date(Date.now() + 30 * 24 * 60 * 60 * 1000), 'yyyy-MM-dd'),
        status: 'draft',
        notes: ''
    });

    let status = $state('draft');

    let items = $state([
        { id: uniqueId('p-'), name: '', quantity: 1, unit_price: 0, total: 0, tax: 0}
    ]);

    function calculateTotal(quantity: number, unit_price:number, tax: number = 1){
        const total = quantity * unit_price;
        const taxAmount = total * (tax / 100);
        return total + taxAmount;
    }

    function updateItemTotal(quantity: number, unit_price: number, tax: number) {
        const subtotal = quantity * unit_price;
        const tax_amount = subtotal * (tax / 100);
        return subtotal + tax_amount;
    }
</script>

<AppLayout {breadcrumbs}>
    <div class="container mx-auto p-4">
        <Form
            method="post" 
            action={route('job-cards.store')} 
            class="space-y-6">
            {#snippet children({ errors, processing }: BaseFormSnippetProps)}
        
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Create Invoice</h1>
                <p class="text-gray-600">Fill in the details below to create a new invoice</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Invoice Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Client Information Card -->
                    <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                        <User class="h-5 w-5" />
                        Client Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label class="text-sm font-medium">Select Customer *</Label>
                            <CustomerSelect initCustomers={customers}  bind:modelValue={customer_id} />
                            <InputError class="mt-1" message={errors.customer_id} />
                        </div>

                        <div class="space-y-2">
                            <Label for="invoice_number">Invoice Number *</Label>
                            <Input
                            id="invoice_number"
                            bind:value={invoice.invoice_number}
                            placeholder="e.g., INV-2024-001"
                            />
                        </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="issue_date">Issue Date *</Label>
                            <div class="flex items-center gap-2">
                            <Calendar class="h-4 w-4 text-gray-500" />
                            <Input
                                id="issue_date"
                                type="date"
                                bind:value={invoice.issue_date}
                            />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="due_date">Due Date *</Label>
                            <div class="flex items-center gap-2">
                            <Calendar class="h-4 w-4 text-gray-500" />
                            <Input
                                id="due_date"
                                type="date"
                                bind:value={invoice.due_date}
                            />
                            </div>
                        </div>
                        </div>
                    </CardContent>
                    </Card>

                    <!-- Invoice Items Card -->
                    <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle class="flex items-center gap-2">
                            <FileText class="h-5 w-5" />
                            Invoice Items
                        </CardTitle>
                        <Button type="button" size="sm" onclick={() => {
                            items = [...items, { id: uniqueId('p-'), name: '', quantity: 1, unit_price: 0, tax: 0, total: 0 }];
                        }}>
                        <Plus class="h-4 w-4 mr-2" />
                        Add Item
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <ProductSelect onSelect={(product:Product)=>{
                            const existingIndex = items.findIndex(item => item.id === String(product.id));
                            if (existingIndex >= 0) {
                                items[existingIndex].quantity += 1;
                                items[existingIndex].total = calculateTotal(
                                    items[existingIndex].quantity, 
                                    items[existingIndex].unit_price, 
                                    items[existingIndex].tax
                                );
                            } else {
                                items = [...items, { 
                                    id: String(product.id), 
                                    name: product.name, 
                                    quantity: 1, 
                                    unit_price: product.price, 
                                    tax: product.tax, 
                                    total: calculateTotal(1, product.price, product.tax)
                                }];
                            }
                        }} />
                        <Table>
                        <TableHeader>
                            <TableRow>
                            <TableHead>Description</TableHead>
                            <TableHead class="w-24">Quantity</TableHead>
                            <TableHead class="w-32">Unit Price</TableHead>
                            <TableHead class="w-32">Tax %</TableHead>
                            <TableHead class="w-32">Total</TableHead>
                            <TableHead class="w-12"></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {#each items as item (item.id)}
                            <TableRow>
                                <TableCell>
                                <Input
                                    bind:value={item.name}
                                    placeholder="Item description"
                                />
                                </TableCell>
                                <TableCell>
                                <Input
                                    type="number"
                                    min="1"
                                    bind:value={item.quantity}
                                    oninput={(e) => {
                                        item.total = updateItemTotal(item.quantity, item.unit_price, item.tax);
                                    }}
                                />
                                </TableCell>
                                <TableCell>
                                <Input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    bind:value={item.unit_price}
                                    oninput={(e) => {
                                        item.total = updateItemTotal(item.quantity, item.unit_price, item.tax);
                                    }}
                                />
                                </TableCell>
                                <TableCell>
                                    <Input
                                        type="number"
                                        min="1"
                                        bind:value={item.tax}
                                        oninput={(e) => {
                                            item.total = updateItemTotal(item.quantity, item.unit_price, item.tax);
                                        }}
                                    />
                                </TableCell>
                                <TableCell class="font-medium">
                                {Number(item.total).toFixed(2)}
                                </TableCell>
                                <TableCell>
                                    <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    onclick={() => {
                                        items = items.filter(i => i.id !== item.id);
                                    }}
                                    >
                                    <Trash2 class="h-4 w-4 text-red-500" />
                                    </Button>
                                </TableCell>
                            </TableRow>
                            {/each}
                        </TableBody>
                        </Table>
                    </CardContent>
                    </Card>

                    <!-- Notes Card -->
                    <Card>
                    <CardHeader>
                        <CardTitle>Notes</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Textarea
                        bind:value={invoice.notes}
                        placeholder="Additional notes or terms..."
                        rows={3}
                        />
                    </CardContent>
                    </Card>
                </div>

                <!-- Right Column - Summary -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <Card>
                    <CardHeader>
                        <CardTitle>Invoice Status</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Select.Root type="single" bind:value={status}>
                            <Select.Trigger>
                                Select status
                            </Select.Trigger>
                            <Select.Content>
                                <Select.Item value="draft">Draft</Select.Item>
                                <Select.Item value="sent">Sent</Select.Item>
                                <Select.Item value="paid">Paid</Select.Item>
                                <Select.Item value="cancelled">Cancelled</Select.Item>
                            </Select.Content>
                            </Select.Root>
                    </CardContent>
                    </Card>

                    <!-- Summary Card -->
                    <Card>
                    <CardHeader>
                        <CardTitle>Summary</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">
                            {Number(items.reduce((sum, item) => sum + (item.unit_price * item.quantity ), 0)).toFixed(2)}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">GST</span>
                            <span class="font-medium">
                            {Number(items.reduce((sum, item) => sum + (item.tax/100 * item.unit_price * item.quantity ), 0)).toFixed(2)}
                            </span>
                        </div>
                        <div class="flex justify-between pt-2 border-t">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-lg font-bold">
                            {Number(items.reduce((sum, item) => sum + item.total, 0)).toFixed(2)}
                            </span>
                        </div>
                        </div>
                    </CardContent>
                    </Card>

                    <!-- Actions Card -->
                    <Card>
                    <CardContent class="pt-6">
                        <div class="space-y-3">
                        <Button class="w-full" size="lg" onclick={() => {
                            console.log('Save invoice:', { invoice, items });
                        }}>
                            <Save class="h-4 w-4 mr-2" />
                            Save Invoice
                        </Button>
                        <Button class="w-full" variant="outline" size="lg">
                            Save as Draft
                        </Button>
                        </div>
                    </CardContent>
                    </Card>
                </div>
            </div>
            {/snippet}
        </Form>
  </div>
</AppLayout>