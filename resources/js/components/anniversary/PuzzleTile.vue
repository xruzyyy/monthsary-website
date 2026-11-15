<script setup lang="ts">
import { computed, type CSSProperties } from 'vue';

const props = defineProps<{
    tile: number;
    photoUrl: string;
    photoAspectRatio: number;
    disabled: boolean;
}>();

const emit = defineEmits<{
    select: [];
}>();

const imageStyle = computed<CSSProperties>(() => {
    if (props.tile === 0) {
        return {
            aspectRatio: props.photoAspectRatio.toString(),
        };
    }

    const sourcePosition = props.tile - 1;

    return {
        aspectRatio: props.photoAspectRatio.toString(),
        backgroundImage: `url("${props.photoUrl}")`,
        backgroundSize: '300% 300%',
        backgroundPosition: `${(sourcePosition % 3) * 50}% ${Math.floor(sourcePosition / 3) * 50}%`,
    };
});
</script>

<template>
    <button
        v-if="tile !== 0"
        type="button"
        class="aspect-auto overflow-hidden rounded-xl border border-white/75 bg-[#f3d9e2] bg-cover shadow-[0_5px_14px_rgb(117_68_86_/_10%)] disabled:cursor-default"
        :style="imageStyle"
        :disabled="disabled"
        :aria-label="`Move tile ${tile}`"
        @click="emit('select')"
    >
        <span class="sr-only">Tile {{ tile }}</span>
    </button>
    <div
        v-else
        class="aspect-auto rounded-xl border border-dashed border-[#e7c4d0] bg-[#fbedf1]/85"
        aria-label="Empty space"
        role="img"
        :style="imageStyle"
    />
</template>
