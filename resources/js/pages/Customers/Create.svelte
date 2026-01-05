<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';

    import HeadingSmall from '@/components/HeadingSmall.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Textarea } from '@/components/ui/textarea';
    import { Label } from '@/components/ui/label';
    import type { BaseFormSnippetProps, ProfileFormSnippetProps } from '@/types/forms';
    import { Form, Link, page } from '@inertiajs/svelte';


    const breadcrumbs: BreadcrumbItem[] = [
        {
        title: 'Customers',
        href: '/customers',
        },
        {
        title: 'Create Customers',
        href: '/customers/Create',
        },
    ];
</script>

<svelte:head>
  <title>Customers</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="p-4 md:p-6 flex flex-col space-y-6 max-w-lg">
            <HeadingSmall
                title="Customer Information"
                description="Provide customer details below"
            />        
            <Form method="post" action={route('customers.store')} class="space-y-6">
                {#snippet children({ errors, processing }: BaseFormSnippetProps)}

                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input name="name" id="name" class="mt-1 block w-full" autocomplete="name" placeholder="Full name" />
                    <InputError class="mt-2" message={errors.name} />
                </div>

                <div class="grid gap-2">
                    <Label for="mobile">Mobile Number</Label>
                    <Input
                        id="mobile"
                        name="phone"
                        class="mt-1 block w-full"
                        type="tel"
                        placeholder="Mobile number"
                    />
                    <InputError class="mt-2" message={errors.phone} />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        name="email"
                        class="mt-1 block w-full"
                        type="email"
                        placeholder="Email address"
                    />
                    <InputError class="mt-2" message={errors.email} />
                </div>

                <div class="grid gap-2">
                    <Label for="address">Address</Label>
                    <Textarea
                        id="address"
                        name="address"
                        class="mt-1 block w-full"
                        placeholder="Address"
                    />
                    <InputError class="mt-2" message={errors.address} />
                </div>

                <div class="flex items-center gap-4">
                    <Button name='submit' type="submit" disabled={processing}>Submit</Button>
                </div>
            {/snippet}
        </Form>
    </div>
</AppLayout>
