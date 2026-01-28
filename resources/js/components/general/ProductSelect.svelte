<script lang="ts">
    import CheckIcon from "@lucide/svelte/icons/check";
    import ChevronsUpDownIcon from "@lucide/svelte/icons/chevrons-up-down";
    import { onMount, tick } from "svelte";

    import * as Command from "@/components/ui/command";
    import * as Popover from "@/components/ui/popover";
    import { Button, buttonVariants } from "@/components/ui/button";
    import { cn } from "@/lib/utils";
    import Input from "../ui/input/input.svelte";
    import { type Product } from "@/types/products";

    let { selectedProduct = null } = $props<{ selectedProduct?: Product }>();

    let open = $state(false);
    let triggerRef: HTMLButtonElement | null = $state(null);
    let loading = $state(false);
    
    let products = $state<Product[]>([]);

    const selectedLabel = $derived( 
        selectedProduct && selectedProduct.sku ? selectedProduct.name + '-' + selectedProduct.sku : ''
    );


    function close() {
        open = false;
        tick().then(() => triggerRef?.focus());
    }

    function onSelectedProduct(selectedProduct: Product) {
        selectedProduct = selectedProduct;
        close();
    }

    let timeout: ReturnType<typeof setTimeout>;

    async function searchproducts(query: string) {
        if (!query.trim()) {
            products = [];
            return;
        }

        loading = true;
        
        try {
            const res = await fetch(
                `/products/search?q=${encodeURIComponent(query)}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                }
            );

            if (res.ok) {
                products = await res.json();
                console.log(products)
            } else {
                console.error('Search failed:', res.status);
                products = [];
            }
        } catch (e) {
            console.error('Search error:', e);
            products = [];
        } finally {
            loading = false;
        }
    }

    function onSearchInput(e: Event) {
        const q = (e.currentTarget as HTMLInputElement).value;
        
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            searchproducts(q);
        }, 300);
    }
</script>

<Popover.Root bind:open>
  <Popover.Trigger class={cn(buttonVariants({ variant: "outline"}), "w-full justify-between")}>
      <span>{selectedLabel || "Select a product..."}</span>
      <ChevronsUpDownIcon class="ml-2 h-4 w-4 shrink-0 opacity-50" />
  </Popover.Trigger>

  <Popover.Content class="w-[320px] p-0" align="start">
    <Command.Root>
      <Command.Input
        placeholder="Search product..."
        oninput={onSearchInput}
      />

      <Command.List>
        {#if loading}
          <Command.Loading class="p-4">Searching...</Command.Loading>
        {/if}

        <Command.Empty>
          {loading ? "Searching..." : ""}
          {!loading && products.length < 1 ? "No product found." : ""}
        </Command.Empty>

        <Command.Group>
          {#each products as product}
            <Command.Item
              value={product.name}
              onSelect={() => onSelectedProduct(product)}
            >
              <CheckIcon
                class={cn(
                  "mr-2 h-4 w-4",
                  selectedProduct && selectedProduct.id === product.id ? "opacity-100" : "opacity-0"
                )}
              />
              {product.name}
            </Command.Item>
          {/each}
        </Command.Group>
      </Command.List>
    </Command.Root>
  </Popover.Content>
</Popover.Root>