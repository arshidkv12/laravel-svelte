<script lang="ts">
  import Clock from 'lucide-svelte/icons/clock';
  import CheckCircle from 'lucide-svelte/icons/check-circle';
  import XCircle from 'lucide-svelte/icons/x-circle';
  import Package from 'lucide-svelte/icons/package';
  import AlertCircle from 'lucide-svelte/icons/alert-circle';

  export let status: string;
  export let className = 'h-3 w-3';

  const statusIcons = {
    pending: Clock,
    in_progress: Clock,
    waiting_parts: AlertCircle,
    completed: CheckCircle,
    delivered: Package,
    cancelled: XCircle,
  } as const;

  type Status = keyof typeof statusIcons;

  const isValidStatus = (s: string): s is Status => s in statusIcons;
</script>

{#if isValidStatus(status)}
  <svelte:component
    this={statusIcons[status]}
    class={className}
  />
{/if}
