<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import PuzzleTile from '@/components/anniversary/PuzzleTile.vue';

const props = defineProps<{
    board: number[];
    photoUrl: string;
    moves: number;
    moving: boolean;
}>();

const emit = defineEmits<{
    move: [position: number];
    restart: [];
}>();
const photoAspectRatio = ref(1);
let photoImage: HTMLImageElement | undefined;

onMounted(() => {
    photoImage = new Image();
    photoImage.onload = () => {
        if (photoImage && photoImage.naturalHeight > 0) {
            photoAspectRatio.value =
                photoImage.naturalWidth / photoImage.naturalHeight;
        }
    };
    photoImage.src = props.photoUrl;
});

onBeforeUnmount(() => {
    if (photoImage) {
        photoImage.onload = null;
        photoImage.src = '';
    }
});

function canMove(position: number): boolean {
    const emptyPosition = props.board.indexOf(0);

    return (
        Math.abs(Math.floor(position / 3) - Math.floor(emptyPosition / 3)) +
            Math.abs((position % 3) - (emptyPosition % 3)) ===
        1
    );
}
</script>

<template>
    <div class="grid gap-5">
        <TransitionGroup
            tag="div"
            name="anniversary-tile"
            class="mx-auto grid w-full max-w-[min(82vw,23rem)] grid-cols-3 grid-rows-3 gap-1.5 rounded-[1.45rem] border border-white/80 bg-white/70 p-2.5 shadow-[0_18px_45px_rgb(117_68_86_/_12%)] sm:gap-2 sm:p-3"
            role="group"
            aria-label="Sliding picture puzzle"
        >
            <PuzzleTile
                v-for="(tile, position) in board"
                :key="tile"
                :tile="tile"
                :photo-url="photoUrl"
                :photo-aspect-ratio="photoAspectRatio"
                :disabled="moving || tile === 0 || !canMove(position)"
                @select="emit('move', position)"
            />
        </TransitionGroup>

        <div class="flex flex-wrap items-center justify-center gap-3">
            <span
                class="rounded-full border border-white/80 bg-white/65 px-4 py-2 text-sm text-[#87596a]"
            >
                <span class="font-semibold text-[#754456]">{{ moves }}</span>
                {{ moves === 1 ? 'move' : 'moves' }}
            </span>
            <button
                type="button"
                class="anniversary-link rounded-full px-4 py-2 text-sm font-medium text-[#bd587b] underline decoration-[#e5a7ba] decoration-1 underline-offset-4 transition hover:text-[#9f4264]"
                :disabled="moving"
                @click="emit('restart')"
            >
                Start over
            </button>
        </div>
    </div>
</template>
