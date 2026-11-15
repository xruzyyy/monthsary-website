<script setup lang="ts">
import { computed, ref } from 'vue';

interface PhotoSlide {
    id: string;
    src: string;
    alt: string;
    caption: string;
}

const props = defineProps<{
    photos: PhotoSlide[];
}>();

const activeIndex = ref(0);
const direction = ref<'next' | 'previous'>('next');
const activePhoto = computed(() => props.photos[activeIndex.value] ?? null);
const transitionName = computed(() => `photo-slide-${direction.value}`);
let touchStartX: number | null = null;

function showNextPhoto(): void {
    if (props.photos.length < 2) {
        return;
    }

    direction.value = 'next';
    activeIndex.value = (activeIndex.value + 1) % props.photos.length;
}

function showPreviousPhoto(): void {
    if (props.photos.length < 2) {
        return;
    }

    direction.value = 'previous';
    activeIndex.value =
        (activeIndex.value - 1 + props.photos.length) % props.photos.length;
}

function showPhoto(index: number): void {
    if (index === activeIndex.value) {
        return;
    }

    direction.value = index > activeIndex.value ? 'next' : 'previous';
    activeIndex.value = index;
}

function rememberTouchStart(event: TouchEvent): void {
    touchStartX = event.changedTouches.item(0)?.clientX ?? null;
}

function handleTouchEnd(event: TouchEvent): void {
    const touchEndX = event.changedTouches.item(0)?.clientX;

    if (touchStartX === null || touchEndX === undefined) {
        touchStartX = null;

        return;
    }

    const distance = touchEndX - touchStartX;
    touchStartX = null;

    if (Math.abs(distance) < 45) {
        return;
    }

    if (distance < 0) {
        showNextPhoto();

        return;
    }

    showPreviousPhoto();
}
</script>

<template>
    <section
        class="grid w-full justify-items-center gap-4"
        aria-label="Our photo memories"
    >
        <div
            class="relative mx-auto w-full max-w-[28rem] touch-pan-y px-2 py-3"
            tabindex="0"
            @keydown.left.prevent="showPreviousPhoto"
            @keydown.right.prevent="showNextPhoto"
            @touchstart.passive="rememberTouchStart"
            @touchend.passive="handleTouchEnd"
        >
            <div
                class="pointer-events-none absolute inset-x-5 top-7 h-[calc(100%-1.75rem)] rotate-3 rounded-[1.8rem] bg-[#f2d7e0] shadow-[0_18px_40px_rgb(117_68_86_/_8%)]"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute inset-x-3 top-4 h-[calc(100%-1rem)] -rotate-2 rounded-[1.8rem] border border-white/75 bg-[#fffafd] shadow-[0_18px_40px_rgb(117_68_86_/_8%)]"
                aria-hidden="true"
            />

            <Transition :name="transitionName" mode="out-in">
                <figure
                    v-if="activePhoto"
                    :key="activePhoto.id"
                    class="relative z-10 mx-auto w-full rotate-[-1deg] rounded-[1.8rem] border border-white/85 bg-white p-3 pb-5 shadow-[0_22px_55px_rgb(117_68_86_/_16%)] sm:p-4 sm:pb-6"
                >
                    <img
                        :src="activePhoto.src"
                        :alt="activePhoto.alt"
                        class="aspect-[3/2] w-full rounded-[1.25rem] bg-[#f5e1e7] object-cover"
                        loading="eager"
                        draggable="false"
                    />
                    <div
                        class="flex items-center justify-between gap-3 px-2 pt-4 text-[0.62rem] font-semibold tracking-[0.2em] text-[#b26d84] uppercase sm:px-3"
                    >
                        <span>our memory</span>
                        <span>{{ activeIndex + 1 }} / {{ photos.length }}</span>
                    </div>
                    <figcaption
                        class="anniversary-display px-2 pt-1 text-left text-xl text-[#754456] sm:px-3 sm:text-2xl"
                    >
                        {{ activePhoto.caption }}
                    </figcaption>
                </figure>
            </Transition>

            <template v-if="photos.length > 1">
                <button
                    type="button"
                    class="absolute top-[42%] left-0 z-20 grid size-10 -translate-y-1/2 place-items-center rounded-full border border-white/90 bg-white/95 text-xl text-[#9c5b73] shadow-lg transition hover:scale-105 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#d65c88] sm:-left-1 sm:size-12"
                    aria-label="Previous photo"
                    @click="showPreviousPhoto"
                >
                    <span aria-hidden="true">‹</span>
                </button>
                <button
                    type="button"
                    class="absolute top-[42%] right-0 z-20 grid size-10 -translate-y-1/2 place-items-center rounded-full border border-white/90 bg-white/95 text-xl text-[#9c5b73] shadow-lg transition hover:scale-105 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#d65c88] sm:-right-1 sm:size-12"
                    aria-label="Next photo"
                    @click="showNextPhoto"
                >
                    <span aria-hidden="true">›</span>
                </button>
            </template>
        </div>

        <div
            v-if="photos.length > 1"
            class="flex items-center justify-center gap-2"
            role="group"
            aria-label="Choose a photo"
        >
            <button
                v-for="(photo, index) in photos"
                :key="photo.id"
                type="button"
                class="grid size-7 place-items-center rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#d65c88]"
                :aria-label="`Show photo ${index + 1}`"
                :aria-pressed="index === activeIndex"
                @click="showPhoto(index)"
            >
                <span
                    class="size-2.5 rounded-full transition-all duration-200"
                    :class="
                        index === activeIndex
                            ? 'scale-125 bg-[#d65c88] shadow-[0_0_0_4px_rgb(214_92_136_/_15%)]'
                            : 'bg-[#e4bcc9] hover:bg-[#cf8ba1]'
                    "
                    aria-hidden="true"
                />
            </button>
        </div>

        <p class="sr-only" aria-live="polite">
            {{ activePhoto?.caption }}
            {{ activeIndex + 1 }} of {{ photos.length }} photos
        </p>
        <p
            v-if="photos.length > 1"
            class="text-xs tracking-wide text-[#a77c8a]"
        >
            Swipe or tap the arrows to see more memories
        </p>
    </section>
</template>

<style scoped>
.photo-slide-next-enter-active,
.photo-slide-next-leave-active,
.photo-slide-previous-enter-active,
.photo-slide-previous-leave-active {
    transition:
        opacity 260ms ease,
        transform 320ms cubic-bezier(0.2, 0.75, 0.25, 1);
}

.photo-slide-next-enter-from {
    transform: translateX(2.5rem) rotate(2deg) scale(0.98);
    opacity: 0;
}

.photo-slide-next-leave-to {
    transform: translateX(-2.5rem) rotate(-2deg) scale(0.98);
    opacity: 0;
}

.photo-slide-previous-enter-from {
    transform: translateX(-2.5rem) rotate(-2deg) scale(0.98);
    opacity: 0;
}

.photo-slide-previous-leave-to {
    transform: translateX(2.5rem) rotate(2deg) scale(0.98);
    opacity: 0;
}

@media (prefers-reduced-motion: reduce) {
    .photo-slide-next-enter-active,
    .photo-slide-next-leave-active,
    .photo-slide-previous-enter-active,
    .photo-slide-previous-leave-active {
        transition: none;
    }
}
</style>
