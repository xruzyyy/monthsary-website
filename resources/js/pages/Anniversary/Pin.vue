<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import PinIndicator from '@/components/anniversary/PinIndicator.vue';
import PinKeypad from '@/components/anniversary/PinKeypad.vue';
import PrimaryButton from '@/components/anniversary/PrimaryButton.vue';
import { anniversary } from '@/routes';
import { unlock } from '@/routes/anniversary';

const props = defineProps<{
    isConfigured: boolean;
}>();

const PIN_LENGTH = 6;
const form = useForm({ pin: '' });
const canSubmit = computed(
    () =>
        props.isConfigured &&
        form.pin.length === PIN_LENGTH &&
        !form.processing,
);

function addDigit(digit: string): void {
    if (form.pin.length >= PIN_LENGTH || form.processing) {
        return;
    }

    form.pin += digit;
    form.clearErrors('pin');
}

function updatePin(pin: string): void {
    if (form.processing) {
        return;
    }

    form.pin = pin;
    form.clearErrors('pin');
}

function clearPin(): void {
    form.pin = '';
    form.clearErrors('pin');
}

function removeLastDigit(): void {
    form.pin = form.pin.slice(0, -1);
    form.clearErrors('pin');
}

function submitPin(): void {
    if (!canSubmit.value) {
        return;
    }

    form.post(unlock.url(), {
        preserveScroll: true,
        onError: () => {
            form.pin = '';
        },
    });
}
</script>

<template>
    <Head title="Our Little Secret" />

    <section
        class="anniversary-enter mx-auto grid w-full max-w-md justify-items-center gap-7 py-6 sm:py-10"
    >
        <Link
            :href="anniversary()"
            class="anniversary-link text-sm text-[#a27685] transition hover:text-[#d65c88]"
        >
            <span aria-hidden="true">←</span> Back to our beginning
        </Link>

        <div class="anniversary-card w-full rounded-[2rem] p-6 sm:p-9">
            <div class="mb-7 grid justify-items-center gap-3 text-center">
                <span
                    class="grid size-12 place-items-center rounded-full bg-[#fbe4eb] text-xl text-[#d65c88]"
                    aria-hidden="true"
                    >♡</span
                >
                <p
                    class="text-[0.65rem] font-semibold tracking-[0.22em] text-[#aa7184] uppercase"
                >
                    a little secret between us
                </p>
                <h1
                    class="anniversary-display text-3xl text-[#754456] sm:text-4xl"
                >
                    Enter our special date
                </h1>
                <p class="text-sm leading-6 text-[#997581]">
                    Six little numbers, then our story begins.
                </p>
            </div>

            <form
                class="mx-auto grid max-w-[19rem] gap-5"
                @submit.prevent="submitPin"
            >
                <PinIndicator
                    :value="form.pin"
                    :length="PIN_LENGTH"
                    @input="updatePin"
                />

                <PinKeypad
                    @digit="addDigit"
                    @clear="clearPin"
                    @backspace="removeLastDigit"
                />

                <p
                    v-if="form.errors.pin"
                    class="min-h-5 text-center text-sm text-[#bd4d70]"
                    role="alert"
                >
                    {{ form.errors.pin }}
                </p>
                <p
                    v-else-if="!props.isConfigured"
                    class="text-center text-sm leading-6 text-[#9c7180]"
                    role="status"
                >
                    This surprise is being prepared. Come back soon, my love.
                </p>
                <p
                    v-else
                    class="min-h-5 text-center text-sm text-[#997581]"
                    aria-live="polite"
                >
                    {{
                        form.processing
                            ? 'Checking our little secret…'
                            : 'Guess the six little numbers that unlock our story baby mochi.'
                    }}
                </p>

                <PrimaryButton type="submit" :disabled="!canSubmit">
                    {{ form.processing ? 'One moment…' : 'Unlock Our Journey' }}
                    <span aria-hidden="true">♡</span>
                </PrimaryButton>
            </form>
        </div>
    </section>
</template>
