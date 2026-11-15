<script setup lang="ts">
import FloatingHearts from '@/components/anniversary/FloatingHearts.vue';
</script>

<template>
    <div class="anniversary-theme min-h-screen">
        <FloatingHearts />
        <div class="relative z-10 flex min-h-screen flex-col">
            <header
                class="mx-auto flex w-full max-w-6xl items-center justify-between px-5 py-5 sm:px-8 sm:py-7"
            >
                <p
                    class="flex items-center gap-2 text-xs font-semibold tracking-[0.22em] text-[#895c6c] uppercase"
                >
                    <span class="text-base text-[#d65c88]" aria-hidden="true"
                        >♡</span
                    >
                    A Journey to Us
                </p>
                <span
                    class="hidden text-xs tracking-wide text-[#ad8491] sm:inline"
                >
                    Five months, infinite memories
                </span>
            </header>

            <main
                class="mx-auto flex w-full max-w-6xl flex-1 flex-col justify-center px-5 pb-10 sm:px-8 sm:pb-14"
            >
                <slot />
            </main>

            <footer
                class="relative z-10 px-5 pb-6 text-center text-[0.68rem] tracking-[0.16em] text-[#ad8491] uppercase sm:pb-7"
            >
                made with all my love
                <span class="text-[#d65c88]" aria-hidden="true">♡</span>
            </footer>
        </div>
    </div>
</template>
