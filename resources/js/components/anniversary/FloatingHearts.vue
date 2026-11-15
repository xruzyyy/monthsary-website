<script setup lang="ts">
const hearts = [
    { left: '4%', delay: '-3s', duration: '16s', size: '1rem', opacity: 0.3 },
    {
        left: '16%',
        delay: '-11s',
        duration: '18s',
        size: '0.8rem',
        opacity: 0.25,
    },
    {
        left: '31%',
        delay: '-7s',
        duration: '15s',
        size: '1.15rem',
        opacity: 0.32,
    },
    {
        left: '48%',
        delay: '-14s',
        duration: '19s',
        size: '0.75rem',
        opacity: 0.25,
    },
    { left: '63%', delay: '-5s', duration: '17s', size: '1rem', opacity: 0.3 },
    {
        left: '79%',
        delay: '-9s',
        duration: '20s',
        size: '0.85rem',
        opacity: 0.26,
    },
    {
        left: '93%',
        delay: '-1s',
        duration: '16s',
        size: '1.1rem',
        opacity: 0.32,
    },
];
</script>

<template>
    <div
        class="pointer-events-none absolute inset-0 z-0 overflow-hidden"
        aria-hidden="true"
    >
        <span
            v-for="(heart, index) in hearts"
            :key="index"
            class="anniversary-floating-heart"
            :style="{
                left: heart.left,
                animationDelay: heart.delay,
                animationDuration: heart.duration,
                fontSize: heart.size,
                opacity: heart.opacity,
            }"
        >
            ♥
        </span>
    </div>
</template>
