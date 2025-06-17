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
    first_name:'',
    last_name:'',
    email:'',
    mobile:'',
    date_of_birth:'',
    gross_monthly_income:'',
    cobo_gross_monthly_income:'',
    cobo_date_of_birth:''
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

        <form
            @submit.prevent="form.post(route('voucher.store'))"
            class="space-y-6"
        >   
            <div>
                <h3 class="fw-bold">Buyer Info</h3>
                <div>
                <GradientStyleInput 
                    label="First Name"
                    required
                    v-model="form.first_name"
                    placeholder="Enter First Name"
                    :error="form.errors.first_name"
                />
            </div>
            <div>
                <GradientStyleInput 
                    label="Last Name"
                    required
                    v-model="form.last_name"
                    placeholder="Enter Last Name"
                    :error="form.errors.last_name"
                />
            </div>
            <div>
                <GradientStyleInput 
                    label="Email"
                    required
                    v-model="form.email"
                    placeholder="Enter email"
                    :error="form.errors.email"
                />
            </div>
            <div>
                <GradientStyleInput 
                    label="Mobile No"
                    required
                    v-model="form.mobile"
                    placeholder="+639XXXXXXXXX"
                    :error="form.errors.mobile"
                />
            </div>
            <div>
                <GradientStyleInput 
                    label="Birthdate"
                    required
                    v-model="form.date_of_birth"
                    placeholder="Enter Birthdate"
                    :error="form.errors.date_of_birth"
                />
            </div>
            <div>
                <GradientStyleInput 
                    label="Gross Monthly Income"
                    required
                    v-model="form.gross_monthly_income"
                    placeholder="Enter GMI"
                    :error="form.errors.gross_monthly_income"
                />
            </div>
            <div> 
                <GradientStyleInput 
                    label="Coborrower Gross Monthly Income"
                    v-model="form.cobo_gross_monthly_income"
                    placeholder="Enter GMI(Optional)"
                    :error="form.errors.cobo_gross_monthly_income"
                />
            </div>
            <div> 
                <GradientStyleInput 
                    label="Coborrower Birthdate"
                    v-model="form.cobo_date_of_birth"
                    placeholder="Enter Birthdate(Optional)"
                    :error="form.errors.cobo_date_of_birth"
                />
            </div>

        </div>
        <hr>
            <div>
                <GradientSelectCombobox 
                    label="Projects"
                    :options="projects"
                    v-model="form.project_code"
                    :error-message="form.errors.project_code"
                />
            </div>

            <div class="flex flex-col items-center">
                <PlainBlackButton type="submit" :disabled="form.processing">Reserve</PlainBlackButton>

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
