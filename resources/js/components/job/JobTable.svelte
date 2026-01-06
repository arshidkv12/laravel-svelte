<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import User from 'lucide-svelte/icons/user';
    import Phone from 'lucide-svelte/icons/phone';
    import Eye from 'lucide-svelte/icons/eye';
    import SquarePen from 'lucide-svelte/icons/square-pen';
    import { getJobStatusClasses } from '@/lib/helper/status';
    import StatusIcon from './StatusIcon.svelte';

    export let jobCards: {
        data: Array<{
        id: number;
        job_no: string;
        status: string;
        created_at_formatted: string;
        delivery_date_formatted?: string | null;
        customer: {
            id: number;
            name: string;
            phone?: string | null;
        };
        }>;
    };
</script>

<div class="overflow-x-auto border rounded-xl shadow-md">
  <table class="w-full text-sm">
    <thead class="bg-gray-50">
      <tr class="border-b">
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
        <tr class="group hover:bg-gray-50 border-b last:border-b-0 transition-colors">
          <!-- Job No -->
          <td class="p-4">
            <div class="font-medium text-gray-900 text-sm">
              {job.job_no}
            </div>
          </td>

          <!-- Date -->
          <td class="p-4 text-sm text-gray-700">
            {job.created_at_formatted}
          </td>

          <!-- Customer -->
          <td class="p-4">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-blue-50 rounded-lg">
                <User class="h-3.5 w-3.5 text-blue-600" />
              </div>
              <div class="min-w-0">
                <div class="font-medium text-gray-900 truncate text-sm">
                  {job.customer.name}
                </div>
                <div class="text-xs text-gray-500">
                  ID: #{job.customer.id.toString().padStart(4, '0')}
                </div>
              </div>
            </div>
          </td>

          <!-- Phone -->
          <td class="p-4">
            <div class="flex items-center gap-2">
              <Phone class="h-4 w-4 text-gray-400" />
              <span class="text-sm text-gray-700">
                {job.customer.phone || '—'}
              </span>
            </div>
          </td>

          <!-- Status -->
          <td class="p-4">
            <span
              class={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold ${getJobStatusClasses(job.status)}`}
            >
              <StatusIcon
                status={job.status}
                className="h-4 w-4"
              />
              {job.status.replaceAll('_', ' ')}
            </span>
          </td>

          <!-- Delivery -->
          <td class="p-4 text-sm text-gray-700">
            {job.delivery_date_formatted || '—'}
          </td>

          <!-- Actions -->
          <td class="p-4">
            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <Link
                href={`/job-cards/${job.id}`}
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg"
              >
                <Eye class="h-4 w-4" />
                View
              </Link>

              <Link
                href={`/job-cards/${job.id}/edit`}
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg"
              >
                <SquarePen class="h-4 w-4" />
                Edit
              </Link>
            </div>
          </td>
        </tr>
      {/each}
    </tbody>
  </table>
</div>
