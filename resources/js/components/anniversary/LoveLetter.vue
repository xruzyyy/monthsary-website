<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, watch } from 'vue';
import { ref } from 'vue';

interface Letter {
    greeting: string;
    paragraphs: string[];
    signature: string;
}

const props = defineProps<{
    letter: Letter;
    visible: boolean;
}>();

const fullText = computed(() =>
    [
        props.letter.greeting,
        ...props.letter.paragraphs,
        props.letter.signature,
    ].join('\n\n'),
);
const displayedText = ref('');
let timer: ReturnType<typeof setTimeout> | undefined;

function revealText(): void {
    if (timer !== undefined) {
        clearTimeout(timer);
    }

    displayedText.value = '';

    if (
        typeof window === 'undefined' ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        displayedText.value = fullText.value;

        return;
    }

    let characterIndex = 0;

    function typeNextCharacter(): void {
        characterIndex += 1;
        displayedText.value = fullText.value.slice(0, characterIndex);

        if (characterIndex < fullText.value.length) {
            timer = setTimeout(typeNextCharacter, 18);
        }
    }

    typeNextCharacter();
}

watch(
    () => props.visible,
    (visible) => {
        if (visible) {
            revealText();
        }
    },
);

onMounted(() => {
    if (props.visible) {
        revealText();
    }
});

onBeforeUnmount(() => {
    if (timer !== undefined) {
        clearTimeout(timer);
    }
});
</script>

<template>
    <article
        class="anniversary-card anniversary-enter rounded-[1.75rem] p-6 text-left sm:p-9"
    >
        <span class="mb-4 block text-2xl text-[#d65c88]" aria-hidden="true"
            >♡</span
        >
        <h3
            class="anniversary-display mb-5 text-2xl text-[#754456] sm:text-3xl"
        >
            A little note from my heart
        </h3>
        <p class="sr-only">{{ fullText }}</p>
        <p
            class="text-[0.98rem] leading-8 whitespace-pre-line text-[#805d6b]"
            aria-hidden="true"
        >
            {{ displayedText
            }}<span
                v-if="visible && displayedText.length < fullText.length"
                class="typing-cursor"
                >|</span
            >
        </p>
    </article>
</template>

<style scoped>
.typing-cursor {
    display: inline-block;
    margin-left: 1px;
    color: #d65c88;
    animation: cursor-blink 760ms steps(2, start) infinite;
}

@keyframes cursor-blink {
    to {
        visibility: hidden;
    }
}
</style>
