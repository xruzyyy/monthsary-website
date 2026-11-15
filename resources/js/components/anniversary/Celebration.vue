<script setup lang="ts">
import PrimaryButton from '@/components/anniversary/PrimaryButton.vue';

const emit = defineEmits<{
    claim: [];
}>();

const sparkles = Array.from({ length: 14 }, (_, index) => ({
    left: `${(index * 37 + 11) % 100}%`,
    delay: `${(index % 7) * 90}ms`,
    rotate: `${(index * 47) % 360}deg`,
}));
</script>

<template>
    <div
        class="relative overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/78 p-6 text-center shadow-[0_18px_50px_rgb(117_68_86_/_10%)] sm:p-8"
    >
        <span
            v-for="(sparkle, index) in sparkles"
            :key="index"
            class="sparkle"
            :style="{
                left: sparkle.left,
                animationDelay: sparkle.delay,
                transform: `rotate(${sparkle.rotate})`,
            }"
            aria-hidden="true"
        >
            ✦
        </span>
        <div class="relative z-10 grid justify-items-center gap-3">
            <span class="text-3xl text-[#d65c88]" aria-hidden="true">♡</span>
            <h2 class="anniversary-display text-3xl text-[#754456] sm:text-4xl">
                You did it! Our picture is complete.
            </h2>
            <p class="max-w-md text-sm leading-6 text-[#936c7a]">
                Every little piece found its way home. One more surprise is
                waiting for you.
            </p>
            <PrimaryButton class="mt-2" @click="emit('claim')">
                Claim Your Surprise <span aria-hidden="true">♡</span>
            </PrimaryButton>
        </div>
    </div>
</template>

<style scoped>
.sparkle {
    position: absolute;
    top: -1.5rem;
    color: #e7a1b6;
    animation: sparkle-fall 1.8s ease-in infinite;
}

@keyframes sparkle-fall {
    0% {
        transform: translateY(0) rotate(0) scale(0.6);
        opacity: 0;
    }

    20% {
        opacity: 0.8;
    }

    100% {
        transform: translateY(180px) rotate(130deg) scale(1.1);
        opacity: 0;
    }
}
</style>
