<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Celebration from '@/components/anniversary/Celebration.vue';
import SlidingPuzzle from '@/components/anniversary/SlidingPuzzle.vue';
import { complete, move, restart } from '@/routes/anniversary/puzzle';

const props = defineProps<{
    board: number[];
    moves: number;
    photoUrl: string;
}>();

const isBusy = ref(false);
const errorMessage = ref('');
const isSolved = computed(() =>
    props.board.every(
        (tile, position) => tile === (position === 8 ? 0 : position + 1),
    ),
);

function moveTile(position: number): void {
    if (isBusy.value || isSolved.value) {
        return;
    }

    isBusy.value = true;
    errorMessage.value = '';

    router.post(
        move.url(),
        { position },
        {
            preserveScroll: true,
            onError: (errors) => {
                errorMessage.value =
                    errors.puzzle ??
                    'That tile could not move. Please try again.';
            },
            onFinish: () => {
                isBusy.value = false;
            },
        },
    );
}

function restartPuzzle(): void {
    if (isBusy.value) {
        return;
    }

    isBusy.value = true;
    errorMessage.value = '';

    router.post(
        restart.url(),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                isBusy.value = false;
            },
        },
    );
}

function claimSurprise(): void {
    if (isBusy.value || !isSolved.value) {
        return;
    }

    isBusy.value = true;
    router.post(
        complete.url(),
        {},
        {
            preserveScroll: true,
            onError: (errors) => {
                errorMessage.value =
                    errors.puzzle ??
                    'The picture needs one more moment to settle.';
            },
            onFinish: () => {
                isBusy.value = false;
            },
        },
    );
}
</script>

<template>
    <Head title="Put Our Picture Together" />

    <section
        class="anniversary-enter mx-auto grid w-full max-w-4xl justify-items-center gap-6 py-5 text-center sm:gap-7 sm:py-8"
    >
        <div class="grid justify-items-center gap-3">
            <span
                class="rounded-full border border-white/85 bg-white/65 px-4 py-1.5 text-[0.65rem] font-semibold tracking-[0.2em] text-[#aa687f] uppercase"
            >
                one picture, a thousand little moments
            </span>
            <h1 class="anniversary-display text-4xl text-[#754456] sm:text-5xl">
                A piece of us
            </h1>
            <p class="max-w-lg text-sm leading-6 text-[#916c7a] sm:text-base">
                Slide the tiles beside the empty space until our picture finds
                its way back together.
            </p>
        </div>

        <SlidingPuzzle
            :board="props.board"
            :photo-url="props.photoUrl"
            :moves="props.moves"
            :moving="isBusy"
            @move="moveTile"
            @restart="restartPuzzle"
        />

        <p v-if="errorMessage" class="text-sm text-[#bd4d70]" role="alert">
            {{ errorMessage }}
        </p>

        <Celebration v-if="isSolved" @claim="claimSurprise" />
    </section>
</template>
