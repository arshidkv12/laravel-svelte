<script lang="ts">
  import AppLayout from '@/layouts/AppLayout.svelte';
  import { type BreadcrumbItem } from '@/types';

  const breadcrumbs: BreadcrumbItem[] = [
      {
          title: 'Dashboard',
          href: '/dashboard',
      },
  ];
  import { router } from '@inertiajs/svelte';
  import { page } from '@inertiajs/svelte';

  export let jobCards: {
    data: Array<{
      id: number;
      job_no: string;
      customer_name: string;
      phone: string;
      status: string;
      delivery_date: string;
      created_at_formatted: string;
      delivery_date_formatted: string;
    }>;
    current_page: number;
    last_page: number;
    links: any[];
  };

  let search = '';
  let status = '';

  function applyFilters() {
    router.get(
      '/job-cards',
      { search, status },
      { preserveState: true, replace: true }
    );
  }

   function getStatusClasses(status: string) {
    switch(status) {
      case 'new': return 'bg-blue-100 text-blue-800';
      case 'in_progress': return 'bg-yellow-100 text-yellow-800';
      case 'waiting_for_parts': return 'bg-orange-100 text-orange-800';
      case 'ready': return 'bg-green-100 text-green-800';
      case 'delivered': return 'bg-gray-100 text-gray-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  }
</script>

<svelte:head>
    <title>Dashboard</title>
</svelte:head>

<AppLayout>
  <div class="p-6 space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Job Cards</h1>
      <a href="/job-cards/create" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition">
        + New Job
      </a>
    </div>

    <!-- Filters -->
    <div class="flex gap-4 flex-wrap items-center">
      <input
        type="text"
        placeholder="Search job / customer"
        bind:value={search}
        on:change={applyFilters}
        class="border rounded px-3 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-400"
      />

      <select
        bind:value={status}
        on:change={applyFilters}
        class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
      >
        <option value="">All Status</option>
        <option value="new">New</option>
        <option value="in_progress">In Progress</option>
        <option value="waiting_for_parts">Waiting for Parts</option>
        <option value="ready">Ready</option>
        <option value="delivered">Delivered</option>
      </select>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto border rounded-xl shadow-md">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr class="hover:[&,&>svelte-css-wrapper]:[&>th,td]:bg-muted/50 data-[state=selected]:bg-muted border-b transition-colors">
            <th class="p-4 text-left font-medium text-gray-700">Job No</th>
            <th class="p-4 text-left font-medium text-gray-700">Date</th>
            <th class="p-4 text-left font-medium text-gray-700">Customer</th>
            <th class="p-4 text-left font-medium text-gray-700">Phone</th>
            <th class="p-4 text-left font-medium text-gray-700">Status</th>
            <th class="p-4 text-left font-medium text-gray-700">Delivery</th>
            <th class="p-4 text-right font-medium text-gray-700">Action</th>
          </tr>
        </thead>

        <tbody>
          {#if jobCards.data.length === 0}
            <tr>
              <td colspan="6" class="p-4 text-center text-gray-500">
                No job cards found
              </td>
            </tr>
          {/if}

          {#each jobCards.data as job}
            <tr class="last:border-b-0 cursor-pointer hover:[&,&>svelte-css-wrapper]:[&>th,td]:bg-muted/50 data-[state=selected]:bg-muted border-b transition-colors">
              <td class="p-4 font-medium text-gray-800">{job.job_no}</td>
              <td class="p-4 text-gray-700">{job.created_at_formatted}</td>
              <td class="p-4 text-gray-700">{job.customer_name}</td>
              <td class="p-4 text-gray-700">{job.phone}</td>
              <td class="p-4">
                <span class="px-2 py-1 rounded-full text-xs font-semibold {getStatusClasses(job.status)}">
                  {job.status.replace('_', ' ')}
                </span>
              </td>
              <td class="p-4 text-gray-700">{job.delivery_date_formatted}</td>
              <td class="p-4 text-right">
                <a href={`/job-cards/${job.id}`} class="text-blue-600 font-medium hover:underline">
                  View
                </a>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>

  <!-- Pagination -->
  <div class="flex items-center justify-between pt-4">
    <!-- Info -->
    <div class="text-sm text-gray-600">
      Page {jobCards.current_page} of {jobCards.last_page}
    </div>

    <!-- Links -->
    <div class="flex gap-1">
      {#each jobCards.links as link}
        <button
          disabled={!link.url}
          on:click={() =>
            link.url &&
            router.get(
              link.url,
              {},
              {
                preserveState: true,
                preserveScroll: true,  
                replace: true
              }
            )
          }
          class="
            cursor-pointer
            px-3 py-1 text-sm border rounded
            {link.active
              ? 'bg-black text-white border-black'
              : 'text-gray-700 hover:bg-gray-100'}
            disabled:opacity-50
            disabled:cursor-not-allowed
          "
        >
          {@html link.label}
        </button>
      {/each}
    </div>
  </div>

  </div>
</AppLayout>
