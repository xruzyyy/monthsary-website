<script setup lang="ts">
interface Props {
    type?: 'button' | 'submit';
    disabled?: boolean;
    variant?: 'solid' | 'soft';
}

withDefaults(defineProps<Props>(), {
    type: 'button',
    disabled: false,
    variant: 'solid',
});
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'anniversary-button',
            { 'anniversary-button-soft': variant === 'soft' },
        ]"
    >
        <slot />
    </button>
</template>
