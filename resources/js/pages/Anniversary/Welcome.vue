<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { pin } from '@/routes/anniversary';
</script>

<template>
    <Head title="A Journey to Us" />

    <section
        class="anniversary-enter relative mx-auto grid w-full max-w-4xl justify-items-center gap-7 py-8 text-center sm:gap-9 sm:py-14"
    >
        <p
            class="rounded-full border border-white/85 bg-white/60 px-4 py-2 text-[0.66rem] font-semibold tracking-[0.23em] text-[#aa687f] uppercase shadow-sm"
        >
            A special day for us
        </p>

        <div
            class="relative grid size-36 place-items-center sm:size-44"
            aria-hidden="true"
        >
            <span
                class="absolute inset-1 rounded-full border border-[#e8b5c5]/55"
            />
            <span
                class="absolute inset-4 rounded-full border border-dashed border-[#e8b5c5]/50"
            />
            <span class="absolute top-7 right-3 text-xl text-[#d995aa]">✧</span>
            <span class="absolute bottom-5 left-2 text-sm text-[#d995aa]"
                >✦</span
            >
            <span
                class="anniversary-heartbeat text-7xl leading-none text-[#d65c88] drop-shadow-[0_10px_20px_rgb(214_92_136_/_22%)] sm:text-8xl"
                >♥</span
            >
        </div>

        <div class="grid justify-items-center gap-4">
            <span
                class="rounded-full border border-[#edccda] bg-white/65 px-4 py-1.5 text-[0.68rem] font-semibold tracking-[0.2em] text-[#a65f78] uppercase"
            >
                05 months together
            </span>
            <h1
                class="anniversary-display max-w-3xl text-5xl leading-[1.06] text-[#754456] sm:text-7xl"
            >
                A Journey to Us
            </h1>
            <p
                class="max-w-xl text-base leading-7 text-[#916c7a] sm:text-lg sm:leading-8"
            >
                Five months, countless memories, and a lifetime to go.
            </p>
        </div>

        <Link :href="pin()" class="anniversary-link anniversary-button mt-2">
            Go to the next step <span class="text-lg" aria-hidden="true">→</span>
            <span class="text-lg" aria-hidden="true">♡</span>
        </Link>
        <p class="text-xs tracking-wide text-[#ad8491]">
            a little experience made just for you
        </p>
    </section>
</template>
