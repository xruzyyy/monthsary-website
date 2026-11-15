<script setup lang="ts">
const emit = defineEmits<{
    digit: [value: string];
    clear: [];
    backspace: [];
}>();

const digits = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
</script>

<template>
    <div
        class="grid grid-cols-3 gap-2.5 sm:gap-3"
        role="group"
        aria-label="Numeric keypad"
    >
        <button
            v-for="digit in digits"
            :key="digit"
            type="button"
            class="anniversary-key"
            :aria-label="digit"
            @click="emit('digit', digit)"
        >
            {{ digit }}
        </button>
        <button
            type="button"
            class="anniversary-key anniversary-key-utility"
            @click="emit('clear')"
        >
            Clear
        </button>
        <button
            type="button"
            class="anniversary-key"
            aria-label="0"
            @click="emit('digit', '0')"
        >
            0
        </button>
        <button
            type="button"
            class="anniversary-key anniversary-key-utility"
            aria-label="Backspace"
            @click="emit('backspace')"
        >
            ⌫
        </button>
    </div>
</template>

<style scoped>
.anniversary-key {
    display: flex;
    min-height: 3.45rem;
    align-items: center;
    justify-content: center;
    border: 1px solid rgb(214 92 136 / 10%);
    border-radius: 1rem;
    background: rgb(255 255 255 / 86%);
    color: #754456;
    font-size: 1.08rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 3px 10px rgb(117 68 86 / 4%);
    touch-action: manipulation;
    transition:
        transform 180ms ease,
        background-color 180ms ease,
        box-shadow 180ms ease;
}

.anniversary-key:hover {
    transform: translateY(-1px);
    background: #fff;
    box-shadow: 0 7px 16px rgb(117 68 86 / 9%);
}

.anniversary-key:active {
    transform: scale(0.96);
    background: #fff0f4;
}

.anniversary-key-utility {
    color: #a67b89;
    font-size: 0.78rem;
    font-weight: 500;
}
</style>
