<script lang="ts">
  import AppLayout from '@/layouts/AppLayout.svelte';
  import { type Flash, type BreadcrumbItem } from '@/types';
  import { onMount } from 'svelte';
  import { Link, page, router } from '@inertiajs/svelte';
  import JobTable from '@/components/job/JobTable.svelte';
  import MobileJobTable from '@/components/job/MobileJobTable.svelte';
  import { throttle } from 'lodash';
  import { toast } from 'svelte-sonner';

  type JobCard = {
    id: number;
    job_no: string;
    item: string;
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
  };

  type JobCardsPagination = {
    data: JobCard[];
    current_page: number;
    last_page: number;
    links: any[];
    total: number;
    from: number;
    to: number;
  };

  let { jobCards } = $props<{
    jobCards: JobCardsPagination;
  }>();

  let search = $state('');
  let status = $state('');
  let isMobile = $state(false);

  $effect(() => {   
    const flash = $page.flash as Flash;
    if (flash?.message) {  
        if (flash.type === 'success') {
            toast.success(flash.message);
        } else if (flash.type === 'error') {
            toast.error(flash.message);
        }
    }
  });


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

  const throttledApplyFilters = throttle(applyFilters, 300); 

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Job Cards',
      href: '/job-cards',
    },
  ];

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
        oninput={throttledApplyFilters}
        class="border rounded px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-blue-400"
      />

      <select
        bind:value={status}
        oninput={throttledApplyFilters}
        class="border rounded px-3 py-2 w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-400"
      >
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="in_progress">In Progress</option>
        <option value="waiting_parts">Waiting Parts</option>
        <option value="completed">Completed</option>
        <option value="delivered">Delivered</option>
        <option value="cancelled">Cancelled</option>
      </select>
      
      <Link 
        href="/job-cards/create" 
        class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition w-full sm:w-auto text-center"
      >
        + New Job
      </Link>
    </div>


    <!-- Results Count -->
    {#if jobCards.total > 0}
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">
                Showing <span class="font-medium">{jobCards.from}</span> to <span class="font-medium">{jobCards.to}</span> of{' '}
                <span class="font-medium">{jobCards.total}</span> job cards
            </p>
        </div>
    {/if}

    <!-- Conditional rendering based on screen size -->
    {#if !isMobile}
      <!-- Desktop Table -->
      <JobTable jobCards={jobCards} />
    {:else}
      <!-- Mobile Cards -->
      <MobileJobTable jobCards={jobCards}/>
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
            onclick={() =>
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