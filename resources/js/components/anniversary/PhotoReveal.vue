<script setup lang="ts">
import { ref } from 'vue';

defineProps<{
    photoUrl: string;
}>();

const photoAspectRatio = ref(4 / 5);

function updatePhotoAspectRatio(event: Event): void {
    const image = event.currentTarget as HTMLImageElement;

    if (image.naturalHeight > 0) {
        photoAspectRatio.value = image.naturalWidth / image.naturalHeight;
    }
}
</script>

<template>
    <figure
        class="anniversary-enter mx-auto grid w-full max-w-sm justify-items-center gap-4"
    >
        <div
            class="anniversary-photo-frame relative w-full rotate-[-1.5deg] overflow-hidden rounded-[1.5rem] bg-[#f5e1e7]"
            :style="{ aspectRatio: photoAspectRatio.toString() }"
        >
            <img
                :src="photoUrl"
                alt="A favorite moment from our fifth monthsary"
                class="size-full object-cover"
                loading="eager"
                @load="updatePhotoAspectRatio"
            />
            <span
                class="absolute top-7 -right-1 rotate-12 rounded-full bg-white/90 px-4 py-2 text-xs font-medium tracking-wide text-[#b25474] shadow-sm"
            >
                my favorite
            </span>
        </div>
        <figcaption class="text-xs tracking-[0.18em] text-[#a77c8a] uppercase">
            one of my happiest places ♡
        </figcaption>
    </figure>
</template>
