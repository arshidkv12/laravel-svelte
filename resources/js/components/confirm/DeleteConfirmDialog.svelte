<script lang="ts">
  import { Button } from "@/components/ui/button";
  import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogCancel,
    AlertDialogAction,
    AlertDialogTrigger
  } from "@/components/ui/alert-dialog";
  import { Trash2 } from "lucide-svelte";
  
  export let onConfirm: () => Promise<void>;
  export let title = "Confirm Deletion";
  export let description: string;
  export let itemName: string;
  export let buttonText = "Delete";
  export let triggerClass = "";
  export let disabled = false;
  
  let isDeleting = false;
  let isOpen = false;
  
  async function handleConfirm() {
    isDeleting = true;
    try {
      await onConfirm();
    } finally {
      isDeleting = false;
      isOpen = false;
    }
  }
</script>

<AlertDialog bind:open={isOpen}>
  <AlertDialogTrigger>
    <Button 
      variant="destructive" 
      class={`gap-1 sm:gap-2 text-sm md:text-base cursor-pointer ${triggerClass}`}
      {disabled}
      size="sm"
    >
      <Trash2 class="h-4 w-4" />
      {buttonText}
    </Button>
  </AlertDialogTrigger>
  <AlertDialogContent class="sm:max-w-md">
    <AlertDialogHeader>
      <AlertDialogTitle>{title}</AlertDialogTitle>
      <AlertDialogDescription>
        {@html description || `Are you sure you want to delete ${itemName ? `"${itemName}"` : 'this item'}?`}
      </AlertDialogDescription>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel disabled={isDeleting} class="cursor-pointer">Cancel</AlertDialogCancel>
      <AlertDialogAction
        onclick={handleConfirm}
        disabled={isDeleting}
        class="bg-red-600 hover:bg-red-700 text-white cursor-pointer"
      >
        {#if isDeleting}
          <div class="flex items-center justify-center gap-2">
            <div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent">
            </div>
            Deleting...
          </div>
        {:else}
          Delete
        {/if}
      </AlertDialogAction>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>