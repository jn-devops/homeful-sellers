<script setup>

import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from "@/Components/SelectComboboxes.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from "@/Components/Modal.vue";
import { ref, watch } from "vue";
import GradientStyleInput from '@/Components/Inputs/GradientStyleInput.vue';
import GradientSelectCombobox from '@/Components/Inputs/GradientSelectCombobox.vue';
import PlainBlackButton from '@/Components/Buttons/PlainBlackButton.vue';
import DefaultModal from '@/Components/DefaultModal.vue';
import SecondaryPlainBlack from '@/Components/Buttons/SecondaryPlainBlack.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const projects = usePage().props.projects.map(item => ({
   id: item.code,
   name: item.name
}));

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
const submit = async () => {
console.log(JSON.stringify(form.data()))
    try {
        const res_arr = {
            user:user,
            contact_reference_code: "H-2G66RH",
            project_code: "PAGSIBOL VILLAGE MAGALANG PAMPANGA" 
        };
        console.log(res_arr);
        const res_voucher = await fetch(route('api.voucher.generate'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
                 },
            body: JSON.stringify(res_arr),
        });
        // console.log(res_voucher.json());
        const res_vouchers = await res_voucher.json(); 
        console.log(res_vouchers);
        // const res_update = await fetch(route('api.buyer.update'), {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //         'Accept': 'application/json',
        //     },
        //     body: JSON.stringify({
        //         contact_reference_code: "H-C47YHH",
        //         match_link: "https://contracts-staging.homeful.ph/avail/create?reference=JN-XYVRQQ&voucher=LH-5FDKYG"
        //     }),
        // });
        // console.log(res_update);
        alert('Registration successful!');
        // window.location.href = '/dashboard';

    } catch (error) {
        console.error('Network or server error:', error);
        alert('An error occurred during submission.');
    }
};
</script>

<template>
    <section>

        <form
            @submit.prevent="submit"
            class="space-y-6"
        >
            <div>
                <GradientStyleInput 
                    label="Homeful ID"
                    required
                    v-model="form.contact_reference_code"
                    placeholder="Enter Homeful ID"
                    :error="form.errors.contact_reference_code"
                />
            </div>

            <div>
                <GradientSelectCombobox 
                    label="Projects"
                    :options="projects"
                    v-model="form.project_code"
                    :error-message="form.errors.project_code"
                />
            </div>

            <div class="flex flex-col items-center">
                <PlainBlackButton type="submit" :disabled="form.processing">Generate</PlainBlackButton>

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
                        Generated
                    </p>
                </Transition>

                <div v-if="voucher.code && voucher.code !== ''" class="mt-2 w-full">
                    <SecondaryPlainBlack class="w-full" @click="showVoucherCode">
                        <div class="text-sm w-full">
                            Show Generated Code
                        </div>
                    </SecondaryPlainBlack>
                </div>

            </div>
        </form>
        <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
        >
            <DefaultModal v-if="showingVoucherCode" @close="closeModal">
                <div class="p-6">
                    <h2
                        class="text-lg font-medium text-gray-900 "
                    >
                        Generated Voucher Code
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        Copy the code below and send to {{ voucher.contact?.name }} upon checking out in Homeful Shop.
                    </p>

                    <h1
                        class="mt-6 text-6xl font-extrabold text-center text-gray-900"
                    >
                        {{ voucher.code }}
                    </h1>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="closeModal">
                            Close
                        </SecondaryButton>
                    </div>
                </div>
            </DefaultModal>
        </Transition>
    </section>
</template>
