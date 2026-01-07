<script lang="ts">
import CheckIcon from "@lucide/svelte/icons/check";
import ChevronsUpDownIcon from "@lucide/svelte/icons/chevrons-up-down";
import { tick } from "svelte";

import * as Command from "@/components/ui/command";
import * as Popover from "@/components/ui/popover";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";

let { modelValue = $bindable() } = $props();

type Customer = {
  value: number;
  label: string;
};

let open = $state(false);
let triggerRef: HTMLButtonElement | null = $state(null);
let customers = $state<Customer[]>([]);
let loading = $state(false);

const value = $derived(modelValue ?? null);

const selectedLabel = $derived(
  customers.find(c => c.value === value)?.label || ""
);

function close() {
  open = false;
  tick().then(() => triggerRef?.focus());
}

function onSelectCustomer(customerValue: number) {
  modelValue = customerValue;
  close();
}

let timeout: ReturnType<typeof setTimeout>;

async function searchCustomers(query: string) {
  if (!query.trim()) {
    customers = [];
    return;
  }

  loading = true;
  
  try {
    const res = await fetch(
      `/customers/search?q=${encodeURIComponent(query)}`,
      {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      }
    );

    if (res.ok) {
      customers = await res.json();
    } else {
      console.error('Search failed:', res.status);
      customers = [];
    }
  } catch (e) {
    console.error('Search error:', e);
    customers = [];
  } finally {
    loading = false;
  }
}

function onSearchInput(e: Event) {
  const q = (e.currentTarget as HTMLInputElement).value;
  
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    searchCustomers(q);
  }, 300);
}
</script>

<Popover.Root bind:open>
  <Popover.Trigger bind:ref={triggerRef}>
    <Button
      variant="outline"
      class="w-full justify-between"
      role="combobox"
      aria-expanded={open}
    >
      {selectedLabel || "Select a customer..."}
      <ChevronsUpDownIcon class="ml-2 h-4 w-4 shrink-0 opacity-50" />
    </Button>
  </Popover.Trigger>

  <Popover.Content class="w-[320px] p-0" align="start">
    <Command.Root>
      <Command.Input
        placeholder="Search customer..."
        oninput={onSearchInput}
      />

      <Command.List>
        {#if loading}
          <Command.Loading>Searching...</Command.Loading>
        {/if}

        <Command.Empty>
          {loading ? "Searching..." : "No customer found."}
        </Command.Empty>

        <Command.Group>
          {#each customers as customer (customer.value)}
            <Command.Item
              value={customer.label}
              onSelect={() => onSelectCustomer(customer.value)}
            >
              <CheckIcon
                class={cn(
                  "mr-2 h-4 w-4",
                  value === customer.value ? "opacity-100" : "opacity-0"
                )}
              />
              {customer.label}
            </Command.Item>
          {/each}
        </Command.Group>
      </Command.List>
    </Command.Root>
  </Popover.Content>
</Popover.Root>