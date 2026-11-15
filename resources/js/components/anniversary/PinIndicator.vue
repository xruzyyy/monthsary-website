<script setup lang="ts">
const props = defineProps<{
    value: string;
    length: number;
}>();

const emit = defineEmits<{
    input: [value: string];
}>();

function updatePin(event: Event): void {
    const input = event.target as HTMLInputElement;
    const pin = input.value.replace(/\D/g, '').slice(0, props.length);

    input.value = pin;
    emit('input', pin);
}
</script>

<template>
    <div
        class="relative flex items-center justify-center gap-4 rounded-full focus-within:ring-2 focus-within:ring-[#d65c88]/40 focus-within:ring-offset-4"
        role="group"
        aria-label="Monthsary PIN"
    >
        <input
            type="text"
            inputmode="numeric"
            autocomplete="one-time-code"
            pattern="[0-9]*"
            :maxlength="length"
            aria-label="Type your monthsary PIN"
            class="absolute inset-0 z-10 h-full w-full cursor-text opacity-0"
            :value="value"
            @input="updatePin"
        />
        <span
            v-for="position in length"
            :key="position"
            class="pointer-events-none flex size-3.5 items-center justify-center rounded-full border transition-all duration-300"
            :class="
                value.length >= position
                    ? 'scale-110 border-[#d65c88] bg-[#d65c88] shadow-[0_0_0_5px_rgb(214_92_136_/_12%)]'
                    : 'border-[#e8c9d3] bg-white/80'
            "
            aria-hidden="true"
        />
        <span class="sr-only" aria-live="polite"
            >{{ value.length }} of {{ length }} digits entered</span
        >
    </div>
</template>
