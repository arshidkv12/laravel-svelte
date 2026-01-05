<script lang="ts">
  import AppLayout from '@/layouts/AppLayout.svelte';
  import { type BreadcrumbItem } from '@/types';
  import { Form, router } from '@inertiajs/svelte';

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Job Cards',
      href: '/job-cards',
    },
    {
      title: 'Create Job Card',
      href: '/job-cards/create',
    },
  ];

</script>

<svelte:head>
  <title>Create Job Card</title>
</svelte:head>

<AppLayout {breadcrumbs}>
  <div class="p-4 md:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <button
          class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors p-2 rounded-lg hover:bg-gray-100"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span class="hidden sm:inline font-medium">Back</span>
        </button>
        <h3 class="text-lg">Create New Job Card</h3>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white">
      <Form action="/job-cards" class="space-y-6">
        <!-- Customer Information -->
        <div class="space-y-4">
          <h2 class="text-lg font-semibold text-gray-900 border-b pb-2">Customer Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Customer Select -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Customer *
              </label>
              <select
                bind:value={form.customer_id}
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
              >
                <option value="">Select Customer</option>
                {#each customers as customer}
                  <option value={customer.id}>{customer.name} - {customer.phone}</option>
                {/each}
              </select>
            </div>

            <!-- Or Create New Customer -->
            <div class="md:col-span-2">
              <div class="flex items-center gap-4 mb-2">
                <div class="h-px flex-1 bg-gray-300"></div>
                <span class="text-sm text-gray-500">OR create new customer</span>
                <div class="h-px flex-1 bg-gray-300"></div>
              </div>
            </div>

            <!-- Customer Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Customer Name *
              </label>
              <input
                type="text"
                bind:value={form.customer_name}
                placeholder="Enter customer name"
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                disabled={form.customer_id !== ''}
              />
            </div>

            <!-- Phone -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Phone *
              </label>
              <input
                type="tel"
                bind:value={form.phone}
                placeholder="Enter phone number"
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                disabled={form.customer_id !== ''}
              />
            </div>

            <!-- Address -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Address
              </label>
              <textarea
                bind:value={form.address}
                placeholder="Enter address"
                rows="2"
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                disabled={form.customer_id !== ''}
              />
            </div>
          </div>
        </div>

        <!-- Job Information -->
        <div class="space-y-4">
          <h2 class="text-lg font-semibold text-gray-900 border-b pb-2">Job Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Job Number -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Job Number *
              </label>
              <input
                type="text"
                bind:value={form.job_no}
                placeholder="Auto-generated or enter manually"
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
              />
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Status *
              </label>
              <select
                bind:value={form.status}
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
              >
                <option value="new">New</option>
                <option value="in_progress">In Progress</option>
                <option value="waiting_for_parts">Waiting for Parts</option>
                <option value="ready">Ready</option>
                <option value="delivered">Delivered</option>
              </select>
            </div>

            <!-- Delivery Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Delivery Date *
              </label>
              <input
                type="date"
                bind:value={form.delivery_date}
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
              />
            </div>

            <!-- Problem Description -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Problem Description *
              </label>
              <textarea
                bind:value={form.problem}
                placeholder="Describe the problem..."
                rows="3"
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
              />
            </div>

            <!-- Notes -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Notes
              </label>
              <textarea
                bind:value={form.note}
                placeholder="Additional notes..."
                rows="2"
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
              />
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4 pt-6">
          <button
            type="button"
            on:click={goBack}
            class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
          >
            Create Job Card
          </button>
        </div>
    </Form>

    </div>
  </div>
</AppLayout>