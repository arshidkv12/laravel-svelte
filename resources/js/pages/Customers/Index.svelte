<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type Flash, type BreadcrumbItem } from '@/types';
    import { router } from '@inertiajs/svelte';
    import { onMount } from 'svelte';
    import { page } from '@inertiajs/svelte'
    import { toast } from "svelte-sonner";

    export let customers: {
        data: Array<{
        id: number;
        name: string;
        phone: string;
        email?: string;
        address?: string;
        created_at_formatted: string;
        }>;
        current_page: number;
        last_page: number;
        links: any[];
    };

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Customers',
      href: '/customers',
    },
  ];

  let search = '';
  let isMobile = false;

  onMount(() => {

    const flash = $page.flash as Flash;
    if (flash.message && flash.type === 'success') {
        toast.success(flash.message);
    }

    isMobile = window.innerWidth < 768;

    const handleResize = () => {
      isMobile = window.innerWidth < 768;
    };

    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  });

  function applyFilters() {
    router.get(
      '/customers',
      { search },
      { preserveState: true, replace: true }
    );
  }
</script>

<svelte:head>
  <title>Customers</title>
</svelte:head>

<AppLayout {breadcrumbs}>

    <div class="p-4 md:p-6 space-y-6">
        <!-- Filters & Add Customer -->
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
        <input
            type="text"
            id="search-customers"
            placeholder="Search customer name / phone"
            bind:value={search}
            on:change={applyFilters}
            class="border rounded px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-blue-400"
        />

        <a
            href="/customers/create"
            class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition w-full sm:w-auto text-center"
        >
            + New Customer
        </a>
        </div>

        <!-- Desktop Table -->
        {#if !isMobile}
        <div class="overflow-x-auto border rounded-xl shadow-md">
            <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="hover:bg-gray-100 border-b">
                    <th class="p-4 text-left font-medium text-gray-700">Created At</th>
                    <th class="p-4 text-left font-medium text-gray-700">Name</th>
                    <th class="p-4 text-left font-medium text-gray-700">Phone</th>
                    <th class="p-4 text-left font-medium text-gray-700">Email</th>
                    <th class="p-4 text-right font-medium text-gray-700">Action</th>
                </tr>
            </thead>

            <tbody>
                {#if customers.data.length === 0}
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">
                    No customers found
                    </td>
                </tr>
                {/if}

                {#each customers.data as customer}
                <tr class="last:border-b-0 border-b hover:bg-gray-50 cursor-pointer">
                    <td class="p-4 text-gray-700">{customer.created_at_formatted}</td>
                    <td class="p-4 font-medium text-gray-800">{customer.name}</td>
                    <td class="p-4 text-gray-700">{customer.phone}</td>
                    <td class="p-4 text-gray-700">{customer.email ?? '-'}</td>
                    <td class="p-4 text-right">
                    <a href={`/customers/${customer.id}`} class="text-blue-600 font-medium hover:underline">
                        View
                    </a>
                    </td>
                </tr>
                {/each}
            </tbody>
            </table>
        </div>
        {:else}
        <!-- Mobile Cards -->
        <div class="space-y-4">
            {#if customers.data.length === 0}
            <div class="p-4 text-center text-gray-500 border rounded-lg">
                No customers found
            </div>
            {/if}

            {#each customers.data as customer}
            <div class="border rounded-xl shadow-sm hover:shadow-md transition-shadow bg-white">
                <div class="p-4 space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                    <div class="font-semibold text-gray-900 text-lg">{customer.name}</div>
                    <div class="text-sm text-gray-500 mt-1">{customer.created_at_formatted}</div>
                    </div>
                </div>

                <div class="space-y-1 text-sm text-gray-700">
                    <div>Phone: {customer.phone}</div>
                    <div>Email: {customer.email ?? '-'}</div>
                    <div>Address: {customer.address ?? '-'}</div>
                </div>

                <div class="pt-2 border-t flex justify-end">
                    <a
                    href={`/customers/${customer.id}`}
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm"
                    >
                    View Details
                    </a>
                </div>
                </div>
            </div>
            {/each}
        </div>
        {/if}

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between pt-4 gap-4">
        <div class="text-sm text-gray-600">
            Page {customers.current_page} of {customers.last_page}
        </div>

        <div class="flex flex-wrap gap-1 justify-center">
            {#each customers.links as link}
            <button
                disabled={!link.url}
                on:click={() =>
                link.url &&
                router.get(link.url, {}, { preserveState: true, preserveScroll: true, replace: true })
                }
                class="cursor-pointer px-3 py-1 text-sm border rounded
                {link.active ? 'bg-black text-white border-black' : 'text-gray-700 hover:bg-gray-100'}
                disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {@html link.label}
            </button>
            {/each}
        </div>
        </div>
    </div>
</AppLayout>
