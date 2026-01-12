<script lang="ts">
    import { Image } from 'lucide-svelte';
    import { Dialog, DialogContent } from '@/components/ui/dialog';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
    import { ChevronLeft, ChevronRight, X } from 'lucide-svelte';
    import { onMount } from 'svelte';

    let { 
        images = [],
        title = "Image Gallery",
        description = "View images related to this job card"
    } = $props<{
        images?: Array<{
            url: string;
            name?: string;
        }>;
    }>();

    let showLightbox = false;
    let currentIndex = 0;

    const currentImage = $derived(images[currentIndex]);

    onMount(()=>{
    })

    function openLightbox(index: number) {
        currentIndex = index;
        showLightbox = true;
    }

    function nextImage() {
        if (currentIndex < images.length - 1) {
            currentIndex++;
        }
    }

    function previousImage() {
        if (currentIndex > 0) {
            currentIndex--;
        }
    }
</script>

<!-- Image Gallery Card -->
<Card class="border border-gray-200 shadow-sm">
    <CardHeader class="pb-3">
        <CardTitle class="flex items-center gap-2">
            <Image class="h-5 w-5 text-blue-600" />
            {title}
        </CardTitle>
        <CardDescription>
            {description}
        </CardDescription>
    </CardHeader>
    <CardContent>
        {#if images && images.length > 0}
            <div class="grid grid-cols-3 gap-3">
                {#each images as image, index}
                    <div class="relative group">
                                                    <!-- on:click={() => openLightbox(index)} -->

                        <img
                            src={'/uploads/images/'+image.file_name}
                            alt={image.name || `Job image ${index + 1}`}
                            class="w-full h-32 object-cover rounded-md border border-gray-200 hover:border-blue-400 transition-colors cursor-pointer hover:scale-[1.02] transition-transform duration-200"
                        />
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity rounded-md"></div>
                    </div>
                {/each}
            </div>
        {:else}
            <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                <Image class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                <p class="text-sm text-gray-500">No images available</p>
            </div>
        {/if}
    </CardContent>
</Card>

<!-- Image Lightbox Dialog -->
<Dialog bind:open={showLightbox}>
    <DialogContent class="max-w-4xl">
        <div class="relative">
            {#if currentImage}
                <img
                    src={currentImage.url}
                    alt={currentImage.name || 'Full view'}
                    class="w-full max-h-[70vh] object-contain rounded-lg"
                />
            {/if}
            <Button
                size="icon"
                variant="outline"
                class="absolute top-4 right-4 bg-white/80 hover:bg-white cursor-pointer"
                onclick={() => showLightbox = false}
            >
                <X class="h-4 w-4" />
            </Button>
            
            <!-- Navigation -->
            <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-4">
                <Button
                    size="icon"
                    variant="outline"
                    class="bg-white/80 hover:bg-white cursor-pointer"
                    onclick={previousImage}
                    disabled={currentIndex === 0}
                >
                    <ChevronLeft class="h-4 w-4" />
                </Button>
                <Button
                    size="icon"
                    variant="outline"
                    class="bg-white/80 hover:bg-white cursor-pointer"
                    onclick={nextImage}
                    disabled={currentIndex === images.length - 1}
                >
                    <ChevronRight class="h-4 w-4" />
                </Button>
            </div>
        </div>
        {#if images.length > 0}
            <div class="text-center text-sm text-gray-500">
                {currentIndex + 1} of {images.length}
                {#if currentImage?.name}
                    <p class="mt-1 font-medium">{currentImage.name}</p>
                {/if}
            </div>
        {/if}
    </DialogContent>
</Dialog>