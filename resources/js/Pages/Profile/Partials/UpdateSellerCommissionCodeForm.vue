<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    seller_commission_code: user.seller_commission_code
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Seller Profile
            </h2>

        </header>

        <form
            @submit.prevent="form.post(route('profile.store'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="seller_commission_code" value="Seller Commission Code" />
                <TextInput
                    id="seller_commission_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.seller_commission_code"
                    required
                    autofocus
                    disabled
                />

                <InputError class="mt-2" :message="form.errors.seller_commission_code" />
            </div>

            <div class="flex items-center gap-4 hidden">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
