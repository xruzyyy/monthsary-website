<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';
import Envelope from '@/components/anniversary/Envelope.vue';
import LoveLetter from '@/components/anniversary/LoveLetter.vue';
import PhotoReveal from '@/components/anniversary/PhotoReveal.vue';
import { replay } from '@/routes/anniversary';

interface Letter {
    greeting: string;
    paragraphs: string[];
    signature: string;
}

const props = defineProps<{
    photoUrl: string;
    letter: Letter;
}>();

const isEnvelopeOpen = ref(false);
const isRevealVisible = ref(false);
const isReplaying = ref(false);
let revealTimer: ReturnType<typeof setTimeout> | undefined;

function openLetter(): void {
    if (isEnvelopeOpen.value) {
        return;
    }

    isEnvelopeOpen.value = true;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        isRevealVisible.value = true;

        return;
    }

    revealTimer = setTimeout(() => {
        isRevealVisible.value = true;
    }, 900);
}

function replayJourney(): void {
    if (isReplaying.value) {
        return;
    }

    isReplaying.value = true;
    router.post(
        replay.url(),
        {},
        {
            onFinish: () => {
                isReplaying.value = false;
            },
        },
    );
}

onBeforeUnmount(() => {
    if (revealTimer !== undefined) {
        clearTimeout(revealTimer);
    }
});
</script>

<template>
    <Head title="A Little Something for You" />

    <section
        class="anniversary-enter mx-auto grid w-full max-w-5xl justify-items-center gap-7 py-5 text-center sm:gap-9 sm:py-8"
    >
        <template v-if="!isRevealVisible">
            <div class="grid justify-items-center gap-3">
                <span
                    class="rounded-full border border-white/85 bg-white/65 px-4 py-1.5 text-[0.65rem] font-semibold tracking-[0.2em] text-[#aa687f] uppercase"
                >
                    the last little step
                </span>
                <h1
                    class="anniversary-display text-4xl text-[#754456] sm:text-5xl"
                >
                    A Little Something for You
                </h1>
                <p class="text-sm leading-6 text-[#916c7a] sm:text-base">
                    There is one more surprise waiting inside.
                </p>
            </div>

            <Envelope :is-open="isEnvelopeOpen" @open="openLetter" />
        </template>

        <div v-else class="grid w-full justify-items-center gap-8 sm:gap-10">
            <header class="grid justify-items-center gap-3 text-center">
                <span class="text-2xl text-[#d65c88]" aria-hidden="true"
                    >♡</span
                >
                <p
                    class="text-[0.66rem] font-semibold tracking-[0.24em] text-[#aa687f] uppercase"
                >
                    our little milestone
                </p>
                <h1
                    class="anniversary-display text-4xl leading-tight text-[#754456] sm:text-6xl"
                >
                    Happy 5th Monthsary
                </h1>
                <p
                    class="anniversary-display text-2xl text-[#b35e7c] sm:text-3xl"
                >
                    My Favorite Person
                </p>
            </header>

            <div
                class="grid w-full items-center gap-9 md:grid-cols-[0.85fr_1.15fr] md:gap-12"
            >
                <PhotoReveal :photo-url="props.photoUrl" />
                <LoveLetter :letter="props.letter" :visible="isRevealVisible" />
            </div>

            <p class="anniversary-display text-2xl text-[#b35e7c] sm:text-3xl">
                I love you so much ♡
            </p>
            <button
                type="button"
                class="anniversary-link rounded-full px-5 py-3 text-sm font-medium text-[#ad7185] underline decoration-[#e5b1c1] decoration-1 underline-offset-4 transition hover:text-[#9f4264] disabled:opacity-50"
                :disabled="isReplaying"
                @click="replayJourney"
            >
                {{ isReplaying ? 'Starting over…' : 'Replay our journey' }}
            </button>
        </div>
    </section>
</template>
