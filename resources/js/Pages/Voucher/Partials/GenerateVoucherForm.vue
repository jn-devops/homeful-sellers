<script setup>

import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from "@/Components/Modal.vue";
import { ref, watch } from "vue";

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
    contact_reference_code: '',
    project_code: '',
});

const showingVoucherCode = ref(false);
const passwordInput = ref(null);

const showVoucherCode = () => {
    showingVoucherCode.value = true;
};

const closeModal = () => {
    showingVoucherCode.value = false;
};

const voucher = ref({});

watch (
    () => usePage().props.flash.event,
    (event) => {
        switch (event?.name) {
            case 'voucher_generated':
                console.log('event:', event?.data);
                voucher.value = event?.data;
                console.log('voucher:', voucher);
                showVoucherCode();
                break;
        }
    },
    { immediate: true }
);

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Inputs
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Generate voucher code for the buyer to input upon checkout in Homeful Shop.
            </p>
        </header>

        <form
            @submit.prevent="form.post(route('voucher.store'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="contact_reference_code" value="Homeful Id" />

                <TextInput
                    id="contact_reference_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.contact_reference_code"
                    required
                    autofocus
                    autocomplete="contact_reference_code"
                />

                <div class="text-xs text-gray-600 dark:text-gray-400">{{ voucher?.contact?.name }} [ {{ voucher?.contact?.email }}, {{ voucher?.contact?.mobile }} ] </div>
                <InputError class="mt-2" :message="form.errors.contact_reference_code" />
            </div>

            <div>
                <InputLabel for="project_code" value="Project Code" />

                <TextInput
                    id="project_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.project_code"
                />

                <InputError class="mt-2" :message="form.errors.project_code" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Generate</PrimaryButton>

                <div v-if="voucher.code && voucher.code !== ''">
                    <SecondaryButton @click="showVoucherCode">
                        Show
                    </SecondaryButton>
                </div>

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
                        Generated.
                    </p>
                </Transition>
            </div>
        </form>
        <Modal :show="showingVoucherCode" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    Generated Voucher Code
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Copy the code below and send to {{ voucher.contact?.name }} upon checking out in Homeful Shop.
                </p>

                <h1
                    class="mt-6 text-6xl font-extrabold text-center text-gray-900 dark:text-gray-100"
                >
                    {{ voucher.code }}
                </h1>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        Close
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
