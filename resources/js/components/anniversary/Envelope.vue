<script setup lang="ts">
import PrimaryButton from '@/components/anniversary/PrimaryButton.vue';

defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits<{
    open: [];
}>();
</script>

<template>
    <div class="grid justify-items-center gap-7">
        <div
            class="envelope-scene"
            :class="{ 'envelope-open': isOpen }"
            aria-hidden="true"
        >
            <div class="envelope-letter">
                <span class="mb-3 block text-xl text-[#d65c88]">♡</span>
                <span class="anniversary-display text-lg text-[#805665]"
                    >For you, always</span
                >
                <span class="mt-2 block text-xs tracking-[0.2em] text-[#b58b99]"
                    >WITH ALL MY LOVE</span
                >
            </div>
            <div class="envelope-back" />
            <div class="envelope-front" />
            <div class="envelope-flap" />
            <span class="envelope-seal">♥</span>
        </div>

        <PrimaryButton :disabled="isOpen" @click="emit('open')">
            {{ isOpen ? 'Opening your letter…' : 'Open Your Letter' }}
        </PrimaryButton>
    </div>
</template>

<style scoped>
.envelope-scene {
    position: relative;
    width: min(82vw, 23rem);
    height: min(54vw, 15rem);
    min-height: 12rem;
    perspective: 1000px;
}

.envelope-back {
    position: absolute;
    z-index: 1;
    inset: 0;
    border-radius: 0.9rem;
    background: linear-gradient(140deg, #f3b9ca, #e99db4);
    box-shadow: 0 22px 46px rgb(157 75 105 / 20%);
}

.envelope-letter {
    position: absolute;
    z-index: 2;
    right: 7%;
    bottom: 7%;
    left: 7%;
    display: flex;
    height: 86%;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    border: 1px solid #f5e4e9;
    border-radius: 0.5rem;
    background: #fffaf9;
    padding: 1.15rem;
    text-align: center;
    transition: transform 3s cubic-bezier(0.2, 0.75, 0.25, 1) 180ms;
}

.envelope-front {
    position: absolute;
    z-index: 3;
    inset: 0;
    border-radius: 0.9rem;
    background: linear-gradient(145deg, #f6cad5, #efaec1);
    clip-path: polygon(0 0, 50% 63%, 100% 0, 100% 100%, 0 100%);
}

.envelope-flap {
    position: absolute;
    z-index: 4;
    inset: 0 0 auto;
    height: 72%;
    border-radius: 0.8rem 0.8rem 0 0;
    background: linear-gradient(145deg, #f8d5df, #efb2c4);
    clip-path: polygon(0 0, 100% 0, 50% 100%);
    transform: rotateX(0);
    transform-origin: top;
    transition: transform 2s cubic-bezier(0.2, 0.75, 0.25, 1);
    backface-visibility: hidden;
}

.envelope-seal {
    position: absolute;
    z-index: 5;
    top: 51%;
    left: 50%;
    display: grid;
    width: 3rem;
    height: 3rem;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 72%);
    border-radius: 50%;
    background: linear-gradient(145deg, #df7897, #cb587b);
    color: #fff;
    font-size: 1.25rem;
    box-shadow: 0 6px 16px rgb(150 59 88 / 24%);
    transform: translate(-50%, -50%);
    transition:
        opacity 280ms ease,
        transform 280ms ease;
}

.envelope-open .envelope-flap {
    transform: rotateX(-180deg);
}

.envelope-open .envelope-letter {
    transform: translateY(-42%);
}

.envelope-open .envelope-seal {
    transform: translate(-50%, -50%) scale(0.65);
    opacity: 0;
}
</style>
