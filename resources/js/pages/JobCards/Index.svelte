<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type Flash, type BreadcrumbItem, type Filters } from '@/types';
    import { onMount } from 'svelte';
    import { page } from '@inertiajs/svelte';
    import JobTable from '@/components/job/JobTable.svelte';
    import MobileJobTable from '@/components/job/MobileJobTable.svelte';
    import { toast } from 'svelte-sonner';
    import Pagination from '@/components/general/Pagination.svelte';
    import Card from '@/components/ui/card/card.svelte';
    import CardContent from '@/components/ui/card/card-content.svelte';
    import Filter from '@/components/general/Filter.svelte';
    import { type JobCardsPagination, type JobStatusOption } from '@/types/job-card';

    let { jobCards, jobStatusOptions, filters } = $props<{
      jobCards: JobCardsPagination;
      jobStatusOption: JobStatusOption[];
      filters?: Filters
    }>();

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
  
  <!-- Filters Card -->
  <Card class="shadow-none border-none pb-0">
      <CardContent>
          <Filter routePath='job-cards.index' {filters} statusOptions={jobStatusOptions} />
      </CardContent>
  </Card>

  <div class="p-4 md:p-6 space-y-6">
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
    <Pagination links={jobCards.links}  
      currentPage={jobCards.current_page} 
      lastPage={jobCards.last_page}
    />
    
  </div>
</AppLayout>