<script lang="ts">
  import AppLayout from '@/layouts/AppLayout.svelte';
  import { type BreadcrumbItem } from '@/types';
  import { onMount } from 'svelte';

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Job Cards',
      href: '/job-cards',
    },
  ];
  import { router } from '@inertiajs/svelte';

  export let jobCards: {
    data: Array<{
      id: number;
      job_no: string;
      phone: string;
      status: string;
      delivery_date: string;
      created_at_formatted: string;
      delivery_date_formatted: string;
      customer: {
        id: number;
        name: string;
        phone: string;
      };
    }>;
    current_page: number;
    last_page: number;
    links: any[];
  };

  let search = '';
  let status = '';
  let isMobile = false;

  onMount(() => {
    isMobile = window.innerWidth < 768;  
    
    const handleResize = () => {
      isMobile = window.innerWidth < 768;
    };
    
    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  });

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
  <title>Job Cards</title>
</svelte:head>

<AppLayout {breadcrumbs}>
  <div class="p-4 md:p-6 space-y-6">
    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
      <input
        type="text"
        placeholder="Search job / customer"
        bind:value={search}
        on:change={applyFilters}
        class="border rounded px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-blue-400"
      />

      <select
        bind:value={status}
        on:change={applyFilters}
        class="border rounded px-3 py-2 w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-400"
      >
        <option value="">All Status</option>
        <option value="new">New</option>
        <option value="in_progress">In Progress</option>
        <option value="waiting_for_parts">Waiting for Parts</option>
        <option value="ready">Ready</option>
        <option value="delivered">Delivered</option>
      </select>
      
      <a 
        href="/job-cards/create" 
        class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition w-full sm:w-auto text-center"
      >
        + New Job
      </a>
    </div>

    <!-- Conditional rendering based on screen size -->
    {#if !isMobile}
      <!-- Desktop Table -->
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
                <td colspan="7" class="p-4 text-center text-gray-500">
                  No job cards found
                </td>
              </tr>
            {/if}

            {#each jobCards.data as job}
              <tr class="last:border-b-0 cursor-pointer hover:[&,&>svelte-css-wrapper]:[&>th,td]:bg-muted/50 data-[state=selected]:bg-muted border-b transition-colors">
                <td class="p-4 font-medium text-gray-800">{job.job_no}</td>
                <td class="p-4 text-gray-700">{job.created_at_formatted}</td>
                <td class="p-4 text-gray-700">{job.customer.name}</td>
                <td class="p-4 text-gray-700">{job.customer.phone}</td>
                <td class="p-4">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold {getStatusClasses(job.status)}">
                    {job.status.replaceAll('_', ' ')}
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
    {:else}
      <!-- Mobile Cards -->
      <div class="space-y-4">
        {#if jobCards.data.length === 0}
          <div class="p-4 text-center text-gray-500 border rounded-lg">
            No job cards found
          </div>
        {/if}

        {#each jobCards.data as job}
          <div class="border rounded-xl shadow-sm hover:shadow-md transition-shadow bg-white">
            <div class="p-4 space-y-3">
              <!-- Header row -->
              <div class="flex justify-between items-start">
                <div>
                  <div class="font-semibold text-gray-900 text-lg">{job.job_no}</div>
                  <div class="text-sm text-gray-500 mt-1">{job.created_at_formatted}</div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {getStatusClasses(job.status)}">
                  {job.status.replaceAll('_', ' ')}
                </span>
              </div>

              <!-- Customer info -->
              <div class="space-y-2">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <span class="font-medium text-gray-900">{job.customer.name}</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  <span class="text-gray-700">{job.customer.phone}</span>
                </div>
              </div>

              <!-- Delivery and action -->
              <div class="flex justify-between items-center pt-2 border-t">
                <div class="text-sm">
                  <div class="text-gray-500">Delivery Date</div>
                  <div class="font-medium text-gray-900">{job.delivery_date_formatted}</div>
                </div>
                <a 
                  href={`/job-cards/${job.id}`} 
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
      <!-- Info -->
      <div class="text-sm text-gray-600">
        Page {jobCards.current_page} of {jobCards.last_page}
      </div>

      <!-- Links -->
      <div class="flex flex-wrap gap-1 justify-center">
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